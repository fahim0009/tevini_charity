<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Usertransaction;
use Illuminate\Http\Request;

class GiftAidController extends Controller
{
    public function currentYearTransaction($type = null)
    {
        $userId = auth()->id();

        $currentTaxYearStart = now()->month >= 4 ? now()->month(4)->startOfMonth() : now()->subYear()->month(4)->startOfMonth();
        $lastTaxYearStart = $currentTaxYearStart->copy()->subYear();

        if ($type === 'prev-year') {

            $giftAid = Usertransaction::where('user_id', $userId)
                ->whereBetween('created_at', [$lastTaxYearStart, $currentTaxYearStart->copy()->subDay()])
                ->where('status', 1)
                ->where(function($query) {
                    $query->whereNotNull('gift')
                        ->orWhere('clear_gift', 1);
                })
                ->orderByDesc('id')
                ->get();

        }else{
            $giftAid = Usertransaction::where('user_id', $userId)
                ->whereBetween('created_at', [$currentTaxYearStart, now()])
                ->where('status', 1)
                ->where(function($query) {
                    $query->whereNotNull('gift')
                        ->orWhere('clear_gift', 1);
                })
                ->orderByDesc('id')
                ->get();

        }


        return view('frontend.user.gift_aid_transaction', compact(
            'giftAid'
        ));
    }
}
