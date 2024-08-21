<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Order;
use App\Models\Barcode;
use App\Models\OrderHistory;
use App\Models\Voucher;
use App\Models\Charity;
use App\Models\Provoucher;
use App\Models\Draft;
use App\Models\Batchprov;
use App\Models\Commission;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use App\Models\ProvouchersImages;

use App\Mail\InstantReport;
use App\Mail\PendingvCancelReport;
use App\Mail\PendingvReport;
use App\Mail\WaitingVoucherCancel;
use App\Mail\WaitingvoucherReport;
use Auth;
use PDF;

use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function userStandingOrder()
    {
        return view('frontend.user.standingorder');
    }

    public function userOrderVoucherBook(Request $request)
    {
        
        $cartJson = $request->session()->get('cart', '[]');
        $cart = json_decode($cartJson, true);
        return view('frontend.user.voucharbook', compact('cart'));
    }

    public function userOrderVoucherBookstoreCart(Request $request)
    {
        $request->session()->put('cart', $request->input('cart'));

        return response()->json(['success' => true]);
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

            if($request->delivery == "false" && $request->collection == "false"){
                $message ="<div class='alert alert-danger'>Please select delivery option.</div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            if($request->delivery == "true"){
                $delivery_opt = "Delivery";
            }elseif($request->collection == "true"){
                $delivery_opt = "Collection";
            }else{
                $delivery_opt = null;
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

            if ($prepaid_amount > 0) {
                if($limitChk < $prepaid_amount ){
                    $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn limit exceed.</b></div>";
                    return response()->json(['status'=> 303,'message'=>$message]);
                    exit();
                }
            }
            

        $order = new Order();
        $order->user_id = $request->did;
        $order->order_id = time() . "-" . $request->did;

        if ($delivery_opt = "Delivery") {
            $order->amount = $prepaid_amount + $request->delivery_charge;
            $order->delivery_charge = $request->delivery_charge;
        } else {
            $order->amount = $prepaid_amount;
            $order->delivery_charge = 0;
        }

        if($request->delivery == "true"){
            $order->delivery_option = "Delivery";
        }elseif($request->collection == "true"){
            $order->delivery_option = "Collection";
        }else{
            $order->delivery_option = null;
        }


        $order->notification = 1;
        $order->status = 0;
        if($order->save()){



            $ppc = 0;
            foreach($voucher_ids as $key => $voucher_id)
            {
                if($qtys[$key] != "0"){

                    if($qtys[$key] > "1"){

                        for($x = 0; $x < $qtys[$key]; $x++)
                        {
                        $unique = time().rand(1,100);
                        //order history
                        $amount =  Voucher::where('id',$voucher_id)->first()->amount;
                        $input['order_id'] = $order->id;
                        $input['voucher_id'] = $voucher_id;
                        $input['number_voucher'] = 1;
                        $input['amount'] = $amount;
                        $input['o_unq'] = $unique;
                        $input['status'] = "0";
                        OrderHistory::create($input);
                        }

                    }else{

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
                    }
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
                        $utransaction->order_id = $order->id;
                        $utransaction->title ="Prepaid Voucher Book";
                        $utransaction->status =  1;
                        $utransaction->save();
                        $ppc = $ppc + 1;
                    }

                }
            }

            if ($ppc > 0) {
                if($request->delivery == "true"){
                    if ($prepaid_amount < 200) {
                        $udtransaction = new Usertransaction();
                        $udtransaction->t_id = time() . "-" . $request->did;
                        $udtransaction->user_id = $request->did;
                        $udtransaction->t_type = "Out";
                        $udtransaction->amount =  3.50;
                        $udtransaction->t_unq = time().rand(1,100);
                        $udtransaction->order_id = $order->id;
                        $udtransaction->title ="Delivery Charge";
                        $udtransaction->status =  1;
                        $udtransaction->save();
                    }
                }
            }
            

            if($prepaid_amount !=0){
                $user = User::find($request->did);
                $user->decrement('balance',$order->amount);
                $user->save();

                // card balance update
                if (isset($user->CreditProfileId)) {
                    $CreditProfileId = $user->CreditProfileId;
                    $CreditProfileName = $user->name;
                    $AvailableBalance = 0 - $prepaid_amount - $request->delivery_charge;
                    $comment = "Voucher Store";
                    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                            'CreditProfileId' => $CreditProfileId,
                            'CreditProfileName' => $CreditProfileName,
                            'AvailableBalance' => $AvailableBalance,
                            'comment' => $comment,
                        ]);
                }
                // card balance update end
            }

            session()->forget('cart');
            $user = User::where('id',$request->did)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['subject'] = 'Order place to Tevini';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $array['name'] = $user->name;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['order_id'] = $order->id;
            $array['delivery_option'] = $delivery_opt;


            Mail::send('mail.order', compact('array'), function($message)use($array,$email) {
             $message->from($array['from'], 'Tevini.co.uk');
             $message->to($email)->cc($array['cc'])->subject($array['subject']);
            });


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher order place successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

        }


    }

    // voucher draft
    public function pvoucherDraft(Request $request)
    {
        $charity_id= $request->charityId;
        $donor_ids = $request->donorIds;
        $donor_names = $request->donorNms;
        $donor_accs = $request->donorAccs;
        $chqs = $request->chqNos;
        $amounts = $request->amts;
        $notes = $request->notes;
        $waitings = $request->waitings;


        $check_chqs = Provoucher::all();

        if(empty($charity_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select a charity first.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        foreach( array_count_values($chqs) as $key => $val ) {
            if ( $val > 1 ){
                $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher ".$key." is more than one entry. </b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
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

            foreach($donor_ids as $key => $donor_id)
            {
                if(!isset(Draft::where('voucher_number','=', $chqs[$key])->first()->charity_id)){
                $pvsr =  new Draft();
                $pvsr->charity_id = $charity_id;
                $pvsr->donor_id = $donor_id;
                $pvsr->donor_acc = $donor_accs[$key];
                $pvsr->donor_name = $donor_names[$key];
                $pvsr->voucher_number = $chqs[$key];
                $pvsr->amount = $amounts[$key];
                $pvsr->note = $notes[$key];
                $pvsr->waiting = $waitings[$key];
                $pvsr->status = 1;
                $pvsr->save();
                }

            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher save as a draft successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message, 'charity_id'=>$charity_id,]);

    }


    public function pvoucherStore(Request $request)
    {
        $charity_id= $request->charityId;
        $donor_ids = $request->donorIds;
        $donor_accs = $request->donorAccs;
        $chqs = $request->chqNos;
        $amounts = $request->amts;
        $notes = $request->notes;
        $waitings = $request->waitings;


        $check_chqs = Provoucher::all();

        if(empty($charity_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select a charity first.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        foreach( array_count_values($chqs) as $key => $val ) {
            if ( $val > 1 ){
                $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher ".$key." is more than one entry. </b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
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
                if($limitChk < $amounts[$key] || $waitings[$key] =="Yes"){
                $utransaction->pending = 0; //transaction pending e ase
                $utransaction->status =  0; //status pending
                }else{
                $utransaction->pending = 1; //transaction complete
                $utransaction->status =  1; //status complete  
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
                $pvsr->waiting = $waitings[$key];
                if($limitChk < $amounts[$key] || $waitings[$key] =="Yes"){
                    $pvsr->status = 0;  //process voucher pending
                }else{
                    $pvsr->status = 1;  //process voucher complete
                }
                $pvsr->tran_id =  $utransaction->id;
                $pvsr->save();

                if($limitChk >= $amounts[$key] && $waitings[$key] =="No"){
                $ch = Charity::find($charity_id);
                $ch->increment('balance',$amounts[$key]);
                $ch->save();
                $user = User::find($donor_id);
                $user->decrement('balance',$amounts[$key]);
                $user->save();

                // card balance update
                if (isset($user->CreditProfileId)) {
                    $CreditProfileId = $user->CreditProfileId;
                    $CreditProfileName = $user->name;
                    $AvailableBalance = 0 - $amounts[$key];
                    $comment = "Pending Voucher Balance update";
                    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                            'CreditProfileId' => $CreditProfileId,
                            'CreditProfileName' => $CreditProfileName,
                            'AvailableBalance' => $AvailableBalance,
                            'comment' => $comment,
                        ]);
                }
                // card balance update end


                }

                Draft::truncate();

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

        $previous_pending = Provoucher::where([
            ['charity_id','=', $charityid],
            ['status', '=', '0']
            ])->sum('amount');

        $pdf = PDF::loadView('invoices.inst_report', compact('total','remittance','charity','previous_pending'));
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
        $cvouchers = Provoucher::select('id','user_id','charity_id','created_at','amount','note','cheque_no')->where('status','=', '1')->orderBy('id','DESC')->limit(1500)->get();
        // $cvouchers = Provoucher::where('status','=', '1')->orderBy('id','DESC')->get();
        return view('voucher.completevoucher')
        ->with('cvouchers',$cvouchers);

    }

    public function waitingVoucher()
    {
        $wvouchers = Provoucher::where('waiting','=', 'Yes')->orderBy('id','DESC')->get();
        return view('voucher.waitingvoucher')
        ->with('wvouchers',$wvouchers);

    }


    public function pendingVoucher()
    {
        $cvouchers = Provoucher::where([
            ['waiting', '=', 'No'],
            ['status', '=', '0']
        ])->orderBy('id','DESC')->get();
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

    public function addStartBarcode(Request $request)
    {
        if(empty($request->startbarcode)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please start barcode fill field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = OrderHistory::find($request->orderhisid);
        $user->startbarcode = $request->startbarcode;
        if($user->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Start Barcode added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function addNumberofpage(Request $request)
    {

        $number_pages = $request->pages;

        if(empty($number_pages)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill pages field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $order_history = OrderHistory::where('id','=', $request->orderhisid)->first();

        $startbarcode = $order_history->startbarcode;
        $single_vamount = $order_history->voucher->single_amount;

        if(empty($startbarcode)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please add start Start Barcode first.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $endbarcode = $startbarcode + $number_pages;

        $padd = OrderHistory::find($request->orderhisid);
        $padd->total_page = $number_pages;
        if($padd->save()){

            for($x = $startbarcode; $x < $endbarcode; $x++)
            {


                $addbarcode = new Barcode();
                $addbarcode->orderhistory_id = $request->orderhisid;
                $addbarcode->user_id = $request->user_id;
                $addbarcode->barcode = $x;
                $addbarcode->amount = $single_vamount;
                $addbarcode->save();


           }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Number of pages added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function barcode($id)
    {

        $order = Order::where('id',$id)->first();
        $user_id = $order->user_id;
        $user = User::where('id','=', $user_id)->first();
        $orderDtls = OrderHistory::where('order_id',  $id)->get();
        return view('voucher.barcode')
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

        //get data using barcode
        public function getbarCode(Request $request)
        {
            $orderDtl = Barcode::where('barcode', '=', $request->barcode)->first();

            if(empty($orderDtl)){
                $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>No data found.</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }else{
                return response()->json(['status'=> 300,'donorname'=>$orderDtl->user->name, 'donorid'=>$orderDtl->user_id,'donoracc'=>$orderDtl->user->accountno, 'amount'=>$orderDtl->amount ]);
            }
        }

        public function getCharitybarCode(Request $request)
        {

            $orderDtl = Barcode::where('barcode', '=', $request->barcode)->first();

            if(empty($orderDtl)){
                
                $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>No data found.</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
                
            }else{

                $orderHistory = OrderHistory::where('id', $orderDtl->orderhistory_id)->first();
                $voucher = Voucher::where('id', $orderHistory->voucher_id)->first();

                if ($voucher->type == "Blank") {
                    $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This is blank voucher.You can't process.</b></div>";
                    return response()->json(['status'=> 303,'message'=>$message, 'vouchertype'=>$voucher->type]);
                    exit();
                } else {
                    return response()->json(['status'=> 300,'donorname'=>$orderDtl->user->name, 'donorid'=>$orderDtl->user_id,'donoracc'=>$orderDtl->user->accountno, 'amount'=>$orderDtl->amount, 'vouchertype'=>$voucher->type ]);
                }
                
            }

        }

    public function pvComplete(Request $request)
    {
     if(empty($request->voucherIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $array1 = $request->charityIds;
        $array2 = $request->voucherIds;

        $result = [];

        $index = 0;
        foreach( $array1 as $key => $value ){
            $result[$value][] = $array2[$index];
            $index++;
        }

        foreach($array2 as $key => $voucher_id)
        {

        $voucher = Provoucher::where('id',$voucher_id)->first();


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

            // card balance update
            if (isset($donor->CreditProfileId)) {
                $CreditProfileId = $donor->CreditProfileId;
                $CreditProfileName = $donor->name;
                $AvailableBalance = 0 - $voucher->amount;
                $comment = "Provoucher complete";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end


            $pstatus = Provoucher::find($voucher_id);
            $pstatus->status = 1;
            $pstatus->save();
        }

        foreach($result as $chrt_id => $vchr_ids)
        {

        $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
        $charity = Charity::where('id','=',$chrt_id)->first();

        $pdf = PDF::loadView('invoices.pendingvreport', compact('remittances','charity'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'voucher_Report#'.$charity->id.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $charity->email;
        $array['charity'] = $charity;
        $array['file'] = public_path().'/invoices/voucher_Report#'.$charity->id.'.pdf';
        $array['file_name'] = 'voucher_Report#'.$charity->id.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charity->id;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new PendingvReport($array));
    }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Process voucher status change successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function pvCancel(Request $request)
    {
     if(empty($request->voucherIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $array1 = $request->charityIds;
        $array2 = $request->voucherIds;

        $result = [];

        $index = 0;
        foreach( $array1 as $key => $value ){
            $result[$value][] = $array2[$index];
            $index++;
        }

        foreach($array2 as $key => $voucher_id)
        {

        $voucher = Provoucher::where('id',$voucher_id)->first();

        Usertransaction::where('id', $voucher->tran_id)->delete();

        $pstatus = Provoucher::find($voucher_id);
        $pstatus->status = 3;
        $pstatus->save();

        }
        foreach($result as $chrt_id => $vchr_ids)
        {

        $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
        $charity = Charity::where('id','=',$chrt_id)->first();

        $pdf = PDF::loadView('invoices.pendingvreport', compact('remittances','charity'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'voucher_Report#'.$charity->id.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $charity->email;
        $array['charity'] = $charity;
        $array['file'] = public_path().'/invoices/voucher_Report#'.$charity->id.'.pdf';
        $array['file_name'] = 'voucher_Report#'.$charity->id.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charity->id;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new PendingvCancelReport($array));
    }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Process voucher status change successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);

    }

    ///////////////// waiting voucher start ////////////////////

    public function watingvoucherComplete(Request $request)
    {
     if(empty($request->voucherIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $donor_ids = $request->donorIds;
        $charity_ids = $request->charityIds;
        $voucher_ids = $request->voucherIds;

        $result = [];

        $index = 0;
        foreach( $charity_ids as $key => $value ){
            $result[$value][] = $voucher_ids[$index];
            $index++;
        }

        foreach($voucher_ids as $key => $voucher_id)
        {

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

            // card balance update
            if (isset($donor->CreditProfileId)) {
                $CreditProfileId = $donor->CreditProfileId;
                $CreditProfileName = $donor->name;
                $AvailableBalance = 0 - $voucher->amount;
                $comment = "waiting voucher complete";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end



            $pstatus = Provoucher::find($voucher_id);
            $pstatus->waiting = "No";
            $pstatus->status = 1;
            $pstatus->save();

            }else {
            $pstatus = Provoucher::find($voucher_id);
            $pstatus->waiting = "No";
            $pstatus->save();
            }   
        }

        foreach($result as $chrt_id => $vchr_ids)
        {

        $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
        $charity = Charity::where('id','=',$chrt_id)->first();

        $pdf = PDF::loadView('invoices.waiting_vouchercomplete', compact('remittances','charity'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'confirm_waiting_voucher#'.$charity->id.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $charity->email;
        $array['charity'] = $charity;
        $array['file'] = public_path().'/invoices/confirm_waiting_voucher#'.$charity->id.'.pdf';
        $array['file_name'] = 'confirm_waiting_voucher#'.$charity->id.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charity->id;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new PendingvReport($array));
    }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Waiting voucher status change successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);
    
}


public function watingvoucherCancel(Request $request)
    {
     if(empty($request->voucherIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $donor_ids = $request->donorIds;
        $voucher_ids = $request->voucherIds;

        $result = [];

        $index = 0;
        foreach( $donor_ids as $key => $value ){
            $result[$value][] = $voucher_ids[$index];
            $index++;
        }

        foreach($voucher_ids as $key => $voucher_id)
        {

        $voucher = Provoucher::where('id',$voucher_id)->first();

        Usertransaction::where('id', $voucher->tran_id)->delete();

        $pstatus = Provoucher::find($voucher_id);
        $pstatus->status = 3;
        $pstatus->waiting = "Cancel";
        $pstatus->save();

        }

        foreach($result as $chrt_id => $vchr_ids)
        {

        $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
        $charity = Charity::where('id','=',$chrt_id)->first();

        $pdf = PDF::loadView('invoices.pendingvreport', compact('remittances','charity'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'voucher_Report#'.$charity->id.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $charity->email;
        $array['charity'] = $charity;
        $array['file'] = public_path().'/invoices/voucher_Report#'.$charity->id.'.pdf';
        $array['file_name'] = 'voucher_Report#'.$charity->id.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charity->id;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new WaitingVoucherCancel($array));
    }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Waiting voucher status change successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);

    }

    public function watingvoucherImageadd(Request $request)
    {
        $process_voucherId =$request->process_voucher_id;

        $image_record = ProvouchersImages::where('provouchers_id', $process_voucherId)->first();

        if ($image_record) {
            $Old_image_path = public_path('images/waiting_voucher/'.$image_record->image_name);
            unlink($Old_image_path);
            $image_record->delete();
        }

        if ($request->image) {
            $file = $request->image;
            if($file != null){
            $originalName = $file->getClientOriginalName();
            $filename = $process_voucherId . '_' . $originalName;

            $request->image->move(public_path('images/waiting_voucher'), $filename);

            // Insert a record in the database with the unique filename and the unique ID
            $image = new ProvouchersImages();
            $image->image_name = $filename;
            $image->provouchers_id = $process_voucherId;
            $image->save();
            }
        }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>File added successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);

    }


    public function watingvoucherMail(Request $request)
    {
     if(empty($request->voucherIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher id not define</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $donor_ids = $request->donorIds;
        $voucher_ids = $request->voucherIds;

        $result = [];

        $index = 0;
        foreach( $donor_ids as $key => $value ){
            $result[$value][] = $voucher_ids[$index];
            $index++;
        }


        foreach($result as $donor_id => $vchr_ids)
        {

        $image_records = ProvouchersImages::whereIn('provouchers_id', $vchr_ids)->get(); 

        $image_attachments = [];

        foreach ($image_records as $image_record) {
            $image_path = public_path('images/waiting_voucher/'.$image_record->image_name);
    
            if (file_exists($image_path)) {
                // If the image file exists, add it as an attachment to the email
                $image_attachments[] = [
                    'path' => $image_path,
                    'name' => $image_record->image_name,
                ];
            }
        }



        $remittances = Provoucher::whereIn('id', $vchr_ids)->get();
        $donor = User::where('id','=',$donor_id)->first();

        $pdf = PDF::loadView('invoices.waiting_vreport', compact('remittances','donor'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'waiting_voucher_Report#'.$donor->id.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Waiting Voucher Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $donor->name;
        $email = $donor->email;
        $array['donor'] = $donor;
        $array['image_attachments'] = $image_attachments;
        $array['file'] = public_path().'/invoices/waiting_voucher_Report#'.$donor->id.'.pdf';
        $array['file_name'] = 'waiting_voucher_Report#'.$donor->id.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$donor->id;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new WaitingvoucherReport($array));
        }

    $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Send mail to donor successfully.</b></div>";
    return response()->json(['status'=> 300,'message'=>$message]);

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

                    // card balance update
                    if (isset($donor->CreditProfileId)) {
                        $CreditProfileId = $donor->CreditProfileId;
                        $CreditProfileName = $donor->name;
                        $AvailableBalance = $amount*$order->number_voucher;
                        $comment = "Make a donation or Standing order";
                        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                                'CreditProfileId' => $CreditProfileId,
                                'CreditProfileName' => $CreditProfileName,
                                'AvailableBalance' => $AvailableBalance,
                                'comment' => $comment,
                            ]);
                    }
                    // card balance update end

                    Usertransaction::where(['order_id'=>$request->orderId])->update(['status'=>'0']);


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
        $commissions = Commission::orderBy('id','DESC')->get();
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
