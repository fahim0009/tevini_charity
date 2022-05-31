<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Voucher;
use App\Models\Charity;
use App\Models\Provoucher;
use App\Models\Batchprov;
use App\Models\Commission;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use App\Mail\InstantReport;
use App\Mail\PendingvReport;
use Auth;
use PDF;

class OrderController extends Controller
{
    public function userStandingOrder()
    {
        return view('frontend.user.standingorder');
    }

    public function userOrderVoucherBook()
    {
        return view('frontend.user.voucharbook');
    }
    public function voucherBookStock()
    {
        $voucher = Voucher::all();
        return view('voucher.index', compact('voucher'));
    }

    public function voucherinAdmin($id)
    {
        return view('donor.voucherorder')
        ->with('donor_id',$id);
    }

    public function storeVoucher(Request $request)
    {
        $voucher_ids= $request->voucherIds;
        $qtys = $request->qtys;
        $prepaid_amount= 0;
        $order_amount= 0;
        $all_zero = true;

            foreach($qtys as $key => $qty){
             if($qty > Voucher::where('id',$voucher_ids[$key])->first()->stock){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>One or some vouchers stock limit exceeded.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
             }
            }

            foreach($qtys as $qty){
                if($qty != '0')
                    {
                        $all_zero = false;
                        break;
                    }
            }

            if($all_zero == true){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You didn't select any voucher.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            }

            foreach($voucher_ids as $key => $id){
                $order_amount+= Voucher::where('id',$id)->first()->amount*$qtys[$key];

                $p_amount =  Voucher::where('id',$id)->first()->amount;

                if(Voucher::where('id',$id)->first()->type == "Prepaid"){
                    $prepaid_amount += $p_amount*$qtys[$key];
                    }
            }

            $u_bal = User::where('id',$request->did)->first()->balance;
            $overdrawn = (User::where('id',$request->did)->first()->overdrawn_amount);
            $limitChk = $u_bal + $overdrawn;

            if($limitChk < $prepaid_amount ){
                $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn limit exceed.</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

        $order = new Order();
        $order->user_id = $request->did;
        $order->order_id = time() . "-" . $request->did;
        $order->amount = $prepaid_amount;
        $order->status = 0;
        if($order->save()){



            foreach($voucher_ids as $key => $voucher_id)
            {
                if($qtys[$key] != "0"){

                $unique = time().rand(1,100);

                //order history
                $amount =  Voucher::where('id',$voucher_id)->first()->amount;
                $input['order_id'] = $order->id;
                $input['voucher_id'] = $voucher_id;
                $input['number_voucher'] = $qtys[$key];
                $input['amount'] = $qtys[$key]*$amount;
                $input['o_unq'] = $unique;
                $input['status'] = "0";
                OrderHistory::create($input);

                //voucher stock decrement
                $v = Voucher::find($voucher_id);
                $v->decrement('stock',$qtys[$key]);
                $v->save();

                //user transaction out if voucher is/are prepaid
                if(Voucher::where('id',$voucher_id)->first()->type == "Prepaid"){
                $utransaction = new Usertransaction();
                $utransaction->t_id = time() . "-" . $request->did;
                $utransaction->user_id = $request->did;
                $utransaction->t_type = "Out";
                $utransaction->amount =  $qtys[$key]*$amount;
                $utransaction->t_unq = $unique;
                $utransaction->title ="Prepaid Voucher Book";
                $utransaction->status =  1;
                $utransaction->save();
                }

                 }
            }

            if($prepaid_amount !=0){
            $user = User::find($request->did);
            $user->decrement('balance',$prepaid_amount);
            $user->save();
            }

            $user = User::where('id',$request->did)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['subject'] = 'Order place to Tevini';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $array['name'] = $user->name;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['order_id'] = $order->id;


            Mail::send('mail.order', compact('array'), function($message)use($array,$email) {
             $message->from($array['from'], 'Tevini.co.uk');
             $message->to($email)->cc($array['cc'])->subject($array['subject']);
            });


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher order place successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

        }


    }


    public function pvoucherStore(Request $request)
    {
        $charity_id= $request->charityId;
        $donor_ids = $request->donorIds;
        $donor_accs = $request->donorAccs;
        $chqs = $request->chqNos;
        // $vtyps = $request->vTyps;
        $amounts = $request->amts;
        $notes = $request->notes;

        $check_chqs = Provoucher::all();

        if(empty($charity_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select a charity first.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        foreach($chqs as $chq){
            foreach($check_chqs as $check_chq){
                if($chq == $check_chq->cheque_no){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher number ".$chq." is already proccesed. </b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();


                }
            }
        }

        foreach($donor_ids as $key => $donor_id){
            if($donor_id == "" || $donor_accs[$key] == "" || $chqs[$key] == "" || $amounts[$key] == ""){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill all field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            }
        }


        // new code
        $batch = new Batchprov();
        $batch->charity_id = $charity_id;
        $batch->status = 0;

        if($batch->save()){

            foreach($donor_ids as $key => $donor_id)
            {
                $u_bal = User::where('id',$donor_id)->first()->balance;
                $overdrawn = (User::where('id',$donor_id)->first()->overdrawn_amount);
                $limitChk = $u_bal + $overdrawn;

                $utransaction = new Usertransaction();
                $utransaction->t_id = time() . "-" . $donor_id;
                $utransaction->user_id = $donor_id;
                $utransaction->charity_id = $charity_id;
                $utransaction->t_type = "Out";
                $utransaction->amount =  $amounts[$key];
                $utransaction->cheque_no =  $chqs[$key];
                $utransaction->title =  "Voucher";
                if($limitChk >= $amounts[$key]){
                $utransaction->pending = 1;
                $utransaction->status =  1;
                }else{
                $utransaction->pending = 0;
                $utransaction->status =  0;
                }
                $utransaction->save();

                $pvsr =  new Provoucher();
                $pvsr->charity_id = $charity_id;
                $pvsr->user_id = $donor_id;
                $pvsr->batch_id = $batch->id;
                $pvsr->donor_acc = $donor_accs[$key];
                $pvsr->cheque_no = $chqs[$key];
                $pvsr->amount = $amounts[$key];
                $pvsr->note = $notes[$key];
                if($limitChk >= $amounts[$key]){
                    $pvsr->status = 1;
                }else{
                    $pvsr->status = 0;
                }
                $pvsr->tran_id =  $utransaction->id;
                $pvsr->save();

                if($limitChk >= $amounts[$key]){
                $ch = Charity::find($charity_id);
                $ch->increment('balance',$amounts[$key]);
                $ch->save();
                $user = User::find($donor_id);
                $user->decrement('balance',$amounts[$key]);
                }


            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher Process successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message, 'charity_id'=>$charity_id, 'batch_id'=>$batch->id, ]);
        }

    }

    public function instReport($id)
    {
        $batch_id = $id;
        $charityid = Provoucher::where('batch_id','=', $id)->first()->charity_id;
        $charity = Charity::where('id','=',$charityid)->first();
        $remittance = Provoucher::where('batch_id','=', $id)->get();
        $total = Provoucher::where([
            ['batch_id','=', $id],
            ['status', '=', '1']
            ])->sum('amount');
        return view('voucher.instreport',compact('remittance','total','charity','batch_id'));
    }

    public function instReportmail(Request $request)
    {
        $charityid = Provoucher::where('batch_id','=', $request->batch_id)->first()->charity_id;
        $charity = Charity::where('id','=',$charityid)->first();
        $remittance = Provoucher::where('batch_id','=', $request->batch_id)->orderBy('id','ASC')->get();
        $total = Provoucher::where([
            ['batch_id','=', $request->batch_id],
            ['status', '=', '1']
            ])->sum('amount');

        $user = User::where('id',$request->did)->first();

        $pdf = PDF::loadView('invoices.inst_report', compact('total','remittance','charity'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'voucherReport#'.$charityid.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $request->mail;
        $array['charity'] = $charity;
        $array['remittance'] = $remittance;
        $array['total'] = $total;
        $array['file'] = public_path().'/invoices/voucherReport#'.$charityid.'.pdf';
        $array['file_name'] = 'voucherReport#'.$charityid.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charityid;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new InstantReport($array));


        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Mail sent successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);


    }


    public function addStock(Request $request)
    {
        if(empty($request->stockno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill stock field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = Voucher::find($request->stockId);
        $user->stock = $request->stockno + Voucher::where('id',$request->stockId)->first()->stock;
        if($user->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Stock added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }



    public function processVoucher()
    {
        $charities = Charity::all();
        $donors = User::where([
            ['is_type', '=', 'user'],
            ['status', '=', '1']
        ])->get();

        return view('voucher.processvoucher')
        ->with('charities',$charities)
        ->with('donors',$donors);
    }



    public function completeVoucher()
    {
        $cvouchers = Provoucher::where('status','=', '1')->orderBy('id','DESC')->get();
        return view('voucher.completevoucher')
        ->with('cvouchers',$cvouchers);

    }

    public function pendingVoucher()
    {
        $cvouchers = Provoucher::where('status','=', '0')->orderBy('id','DESC')->get();
        return view('voucher.pendingvoucher')
        ->with('cvouchers',$cvouchers);

    }



    public function singleOrder($id)
    {

        $order = Order::where('id',$id)->first();

        $user_id = $order->user_id;

        $user = User::where('id','=', $user_id)->first();

        $orderDtls = OrderHistory::where('order_id',  $id)->get();

        return view('voucher.singleorder')
        ->with('user',$user)
        ->with('order',$order)
        ->with('orderDtls',$orderDtls);

    }

    public function findName(Request $request)
    {

        $user = User::where([
            ['accountno', '=', $request->accno],
            ['is_type', '=', 'user'],
            ['status', '=', '1']
        ])->get();

        $donorname = "Not found";
        $donorid = "";

        foreach($user as $u){
            $donorname = $u->name;
            $donorid = $u->id;
        }


        return response()->json(['status'=> 300,'donorname'=>$donorname, 'donorid'=>$donorid]);

    }

    public function voucherStatus(Request $request)
    {
     if(empty($request->vid)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $voucher = Provoucher::where('id',$request->vid)->first();


        if($request->status == "3"){

            $utransaction = Usertransaction::find($voucher->tran_id);
            $utransaction->pending = '3';
            $utransaction->save();



         }else if($request->status == "1"){

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

         }

        $pstatus = Provoucher::find($request->vid);
        $pstatus->status = $request->status;
        if($pstatus->save()){

        $remittance = Provoucher::where('id',$request->vid)->first();
        $charity = Charity::where('id','=',$remittance->charity_id)->first();
        $user = User::where('id',$remittance->user_id)->first();

        $pdf = PDF::loadView('invoices.pendingvreport', compact('user','remittance','charity'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'voucher_Report#'.$charity->id.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $charity->email;
        $array['charity'] = $charity;
        $array['remittance'] = $remittance;
        $array['file'] = public_path().'/invoices/voucher_Report#'.$charity->id.'.pdf';
        $array['file_name'] = 'voucher_Report#'.$charity->id.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charity->id;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new PendingvReport($array));


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Process voucher status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function orderStatus(Request $request)
    {

        if($request->status == "3"){

            $orders = OrderHistory::where('order_id',$request->orderId)->get();

            foreach($orders as $order){

                if(Voucher::where('id',$order->voucher_id)->first()->type == "Prepaid"){

                    $amount = Voucher::where('id',$order->voucher_id)->first()->amount;

                    $donor = User::find(Order::where('id',$request->orderId)->first()->user_id);
                    $donor->increment('balance',$amount*$order->number_voucher);
                    $donor->save();

                    $utransaction = Usertransaction::find(Usertransaction::where('t_unq',$order->o_unq)->first()->id);
                    $utransaction->status = '0';
                    $utransaction->save();

                }

            }

         }


        $order = Order::find($request->orderId);
        $order->status = $request->status;
        if($order->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Order status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function newOrder()
    {
        $orders = Order::where('status','0')->orderBy('id','DESC')->get();
        return view('voucher.order')
        ->with('orders',$orders);
    }

    public function completeOrder()
    {
        $orders = Order::where('status','1')->orderBy('id','DESC')->get();
        return view('voucher.order')
        ->with('orders',$orders);
    }


    public function cancelOrder()
    {
        $orders = Order::where('status','3')->orderBy('id','DESC')->get();
        return view('voucher.order')
        ->with('orders',$orders);
    }



    public function commission()
    {
        $commissions = Commission::all();
        return view('others.commission', compact('commissions'));
    }


    public function userOrderview()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('id','DESC')->get();
        return view('frontend.user.voucherorder')
        ->with('orders',$orders);

    }

    public function userOrderinAdmin($id)
    {
        $orders = Order::where('user_id', $id)->orderBy('id','DESC')->get();
        return view('donor.orderhistory')
        ->with('donor_id',$id)
        ->with('orders',$orders);

    }





}
