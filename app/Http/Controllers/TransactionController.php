<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\Charity;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Donation;
use App\Models\User;

class TransactionController extends Controller
{
    public function index(Request $request)
    {

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');



            $outtransactions = Transaction::where('t_type','=', 'Out')->where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
            ])->orderBy('id','DESC')->get();

            $intransactions = Transaction::where('t_type','=', 'In')->where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
            ])->orderBy('id','DESC')->get();
            $alltransactions = Transaction::orderBy('id','DESC')->where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
            ])->get();

        }else{

            $outtransactions = Transaction::where('t_type','=', 'Out')->orderBy('id','DESC')->get();
            $intransactions = Transaction::where('t_type','=', 'In')->orderBy('id','DESC')->get();
            $alltransactions = Transaction::orderBy('id','DESC')->get();

        }


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
                ['created_at', '<=', $toDate.' 23:59:59']
            ])->whereIn('status', ['1', '0'])->where('charity_id','=', $charityid)->orderBy('id','DESC')->get();

            $total = Provoucher::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['status', '=', '1']
            ])->where('charity_id','=', $charityid)->sum('amount');

        }elseif(empty($request->input('fromdate')) && empty($request->input('todate'))&& !empty($request->input('charityid'))){
            $charityid = $request->input('charityid');
            $charity = Charity::where('id','=',$charityid)->first();
            $remittance = Provoucher::where('charity_id','=', $charityid)->whereIn('status', ['1', '0'])->orderBy('id','DESC')->get();
            $total = Provoucher::where([
                ['charity_id','=', $charityid],
                ['status', '=', '1']
                ])->sum('amount');
            $fromDate = "";
            $toDate   = "";
        }else{
            $remittance = Provoucher::whereIn('status', ['1', '0'])->orderBy('id','DESC')->get();
            $total = Provoucher::where('status', '=', '1')->sum('amount');
            $charity = "";
            $fromDate = "";
            $toDate   = "";
        }
        return view('remittance.index',compact('remittance','total','fromDate','toDate','charity'));
    }

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
            ['pending','=', '0']
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
            ['pending','=', '0']
            ])->orderBy('id','DESC')->get();


        }else{

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

        }



        return view('frontend.user.transaction')
        ->with('alltransactions',$alltransactions)
        ->with('intransactions',$intransactions)
        ->with('tamount',$tamount)
        ->with('outtransactions',$outtransactions);
    }

    public function donorTransaction(Request $request, $id)
    {

        $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
        $user = User::find($id);
        $donor_id = $user->id;
        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['status','=', '1']
             ])->orwhere([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['pending','=', '0']
                 ])->orderBy('id','DESC')->get();

            $intransactions = Usertransaction::where([
            ['t_type','=', 'In'],
            ['user_id','=', $id],
            ['created_at', '>=', $fromDate],
            ['created_at', '<=', $toDate.' 23:59:59'],
            ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['user_id','=', $id],
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['status','=', '1']
            ])->orwhere([
                ['t_type','=', 'Out'],
                ['user_id','=', $id],
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['pending','=', '0']
                ])->orderBy('id','DESC')->get();

         }else{

             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['status','=', '1']
                 ])->orwhere([
                ['user_id','=', $id],
                ['pending','=', '0']
                ])->orderBy('id','DESC')->get();

                $intransactions = Usertransaction::where([
                    ['t_type','=', 'In'],
                    ['user_id','=', $id],
                    ['status','=', '1']
                ])->orderBy('id','DESC')->get();

                $outtransactions = Usertransaction::where([
                    ['t_type','=', 'Out'],
                    ['user_id','=', $id],
                    ['status','=', '1']
                ])->orwhere([
                    ['t_type','=', 'Out'],
                    ['user_id','=', $id],
                    ['pending','=', '0']
                    ])->orderBy('id','DESC')->get();


             $fromDate = "";
             $toDate   = "";
         }



        return view('donor.transaction')
        ->with('intransactions',$intransactions)
        ->with('outtransactions',$outtransactions)
        ->with('report',$report)
        ->with('fromDate',$fromDate)
        ->with('toDate',$toDate)
        ->with('user',$user)
        ->with('donor_id',$id)
        ->with('tamount',$tamount);

    }

    public function charityTransaction(Request $request, $id)
    {

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $outtransactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $intransactions = Transaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

        }else{

            $outtransactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $intransactions = Transaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

        }



        return view('charity.transaction')
        ->with('intransactions',$intransactions)
        ->with('id',$id)
        ->with('outtransactions',$outtransactions);
    }

}
