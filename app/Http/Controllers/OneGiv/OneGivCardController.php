<?php

namespace App\Http\Controllers\OneGiv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OneGivService;
use App\Models\OneGiv\OneGivCard;
use App\Models\OneGiv\OneGivCardOrder;
use App\Models\OneGiv\OneGivTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OneGivCardController extends Controller
{
    protected OneGivService $onegiv;

    public function __construct(OneGivService $onegiv)
    {
        $this->onegiv = $onegiv;
    }

    /**
     *  Order Form
     */
    public function orderCardForm()
    {
        $orders = OneGivCardOrder::where('user_id', Auth::id())
                          ->latest()
                          ->get();
        return view('frontend.user.onegiv.order-card', compact('orders'));
    }

    /**
     *  Order  — OneGiv API 
     */
    public function orderCardStore(Request $request)
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
            $user = \App\Models\User::find(Auth::id());

            // ✅ Step 1: Check available balance (£5 card order fee)
            $cardOrderFee     = 5; // £5
            $availableBalance = $user->getAvailableLimit();

            if ($availableBalance < $cardOrderFee) {
                return back()->with(
                    'error',
                    'Insufficient balance. You need at least £' . number_format($cardOrderFee, 2) .
                    ' to order a card. Your available balance is £' . number_format($availableBalance, 2) . '.'
                );
            }

            // ✅ Step 2: Place the card order with OneGiv
            $cardId  = 'card-' . Auth::id() . '-' . uniqid();
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

            $result = $this->onegiv->orderCards([$cardPayload]);

            Log::info('OneGiv Card Order Result', ['response' => $result]);

            $orderNumber = $result['orderNumber'] ?? 0;
            $validOrders = $result['validCardOrders'] ?? [];
            $errorOrders = $result['errorCardOrders'] ?? [];

            if ($orderNumber == 0 || empty($validOrders)) {
                $errorMsg = !empty($errorOrders)
                    ? $errorOrders[0]['errors']
                    : 'Card order failed. Please try again.';

                return back()->with('error', $errorMsg);
            }

            // ✅ Step 3: Deduct £5 from donor balance via user transaction
            $utran                        = new \App\Models\Usertransaction();
            $utran->t_id                  = time() . '-' . $user->id;
            $utran->user_id               = $user->id;
            $utran->t_type                = 'Out';
            $utran->source                = 'OneGiv Card';
            $utran->amount                = $cardOrderFee;
            $utran->title                 = 'OneGiv Card Order Fee (Order #' . $orderNumber . ')';
            $utran->status                = 1;
            $utran->save();

            Log::info('OneGiv Card Order Fee Deducted', [
                'user_id'      => $user->id,
                'amount'       => $cardOrderFee,
                'order_number' => $orderNumber,
            ]);

            // ✅ Step 4: Send confirmation email to donor and admin
            try {
                $contactmail = \App\Models\ContactMail::where('id', 1)->first()->name;

                $emailData = [
                    'name'         => $user->name . ' ' . $user->surname,
                    'order_number' => $orderNumber,
                    'card_holder'  => $request->card_holder,
                    'card_type'    => $isFixed ? 'Fixed Amount' : 'Variable Amount',
                    'amount'       => $isFixed ? number_format($request->amount, 2) : 0,
                    'order_date'   => now()->format('d M Y, H:i'),
                ];

                // Donor email
                Mail::to($user->email)
                    ->cc($contactmail)
                    ->send(new \App\Mail\OneGivCardOrder($emailData));

                Log::info('OneGiv Card Order Email Sent', [
                    'user_id'      => $user->id,
                    'email'        => $user->email,
                    'order_number' => $orderNumber,
                ]);

            } catch (\Exception $mailException) {
                Log::error('OneGiv Card Order Email Failed', [
                    'user_id' => $user->id,
                    'error'   => $mailException->getMessage(),
                ]);
            }

            return redirect()
                ->route('onegiv.mycards')
                ->with('success', 'Card order placed successfully! Order #' . $orderNumber);

        } catch (\Exception $e) {
            Log::error('OneGiv orderCardStore error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    /**
     * User Cards 
     */
    public function myCards()
    {
        $cards = OneGivCard::where('user_id', Auth::id())
                           ->latest()
                           ->get();

        $checkOrders = OneGivCardOrder::where('user_id', Auth::id())
                                ->latest()
                                ->get();

        return view('frontend.user.onegiv.my-cards', compact('cards', 'checkOrders'));
    }

    /**
     * PIN Change Form
     */
    public function changePinForm($serial)
    {
        $card = OneGivCard::where('serial_number', $serial)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        return view('frontend.user.onegiv.change-pin', compact('card'));
    }

    /**
     * PIN Change 
     */
    public function changePinStore(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
            'pin'           => 'required|digits:4',
            'pin_confirm'   => 'required|same:pin',
        ]);

        $card = OneGivCard::where('serial_number', $request->serial_number)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        try {
            $this->onegiv->changePin($request->serial_number, $request->pin);

            return redirect()
                ->route('onegiv.mycards')
                ->with('success', 'PIN changed successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'PIN change failed: ' . $e->getMessage());
        }
    }

    /**
     * Transactions 
     */
    public function transactions()
    {
        $cards = OneGivCard::where('user_id', Auth::id())
                           ->pluck('serial_number');

        $transactions = OneGivTransaction::whereIn('card_serial_number', $cards)
                                         ->latest()
                                         ->paginate(20);

        return view('frontend.user.onegiv.transactions', compact('transactions'));
    }


    public function assignCard($id)
    {
        $card = OneGivCard::findOrFail($id);

        if ($card->user_id) {
            return redirect()->route('onegiv.mycards')
                            ->with('error', 'Card already has a user assigned! (User ID: ' . $card->user_id . ')');
        }

        $card->update(['user_id' => Auth::id()]);

        return redirect()->route('onegiv.mycards')
                        ->with('success', 'User ID assigned to card ' . $card->serial_number);
    }



}