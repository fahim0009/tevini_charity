<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use App\Models\Voucher;
use App\Models\Charity;

class VoucherController extends Controller
{
       
    public function processed_Voucher_show()
    {
        $wvouchers = Provoucher::where([
            ['user_id','=', auth()->user()->id],
            ['waiting','=', 'Yes']
            ])->orderBy('id','DESC')->get();

        return view('frontend.user.waiting_voucher')
        ->with('wvouchers',$wvouchers);
    }

    public function waiting_CompleteBydonor(Request $request)
    {
     if(empty($request->voucher_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $charity_id = $request->charity_id;
        $voucher_id = $request->voucher_id;


        $voucher = Provoucher::where('id',$voucher_id)->first();
        
        $u_bal = User::where('id',$voucher->user_id)->first()->balance;
        $overdrawn = (User::where('id',$voucher->user_id)->first()->overdrawn_amount);
        $limitChk = $u_bal + $overdrawn;

        if($limitChk >= $voucher->amount){

            $utransaction = Usertransaction::find($voucher->tran_id);
            $utransaction->status = '1';
            $utransaction->pending = '1';
            $utransaction->save();

            $charity = Charity::find($voucher->charity_id);
            $charity->increment('balance',$voucher->amount);
            $charity->save();

            $donor = User::find($voucher->user_id);
            $donor->decrement('balance',$voucher->amount);
            $donor->save();


            $pstatus = Provoucher::find($voucher_id);
            $pstatus->waiting = "No";
            $pstatus->status = 1;
            $pstatus->save();

            }else {
            $pstatus = Provoucher::find($voucher_id);
            $pstatus->waiting = "No";
            $pstatus->save();
            }   

    //     foreach($result as $chrt_id => $vchr_ids)
    //     {

    //     $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
    //     $charity = Charity::where('id','=',$chrt_id)->first();

    //     $pdf = PDF::loadView('invoices.pendingvreport', compact('remittances','charity'));
    //     $output = $pdf->output();
    //     file_put_contents(public_path().'/invoices/'.'voucher_Report#'.$charity->id.'.pdf', $output);

    //     $contactmail = ContactMail::where('id', 1)->first()->name;

    //     $array['subject'] = 'Remittance Report';
    //     $array['from'] = 'info@tevini.co.uk';
    //     $array['cc'] = $contactmail;
    //     $array['name'] = $charity->name;
    //     $email = $charity->email;
    //     $array['charity'] = $charity;
    //     $array['file'] = public_path().'/invoices/voucher_Report#'.$charity->id.'.pdf';
    //     $array['file_name'] = 'voucher_Report#'.$charity->id.'.pdf';
    //     $array['subjectsingle'] = 'Report Placed - '.$charity->id;

    //     Mail::to($email)
    //     ->cc($contactmail)
    //     ->send(new PendingvReport($array));
    // }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Waiting voucher status change successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);
    
}


public function waiting_CancelBydonor(Request $request)
    {
        if(empty($request->voucher_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $charity_id = $request->charity_id;
        $voucher_id = $request->voucher_id;


        $voucher = Provoucher::where('id',$voucher_id)->first();

        Usertransaction::where('id', $voucher->tran_id)->delete();

        $pstatus = Provoucher::find($voucher_id);
        $pstatus->status = 3;
        $pstatus->waiting = "Cancel";
        $pstatus->save();


    //     foreach($result as $chrt_id => $vchr_ids)
    //     {

    //     $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
    //     $charity = Charity::where('id','=',$chrt_id)->first();

    //     $pdf = PDF::loadView('invoices.pendingvreport', compact('remittances','charity'));
    //     $output = $pdf->output();
    //     file_put_contents(public_path().'/invoices/'.'voucher_Report#'.$charity->id.'.pdf', $output);

    //     $contactmail = ContactMail::where('id', 1)->first()->name;

    //     $array['subject'] = 'Remittance Report';
    //     $array['from'] = 'info@tevini.co.uk';
    //     $array['cc'] = $contactmail;
    //     $array['name'] = $charity->name;
    //     $email = $charity->email;
    //     $array['charity'] = $charity;
    //     $array['file'] = public_path().'/invoices/voucher_Report#'.$charity->id.'.pdf';
    //     $array['file_name'] = 'voucher_Report#'.$charity->id.'.pdf';
    //     $array['subjectsingle'] = 'Report Placed - '.$charity->id;

    //     Mail::to($email)
    //     ->cc($contactmail)
    //     ->send(new PendingvReport($array));
    // }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Waiting voucher status change successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);

    }

}
