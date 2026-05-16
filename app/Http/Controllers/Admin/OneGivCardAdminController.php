<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\OneGiv\OneGivCardOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OneGivCardAdminController extends Controller
{
    protected $onegiv;

    public function __construct()
    {
        $this->onegiv = new \App\Services\OneGivService();
    }

    /**
     * Show Order Form for Admin
     */
    public function orderCardForm($userId)
    {
        $user = User::findOrFail($userId);
        $orders = OneGivCardOrder::where('user_id', $userId)
                          ->latest()
                          ->get();

        return view('admin.onegiv.order-card', compact('user', 'orders'));
    }

    /**
     * Store Card Order — Admin placing order for user
     */
    public function orderCardStore(Request $request, $userId)
    {
        $request->validate([
            'card_holder'  => 'required|string|max:100',
            'pin'          => 'required|digits:4',
            'fixed_amount' => 'required|in:0,1',
            'amount'       => $request->fixed_amount == '1'
                            ? 'required|numeric|min:0.5'
                            : 'nullable|numeric',
            'email'        => 'required|email',
            'mobile'       => 'required|string',
            'house_number' => 'required|string',
            'street'       => 'required|string',
            'address2'     => 'nullable|string',
            'city'         => 'required|string',
            'postcode'     => 'required|string',
            'country'      => 'required|string',
        ]);

        try {
            $user = User::findOrFail($userId);
            $adminId = Auth::id();
            $adminName = Auth::user()->name ?? 'Admin';

            // ✅ Step 1: Check available balance
            $cardOrderFee     = 5;
            $availableBalance = $user->getAvailableLimit();

            if ($availableBalance < $cardOrderFee) {
                return back()->with(
                    'error',
                    'Insufficient balance. User needs at least £' . number_format($cardOrderFee, 2) .
                    '. Available: £' . number_format($availableBalance, 2) . '.'
                );
            }

            // ✅ Step 2: Place the card order with OneGiv
            $cardId  = 'card-' . $user->id . '-' . uniqid();
            $isFixed = $request->fixed_amount == '1';

            $cardPayload = [
                'id'           => $cardId,
                'cardHolder'   => $request->card_holder,
                'fixedAmount'  => $isFixed,
                'pin'          => $request->pin,
                'emailAddress' => $request->email,
                'mobileNumber' => $request->mobile,
                'houseNumber'  => $request->house_number,
                'street'       => $request->street,
                'address2'     => $request->address2 ?? '',
                'city'         => $request->city,
                'postcode'     => $request->postcode,
                'country'      => $request->country,
            ];

            if ($isFixed) {
                $cardPayload['amount'] = (int) ($request->amount * 100);
            }

            // ✅ Pass options so Service saves with correct Donor ID & Admin flags
            $result = $this->onegiv->orderCards([$cardPayload], [
                'user_id'         => $user->id,
                'is_admin_order'  => true,
                'admin_id'        => $adminId,
            ]);

            Log::info('Admin OneGiv Card Order Result', [
                'admin_id' => $adminId,
                'user_id'  => $user->id,
                'response' => $result
            ]);

            $orderNumber = $result['orderNumber'] ?? 0;
            $validOrders = $result['validCardOrders'] ?? [];
            $errorOrders = $result['errorCardOrders'] ?? [];

            if ($orderNumber == 0 || empty($validOrders)) {
                $errorMsg = !empty($errorOrders)
                    ? $errorOrders[0]['errors']
                    : 'Card order failed. Please try again.';

                return back()->with('error', $errorMsg);
            }

            // ❌ REMOVED: Manual OneGivCardOrder save (Service handles this now!)

            // ✅ Step 3: Deduct £5 from donor balance
            $utran                        = new Usertransaction();
            $utran->t_id                  = time() . '-' . $user->id;
            $utran->user_id               = $user->id;
            $utran->t_type                = 'Out';
            $utran->source                = 'OneGiv Card (Admin Ordered)';
            $utran->amount                = $cardOrderFee;
            $utran->title                 = 'OneGiv Card Order Fee (Order #' . $orderNumber . ') - Ordered by Admin';
            $utran->status                = 1;
            $utran->save();

            Log::info('Admin OneGiv Card Order Fee Deducted', [
                'admin_id'     => $adminId,
                'user_id'      => $user->id,
                'amount'       => $cardOrderFee,
                'order_number' => $orderNumber,
            ]);

            // ✅ Step 4: Send confirmation email
            try {
                $contactmail = \App\Models\ContactMail::where('id', 1)->first()->name ?? config('mail.admin_address');

                $emailData = [
                    'name'              => $user->name . ' ' . $user->surname,
                    'order_number'      => $orderNumber,
                    'card_holder'       => $request->card_holder,
                    'card_type'         => $isFixed ? 'Fixed Amount' : 'Variable Amount',
                    'amount'            => $isFixed ? number_format($request->amount, 2) : 0,
                    'order_date'        => now()->format('d M Y, H:i'),
                    'ordered_by_admin'  => true,
                    'admin_name'        => $adminName,
                ];

                Mail::to($user->email)
                    ->cc($contactmail)
                    ->send(new \App\Mail\OneGivCardOrder($emailData));

            } catch (\Exception $mailException) {
                Log::error('Admin OneGiv Card Order Email Failed', [
                    'admin_id' => $adminId,
                    'user_id'  => $user->id,
                    'error'    => $mailException->getMessage(),
                ]);
            }

            return redirect()
                ->route('donor.profile', $userId)
                ->with('success', 'Card order placed successfully for ' . $user->name . '! Order #' . $orderNumber);

        } catch (\Exception $e) {
            Log::error('Admin OneGiv orderCardStore error', [
                'admin_id' => Auth::id(),
                'user_id'  => $userId,
                'error'    => $e->getMessage()
            ]);
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    /**
     * View User's Card Orders
     */
    public function viewUserOrders($userId)
    {
        $user = User::findOrFail($userId);
        $orders = OneGivCardOrder::where('user_id', $userId)
                          ->latest()
                          ->paginate(10);

        return view('admin.onegiv.user-orders', compact('user', 'orders'));
    }
}