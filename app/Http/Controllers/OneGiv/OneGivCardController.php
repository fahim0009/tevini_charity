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
                            ? 'required|numeric|min:1'
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

            // ✅ New format check
            $orderNumber  = $result['orderNumber'] ?? 0;
            $validOrders  = $result['validCardOrders'] ?? [];
            $errorOrders  = $result['errorCardOrders'] ?? [];

            
            if ($orderNumber == 0 || empty($validOrders)) {
                $errorMsg = !empty($errorOrders)
                    ? $errorOrders[0]['errors']
                    : 'Card order failed. Please try again.';

                return back()->with('error', $errorMsg);
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

        $allcards = OneGivCard::latest()
                           ->get();


        return view('frontend.user.onegiv.my-cards', compact('cards','allcards'));
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
}