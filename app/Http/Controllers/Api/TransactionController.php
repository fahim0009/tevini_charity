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

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

        $tamount = Usertransaction::where('user_id','=', auth()->user()->id)->where([
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
        ])->where('status','=', '1')->orderBy('id','DESC')->get();

        $alltransactions = Usertransaction::where([
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



        }else{

        $tamount = Usertransaction::where('user_id','=', auth()->user()->id)->where('status','=', '1')->orderBy('id','DESC')->get();

        $alltransactions = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->get();

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
        $success['outtransactions'] = $outtransactions;
        $success['pending_transactions'] = $pending_transactions;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }
}
