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
        return view('frontend.user.onegiv.order-card');
    }

    /**
     *  Order  — OneGiv API 
     */
    public function orderCardStore(Request $request)
    {
        $request->validate([
            'card_holder'  => 'required|string|max:100',
            'amount'       => 'nullable|numeric',
            'pin'          => 'required|digits:4',
            'fixed_amount' => 'required|boolean',
        ]);

        try {
            $cardId = 'card-' . Auth::id() . '-' . uniqid();

            $result = $this->onegiv->orderCards([
                [
                    'id'          => $cardId,
                    'cardHolder'  => $request->card_holder,
                    'fixedAmount' => (bool) $request->fixed_amount,
                    'amount'      => (int) ($request->amount * 100), // pound → pennies
                    'pin'         => $request->pin,
                ]
            ]);

            Log::info('OneGiv Card Order Result', ['response' => $result]);

            return redirect()
                ->route('onegiv.mycards')
                ->with('success', 'Card order placed! Order #' . ($result['orderNumber'] ?? ''));

        } catch (\Exception $e) {
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


        return view('frontend.user.onegiv.my-cards', compact('cards'));
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