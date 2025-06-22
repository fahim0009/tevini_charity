<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\Charity;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Donation;
use App\Models\User;

class TransactionController extends Controller
{
    public function userTransactionShow(Request $request)
    {

        
        $userId = auth()->id();
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate') ? $request->input('toDate') . ' 23:59:59' : null;
        $hasDateRange = $fromDate && $toDate;

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

        $tamount = Usertransaction::where('user_id','=', auth()->user()->id)->where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
        ])->where('status','=', '1')->orderBy('id','DESC')->get();

        $alltransactions = Usertransaction::with(['charity:id,name'])->where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->get();

            
            // dd($alltransactions);

        $intransactions = Usertransaction::where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['t_type','=', 'In'],
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orderBy('id','DESC')->get();

        $outtransactions = Usertransaction::where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->get();

        $pending_transactions = Usertransaction::where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
        ])->orderBy('id','DESC')->get();


        $giftAid = Usertransaction::with('user')->where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->whereNotNull('gift')->orderby('id', 'DESC')->get();



        }else{

        $tamount = Usertransaction::where('user_id','=', auth()->user()->id)->where('status','=', '1')->orderBy('id','DESC')->get();

        
        $giftAid = Usertransaction::with('user')->where('user_id', auth()->user()->id)->where('status', 1)->whereNotNull('gift')->orderby('id', 'DESC')->get();

        // All transactions
        $alltransactions = Usertransaction::with([
            'standingDonation',
            'charity:id,name',
            'standingdonationDetail.standingDonation:id,charitynote,mynote',
            'donation:id,charitynote,mynote',
            'campaign:id,campaign_title'
        ])
        ->where('user_id', $userId)
        ->where(function ($query) use ($hasDateRange, $fromDate, $toDate) {
            $query->where('status', 1)
                ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]));
        })
        ->orWhere(function ($query) use ($userId, $hasDateRange, $fromDate, $toDate) {
            $query->where('user_id', $userId)
                ->where('pending', 1)
                ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]));
        })
        ->orderByDesc('id')
        ->get();

            // dd($alltransactions);

        $intransactions = Usertransaction::where([
            ['t_type','=', 'In'],
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orderBy('id','DESC')->get();

        $outtransactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->get();

        $pending_transactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
        ])->orderBy('id','DESC')->get();



        }

        
        $success['alltransactions'] = $alltransactions;
        $success['intransactions'] = $intransactions;
        $success['tamount'] = $tamount;
        $success['giftAid'] = $giftAid;
        $success['outtransactions'] = $outtransactions;
        $success['pending_transactions'] = $pending_transactions;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }
}
