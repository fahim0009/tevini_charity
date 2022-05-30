<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\Charity;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Donation;

class TransactionController extends Controller
{
    public function index()
    {
        $outtransactions = Transaction::where('t_type','=', 'Out')->get();
        $intransactions = Transaction::where('t_type','=', 'In')->get();
        $alltransactions = Transaction::orderBy('id','ASC')->get();

        return view('transaction.index')
        ->with('alltransactions',$alltransactions)
        ->with('outtransactions',$outtransactions)
        ->with('intransactions',$intransactions);
    }

    public function adminTransactionView()
    {
        return view('transaction.tranview');
    }

    public function remittance(Request $request)
    {


        if(!empty($request->input('fromdate')) && !empty($request->input('todate'))&& !empty($request->input('charityid'))){
            $fromDate = $request->input('fromdate');
            $toDate   = $request->input('todate');
            $charityid = $request->input('charityid');
            $charity = Charity::where('id','=',$charityid)->first();
            $remittance = Provoucher::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate],
            ])->where('charity_id','=', $charityid)->get();

            $total = Provoucher::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate],
            ])->where('charity_id','=', $charityid)->sum('amount');

        }elseif(empty($request->input('fromdate')) && empty($request->input('todate'))&& !empty($request->input('charityid'))){
            $charityid = $request->input('charityid');
            $charity = Charity::where('id','=',$charityid)->first();
            $remittance = Provoucher::where('charity_id','=', $charityid)->orderBy('id','DESC')->get();
            $total = Provoucher::where('charity_id','=', $charityid)->sum('amount');
            $fromDate = "";
            $toDate   = "";
        }else{
            $remittance = Provoucher::orderBy('id','DESC')->get();
            $total = Provoucher::sum('amount');
            $charity = "";
            $fromDate = "";
            $toDate   = "";
        }
        return view('remittance.index',compact('remittance','total','fromDate','toDate','charity'));
    }

    public function userTransactionShow()
    {
        $tamount = Usertransaction::where('user_id','=', auth()->user()->id)->where('status','=', '1')->orderBy('id','DESC')->get();
        $alltransactions = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
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
            ['pending','=', '0']
            ])->orderBy('id','DESC')->get();

        return view('frontend.user.transaction')
        ->with('alltransactions',$alltransactions)
        ->with('intransactions',$intransactions)
        ->with('tamount',$tamount)
        ->with('outtransactions',$outtransactions);
    }

    public function donorTransaction($id)
    {
        $intransactions = Usertransaction::where([
            ['t_type','=', 'In'],
            ['user_id','=', $id],
            ['status','=', '1']
        ])->get();

        $outtransactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', $id],
            ['status','=', '1']
        ])->get();

        return view('donor.transaction')
        ->with('intransactions',$intransactions)
        ->with('donor_id',$id)
        ->with('outtransactions',$outtransactions);
    }

    public function charityTransaction($id)
    {
        $intransactions = Usertransaction::where([
            ['t_type','=', 'In'],
            ['charity_id','=', $id],
            ['status','=', '1']
        ])->get();

        $outtransactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['charity_id','=', $id],
            ['status','=', '1']
        ])->get();

        return view('charity.transaction')
        ->with('intransactions',$intransactions)
        ->with('outtransactions',$outtransactions);
    }

}
