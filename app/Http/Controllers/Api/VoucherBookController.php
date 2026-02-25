<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Order;
use App\Models\Barcode;
use App\Models\OrderHistory;
use App\Models\Charity;
use App\Models\Provoucher;
use App\Models\Draft;
use App\Models\Batchprov;
use App\Models\Commission;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use App\Models\ProvouchersImages;

use App\Mail\WaitingVComplete;
use App\Mail\InstantReport;
use App\Mail\PendingvReport;
use App\Mail\WaitingvoucherReport;
use App\Models\Transaction;
use App\Models\VoucherCart;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use PDF;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;

class VoucherBookController extends Controller
{
    public function userOrderVoucherBook()
    {
        $data = Voucher::where('status','=','1')->get();
        
        $cart = VoucherCart::where('user_id', Auth::user()->id)->get();
        $success['data'] = $data;
        $success['cart'] = $cart;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }

    public function voucherDetailsStore_old($order, $voucher_id, $amount, $unique, $voucherDtl)
    {

        if ($voucher_id == 176) {
            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "3";
            $input['status'] = "0";
            OrderHistory::create($input);

            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "5";
            $input['status'] = "0";
            OrderHistory::create($input);

            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "10";
            $input['status'] = "0";
            OrderHistory::create($input);
            
            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "18";
            $input['status'] = "0";
            OrderHistory::create($input);

        } else {
            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "20";
            $input['status'] = "0";
            OrderHistory::create($input);

            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "25";
            $input['status'] = "0";
            OrderHistory::create($input);

            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "36";
            $input['status'] = "0";
            OrderHistory::create($input);
            
            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "50";
            $input['status'] = "0";
            OrderHistory::create($input);

            $input['order_id'] = $order->id;
            $input['voucher_id'] = $voucher_id;
            $input['number_voucher'] = 1;
            $input['amount'] = 0;
            $input['o_unq'] = $unique;
            $input['mixed_value'] = "72";
            $input['status'] = "0";
            OrderHistory::create($input);
        }

    }

    public function storeVoucher_old(Request $request)
    {

        $vouchers = json_decode($request->vouchers, true); 

        $prepaid_amount = 0;
        $order_amount = 0;
        $all_zero = true;

            foreach ($vouchers as $item){

                if($item['qtys'] > Voucher::where('id',$item['voucherIds'])->first()->stock){
                    $success['message'] = 'One or some vouchers stock limit exceeded..';
                    return response()->json(['success'=>false,'response'=> $success], 202);
                    exit();
                }

            }

            foreach ($vouchers as $chkqty){
                if($chkqty['qtys'] != '0')
                    {
                        $all_zero = false;
                        break;
                    }
            }

            if($all_zero == true){
                $success['message'] = 'You didn\'t select any voucher..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
            }

            if($request->delivery == "false" && $request->collection == "false"){
                
                $success['message'] = 'Please select delivery option..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();

            }

            if($request->delivery != "false"){
                $delivery_opt = "Delivery";
            }elseif($request->collection != "false"){
                $delivery_opt = "Collection";
            }else{
                $delivery_opt = null;
            }

            foreach ($vouchers as $voucher){
                $order_amount+= Voucher::where('id',$voucher['voucherIds'])->first()->amount*$voucher['qtys'];

                $p_amount =  Voucher::where('id',$voucher['voucherIds'])->first()->amount;

                if(Voucher::where('id',$voucher['voucherIds'])->first()->type == "Prepaid"){
                    $prepaid_amount += $p_amount*$voucher['qtys'];
                    }
            }


            // donor balance
            $userTransactionBalance = UserTransaction::selectRaw('
                    SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                    SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
                ')
                ->where([
                    ['user_id', '=', auth()->user()->id],
                    ['status', '=', '1']
                ])
                ->orWhere([
                    ['user_id', '=', auth()->user()->id],
                    ['pending', '=', '1']
                ])
                ->first();

            $u_bal = (float) $userTransactionBalance->balance;
            $overdrawn = (float) User::where('id', Auth::id())->value('overdrawn_amount');

            $limitChk = $u_bal + $overdrawn;


            if($limitChk < $prepaid_amount ){
                $success['message'] = 'Overdrawn limit exceed..';
                return response()->json(['success'=>false,'response'=> $success], 202);
            }

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->order_id = time() . "-" . Auth::user()->id;

        if ($prepaid_amount < 200) {

            if ($delivery_opt == "Delivery") {
                $order->amount = $prepaid_amount + 3.50;
                $order->delivery_charge = '3.50';
            } else {
                $order->amount = $prepaid_amount;
                $order->delivery_charge = '0';
            }
            
        } else {
            $order->amount = $prepaid_amount;
            $order->delivery_charge = '0';
        }
        
        $order->delivery_option = $delivery_opt;
        $order->notification = 1;
        $order->status = 0;
        if($order->save()){

            
            $ppc = 0;
            foreach($vouchers as $vdata)
            {
                if($vdata['qtys'] != "0"){
                    if($vdata['qtys'] > "1"){

                        for($x = 0; $x < $vdata['qtys']; $x++)
                        {
                        $unique = time().rand(1,100);
                        //order history
                        $amount =  Voucher::where('id', $vdata['voucherIds'])->first()->amount;
                        $voucherDtl =  Voucher::where('id', $vdata['voucherIds'])->first();
                        $voucher_id = $vdata['voucherIds'];
                        $input['order_id'] = $order->id;
                        $input['voucher_id'] = $vdata['voucherIds'];
                        $input['number_voucher'] = 1;
                        $input['amount'] = $amount;
                        $input['o_unq'] = $unique;
                        $input['status'] = "0";
                        $vqtys = 1;
                            if ($voucherDtl->type == "Mixed") {
                                $this->voucherDetailsStore($order, $voucher_id, $amount, $unique, $voucherDtl, $vqtys);
                            } else {
                                OrderHistory::create($input);
                            }
                        }
                    }else{

                        $unique = time().rand(1,100);

                        //order history
                        $amount =  Voucher::where('id', $vdata['voucherIds'])->first()->amount;
                        $voucherDtl =  Voucher::where('id', $vdata['voucherIds'])->first();
                        $voucher_id = $vdata['voucherIds'];
                        $input['order_id'] = $order->id;
                        $input['voucher_id'] = $vdata['voucherIds'];
                        $input['number_voucher'] = $vdata['qtys'];
                        $input['amount'] = $vdata['qtys']*$amount;
                        $input['o_unq'] = $unique;
                        $input['status'] = "0";
                        $vqtys = $vdata['qtys'];

                        if ($voucherDtl->type == "Mixed") {
                            $this->voucherDetailsStore($order, $voucher_id, $amount, $unique, $voucherDtl, $vqtys);
                        } else {
                            OrderHistory::create($input);
                        }
                    }

                    //voucher stock decrement
                    $v = Voucher::find($vdata['voucherIds']);
                    $v->decrement('stock',$vdata['qtys']);
                    $v->save();

                    //user transaction out if voucher is/are prepaid
                    if(Voucher::where('id',$vdata['voucherIds'])->first()->type == "Prepaid"){
                        $utransaction = new Usertransaction();
                        $utransaction->t_id = time() . "-" . Auth::user()->id;
                        $utransaction->user_id = Auth::user()->id;
                        $utransaction->t_type = "Out";
                        $utransaction->amount =  $vdata['qtys']*$amount;
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
                if($delivery_opt == "Delivery"){
                    if ($prepaid_amount < 200) {
                        $udtransaction = new Usertransaction();
                        $udtransaction->t_id = time() . "-" . Auth::user()->id;
                        $udtransaction->user_id = Auth::user()->id;
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
                $user = User::find(Auth::user()->id);
                $user->decrement('balance',$order->amount);
                $user->save();

                // card balance update
                if (isset($user->CreditProfileId)) {
                    $CreditProfileId = $user->CreditProfileId;
                    $CreditProfileName = $user->name;
                    $AvailableBalance = 0 - $order->amount;
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

            VoucherCart::where('user_id', Auth::user()->id)->delete();
            $user = User::where('id',Auth::user()->id)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['subject'] = 'Voucher books order confirmation';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $array['name'] = $user->name;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['order_id'] = $order->id;
            $array['orderid'] = $order->order_id;
            $array['delivery_option'] = $delivery_opt;


            Mail::send('mail.order', compact('array'), function($message)use($array,$email) {
             $message->from($array['from'], 'Tevini.co.uk');
             $message->to($email)->cc($array['cc'])->subject($array['subject']);
            });


            $success['message'] = 'Voucher order place successfully.';
            $success['data'] = $order;
            $success['delivery'] = $delivery_opt;
            $success['prepaid_amount'] = $prepaid_amount;
            return response()->json(['success'=>true,'response'=> $success], 200);

        }


    }

    public function storeVoucher(Request $request)
    {
        $vouchersData = json_decode($request->vouchers, true);
        $prepaid_amount = 0;
        $delivery_charge = 0;
        $all_zero = true;

        // 1. Initial Validation & Stock Check
        foreach ($vouchersData as $item) {
            $vModel = Voucher::find($item['voucherIds']);
            if (!$vModel) continue;

            if ($item['qtys'] > 0) {
                $all_zero = false;
                if ($item['qtys'] > $vModel->stock) {
                    return response()->json(['success' => false, 'response' => ['message' => "Stock limit exceeded for £{$vModel->amount} vouchers."]], 202);
                }
                if ($vModel->type == "Prepaid") {
                    $prepaid_amount += ($vModel->amount * $item['qtys']);
                }
            }
        }

        if ($all_zero) {
            return response()->json(['success' => false, 'response' => ['message' => "You didn't select any voucher."]], 202);
        }

        if ($request->delivery == "false" && $request->collection == "false") {
            return response()->json(['success' => false, 'response' => ['message' => "Please select delivery option."]], 202);
        }

        // 2. Delivery Logic
        $delivery_opt = ($request->delivery != "false") ? "Delivery" : "Collection";
        if ($delivery_opt == "Delivery" && $prepaid_amount < 200 && $prepaid_amount > 0) {
            $delivery_charge = 3.50;
        }

        // 3. Balance Check
        $userTransactionBalance = Usertransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where('user_id', Auth::id())
            ->where(function($q) {
                $q->where('status', '1')->orWhere('pending', '1');
            })->first();

        $u_bal = (float) ($userTransactionBalance->balance ?? 0);
        $overdrawn = (float) Auth::user()->overdrawn_amount;

        if (($u_bal + $overdrawn) < $prepaid_amount) {
            return response()->json(['success' => false, 'response' => ['message' => "Overdrawn limit exceed."]], 202);
        }

        // 4. Processing Order
        try {
            DB::beginTransaction();

            $order = new Order();
            $order->user_id = Auth::id();
            $order->order_id = time() . "-" . Auth::id();
            $order->amount = $prepaid_amount + $delivery_charge;
            $order->delivery_charge = $delivery_charge;
            $order->delivery_option = $delivery_opt;
            $order->notification = 1;
            $order->status = 0;
            $order->save();



            foreach ($vouchersData as $vdata) {
                $qty = (int)$vdata['qtys'];
                if ($qty <= 0) continue;

                $voucher = Voucher::find($vdata['voucherIds']);
                
                // Loop for unique records
                for ($x = 0; $x < $qty; $x++) {
                    $uniqueCode = time() . rand(100, 999);
                    
                    if ($voucher->type == "Mixed") {
                        $this->voucherDetailsStore($order, $voucher->id, $voucher->amount, $uniqueCode, $voucher, 1);
                    } else {
                        OrderHistory::create([
                            'order_id' => $order->id,
                            'voucher_id' => $voucher->id,
                            'number_voucher' => 1,
                            'amount' => $voucher->amount,
                            'o_unq' => $uniqueCode,
                            'status' => "0"
                        ]);
                    }

                    // Handle Prepaid Transactions & System Credits
                    if ($voucher->type == "Prepaid") {
                        // Create Donor "Out" Transaction
                        Usertransaction::create([
                            't_id' => "VCH-" . time() . "-" . rand(10,99),
                            'user_id' => Auth::id(),
                            't_type' => "Out",
                            'amount' => $voucher->amount,
                            't_unq' => $uniqueCode,
                            'order_id' => $order->id,
                            'title' => "Prepaid Voucher Book",
                            'status' => 1
                        ]);

                    }
                }
                $voucher->decrement('stock', $qty);
            }

            // Delivery Charge Transaction
            if ($delivery_charge > 0) {
                Usertransaction::create([
                    't_id' => "DEL-" . time(),
                    'user_id' => Auth::id(),
                    't_type' => "Out",
                    'amount' => $delivery_charge,
                    't_unq' => time() . rand(1, 100),
                    'order_id' => $order->id,
                    'title' => "Delivery Charge",
                    'status' => 1
                ]);
            }

            // Finalize User Balance
            if ($prepaid_amount > 0) {
                $user = User::find(Auth::id());
                $user->decrement('balance', ($prepaid_amount + $delivery_charge));
                
                if ($user->CreditProfileId) {
                    $this->updateExternalBalance($user, ($prepaid_amount + $delivery_charge));
                }
            }

            VoucherCart::where('user_id', Auth::id())->delete();
            DB::commit();

            // 5. Email and Response
            $this->sendOrderEmail(Auth::user(), $order, $delivery_opt);

            $success['message'] = 'Voucher order place successfully.';
            $success['data'] = $order;
            return response()->json(['success' => true, 'response' => $success], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'response' => ['message' => $e->getMessage()]], 202);
        }
    }

    private function voucherDetailsStore($order, $voucher_id, $amount, $unique, $voucherDtl, $vqtys)
    {
        // Define mixed values based on the specific Voucher ID
        // Book ID 176 contains lower values, others contain higher values
        $values = ($voucher_id == 176) ? ["3", "5", "10", "18"] : ["20", "25", "36", "50", "72"];

        foreach ($values as $val) {
            OrderHistory::create([
                'order_id'      => $order->id,
                'voucher_id'    => $voucher_id,
                'number_voucher'=> $vqtys,
                'amount'        => 0, // Mixed books usually have a total cost, individual leaves are 0
                'o_unq'         => $unique,
                'mixed_value'   => $val,
                'status'        => "0"
            ]);
        }
    }


    private function updateExternalBalance($user, $totalDeduction)
    {
        try {
            Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                    'CreditProfileId'   => $user->CreditProfileId,
                    'CreditProfileName' => $user->name,
                    'AvailableBalance'  => 0 - $totalDeduction, // Send as a negative deduction
                    'comment'           => "Voucher Store Purchase",
                ]);
        } catch (\Exception $e) {
            \Log::error("External API Sync Failed: " . $e->getMessage());
        }
    }


    private function sendOrderEmail($user, $order, $delivery_opt)
    {
        $contact = \App\Models\ContactMail::first();
        $contactmail = $contact ? $contact->name : 'info@tevini.co.uk';
        
        $array = [
            'subject'         => 'Voucher books order confirmation',
            'from'            => 'info@tevini.co.uk',
            'cc'              => $contactmail,
            'name'            => $user->name,
            'client_no'       => $user->accountno,
            'order_id'        => $order->id,
            'orderid'         => $order->order_id,
            'delivery_option' => $delivery_opt
        ];

        $email = $user->email;

        Mail::send('mail.order', compact('array'), function($message) use ($array, $email) {
            $message->from($array['from'], 'Tevini.co.uk')
                    ->to($email)
                    ->cc($array['cc'])
                    ->subject($array['subject']);
        });
    }



    public function voucherUpdateByDonor(Request $request)
    {
        

        $vouchers = json_decode($request->vouchers, true); 

        $orderId = $request->order_id;
        $prepaid_amount = 0;
        $order_amount = 0;
        $all_zero = true;

            foreach ($vouchers as $item){

                if($item['qtys'] > Voucher::where('id',$item['voucherIds'])->first()->stock){
                    $success['message'] = 'One or some vouchers stock limit exceeded..';
                    return response()->json(['success'=>false,'response'=> $success], 202);
                    exit();
                }

            }

            foreach ($vouchers as $chkqty){
                if($chkqty['qtys'] != '0')
                    {
                        $all_zero = false;
                        break;
                    }
            }

            if($all_zero == true){
                $success['message'] = 'You didn\'t select any voucher..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
            }

            if($request->delivery == "false" && $request->collection == "false"){
                
                $success['message'] = 'Please select delivery option..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();

            }

            if($request->delivery != "false"){
                $delivery_opt = "Delivery";
            }elseif($request->collection != "false"){
                $delivery_opt = "Collection";
            }else{
                $delivery_opt = null;
            }

            foreach ($vouchers as $voucher){
                $order_amount+= Voucher::where('id',$voucher['voucherIds'])->first()->amount*$voucher['qtys'];

                $p_amount =  Voucher::where('id',$voucher['voucherIds'])->first()->amount;

                if(Voucher::where('id',$voucher['voucherIds'])->first()->type == "Prepaid"){
                    $prepaid_amount += $p_amount*$voucher['qtys'];
                    }
            }

            $u_bal = User::where('id',Auth::user()->id)->first()->balance;
            $overdrawn = (User::where('id',Auth::user()->id)->first()->overdrawn_amount);
            $limitChk = $u_bal + $overdrawn;

            if($limitChk < $prepaid_amount ){
                $success['message'] = 'Overdrawn limit exceed..';
                return response()->json(['success'=>false,'response'=> $success], 202);
            }

        $order = Order::find($orderId);

        if ($order->amount > 0) {
            $user = User::find($order->user_id);
            $user->increment('balance',$order->amount);
            $user->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 + $order->amount;
                $comment = "Voucher Update";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
        }



        $order->user_id = Auth::user()->id;
        $order->order_id = time() . "-" . Auth::user()->id;

        if ($prepaid_amount < 200) {

            if ($delivery_opt == "Delivery") {
                $order->amount = $prepaid_amount + 3.50;
                $order->delivery_charge = '3.50';
            } else {
                $order->amount = $prepaid_amount;
                $order->delivery_charge = '0';
            }
            
        } else {
            $order->amount = $prepaid_amount;
            $order->delivery_charge = '0';
        }
        
        $order->delivery_option = $delivery_opt;
        $order->notification = 1;
        $order->status = 0;
        if($order->save()){

            foreach($order->orderhistories as $orderhistory){
                $upVoucher = Voucher::where('id',$orderhistory->voucher_id)->first();
                $upVoucher->increment('stock',$orderhistory->number_voucher);
                $upVoucher->save();
                $orderhistory->delete();
            }
            
            $oldtrans = Usertransaction::where('order_id',$orderId)->update(['status' => 0]);
            
            $ppc = 0;
            foreach($vouchers as $vdata)
            {
                if($vdata['qtys'] != "0"){
                    if($vdata['qtys'] > "1"){

                        for($x = 0; $x < $vdata['qtys']; $x++)
                        {
                        $unique = time().rand(1,100);
                        //order history
                        $amount =  Voucher::where('id', $vdata['voucherIds'])->first()->amount;
                        $voucherDtl =  Voucher::where('id', $vdata['voucherIds'])->first();
                        $voucher_id = $vdata['voucherIds'];
                        $input['order_id'] = $order->id;
                        $input['voucher_id'] = $vdata['voucherIds'];
                        $input['number_voucher'] = 1;
                        $input['amount'] = $amount;
                        $input['o_unq'] = $unique;
                        $input['status'] = "0";
                        $vqtys = 1;
                            if ($voucherDtl->type == "Mixed") {
                                $this->voucherDetailsStore($order, $voucher_id, $amount, $unique, $voucherDtl, $vqtys);
                            } else {
                                OrderHistory::create($input);
                            }
                        }
                    }else{

                        $unique = time().rand(1,100);

                        //order history
                        $amount =  Voucher::where('id', $vdata['voucherIds'])->first()->amount;
                        $voucherDtl =  Voucher::where('id', $vdata['voucherIds'])->first();
                        $voucher_id = $vdata['voucherIds'];
                        $input['order_id'] = $order->id;
                        $input['voucher_id'] = $vdata['voucherIds'];
                        $input['number_voucher'] = $vdata['qtys'];
                        $input['amount'] = $vdata['qtys']*$amount;
                        $input['o_unq'] = $unique;
                        $input['status'] = "0";
                        $vqtys = $vdata['qtys'];

                        if ($voucherDtl->type == "Mixed") {
                            $this->voucherDetailsStore($order, $voucher_id, $amount, $unique, $voucherDtl, $vqtys);
                        } else {
                            OrderHistory::create($input);
                        }
                    }

                    //voucher stock decrement
                    $v = Voucher::find($vdata['voucherIds']);
                    $v->decrement('stock',$vdata['qtys']);
                    $v->save();

                    //user transaction out if voucher is/are prepaid
                    if(Voucher::where('id',$vdata['voucherIds'])->first()->type == "Prepaid"){
                        $utransaction = new Usertransaction();
                        $utransaction->t_id = time() . "-" . Auth::user()->id;
                        $utransaction->user_id = Auth::user()->id;
                        $utransaction->t_type = "Out";
                        $utransaction->amount =  $vdata['qtys']*$amount;
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
                if($delivery_opt == "Delivery"){
                    if ($prepaid_amount < 200) {
                        $udtransaction = new Usertransaction();
                        $udtransaction->t_id = time() . "-" . Auth::user()->id;
                        $udtransaction->user_id = Auth::user()->id;
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
                $user = User::find(Auth::user()->id);
                $user->decrement('balance',$order->amount);
                $user->save();

                // card balance update
                if (isset($user->CreditProfileId)) {
                    $CreditProfileId = $user->CreditProfileId;
                    $CreditProfileName = $user->name;
                    $AvailableBalance = 0 - $order->amount;
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

            VoucherCart::where('user_id', Auth::user()->id)->delete();
            $user = User::where('id',Auth::user()->id)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['subject'] = 'Order update to Tevini';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $array['name'] = $user->name;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['order_id'] = $order->id;
            $array['orderid'] = $order->id;
            $array['delivery_option'] = $delivery_opt;


            Mail::send('mail.order', compact('array'), function($message)use($array,$email) {
             $message->from($array['from'], 'Tevini.co.uk');
             $message->to($email)->cc($array['cc'])->subject($array['subject']);
            });


            $success['message'] = 'Voucher order update successfully.';
            $success['data'] = $order;
            $success['delivery'] = $delivery_opt;
            $success['prepaid_amount'] = $prepaid_amount;
            return response()->json(['success'=>true,'response'=> $success], 200);

        }

    }




    public function waiting_CompleteBydonor(Request $request)
    {
        if(empty($request->voucher_id)){
            $success['message'] = 'Voucher id not define.';
            return response()->json(['success'=>false,'response'=> $success], 202);
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

            // card balance update
            if (isset($donor->CreditProfileId)) {
                $CreditProfileId = $donor->CreditProfileId;
                $CreditProfileName = $donor->name;
                $AvailableBalance = 0 - $voucher->amount;
                $comment = "Waiting voucher complete by donor";
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
            $pstatus->completed_date = date('Y-m-d');
            $pstatus->save();

            }else {
            $pstatus = Provoucher::find($voucher_id);
            $pstatus->waiting = "No";
            $pstatus->save();
        
        }   
            
        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Waiting Voucher Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $charity->email;
        $array['charity'] = $charity;
        $array['amount'] = $voucher->amount;
        $array['voucher_number'] = $voucher->cheque_no;

        $m = Mail::to($email)
            ->cc($contactmail)
            ->send(new WaitingVComplete($array));
        
            $success['message'] = 'Waiting voucher complete successfully.';
            return response()->json(['success'=>true,'response'=> $success], 200);
            
    
    }


    public function waiting_CancelBydonor(Request $request)
    {
        if(empty($request->voucher_id)){
            $success['message'] = 'Voucher id not define.';
            return response()->json(['success'=>false,'response'=> $success], 202);
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

        $success['message'] = 'Waiting voucher cancel successfully.';
        return response()->json(['success'=>true,'response'=> $success], 200);
            

    }


    public function userOrderVoucherBookstoreCart(Request $request)
    {
        if ($request->cartid) {
            if(VoucherCart::destroy($request->cartid)){
            $success['message'] = 'Removed successfully.';
            return response()->json(['success'=>true,'response'=> $success], 200);
            }
        }else{
            
            $chkcart = VoucherCart::where('user_id', Auth::user()->id)->where('voucher_id', $request->voucherID)->first();

            if (isset($chkcart)) {
                $chkcart->qty = $chkcart->qty + 1;
                $chkcart->tamount =  $chkcart->qty * $request->v_amount;
                $chkcart->save();
                $success['message'] = 'Cart update successfully.';
                $success['data'] = $chkcart;
                return response()->json(['success'=>true,'response'=> $success], 200);
            } else {
                $data = new VoucherCart();
                $data->user_id = Auth::user()->id;
                $data->qty =  $request->quantity;
                $data->number_voucher =  $request->single_amount;
                $data->voucher_id =  $request->voucherID;
                $data->amount =  $request->v_amount;
                $data->tamount =  $request->quantity * $request->v_amount;
                $data->save();

                $success['message'] = 'Data added successfully.';
                $success['data'] = $data;
                $success['cartid'] = $data->id;
                return response()->json(['success'=>true,'response'=> $success], 200);
            
            }

        }
    }


    public function voucherEditByDonor($id)
    {
        $data = Order::where('id', $id)->first();
        
        $orderhistories = DB::table('order_histories')
            ->where('order_id', $id)
            ->select('voucher_id', DB::raw('SUM(number_voucher) as qty'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('voucher_id')
            ->get();

        $voucherTypes = DB::table('vouchers')
            ->whereIn('id', $orderhistories->pluck('voucher_id'))
            ->pluck('type', 'id');

        foreach ($orderhistories as $order) {
            if (isset($voucherTypes[$order->voucher_id]) && $voucherTypes[$order->voucher_id] === 'Mixed') {
                $order->qty = round($order->qty / 4);
                $order->total_amount /= 4;
            }
        }
        
        
        $success['message'] = 'Data found.';
        $success['order'] = $data;
        $success['order_details'] = $orderhistories;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }


    



}
