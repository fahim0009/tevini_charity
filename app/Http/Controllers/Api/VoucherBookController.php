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

use App\Mail\InstantReport;
use App\Mail\PendingvReport;
use App\Mail\WaitingvoucherReport;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use PDF;

use Illuminate\Support\Facades\Http;

class VoucherBookController extends Controller
{
    public function userOrderVoucherBook()
    {
        $data = Voucher::where('status','=','1')->get();
        
        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }

    public function storeVoucher(Request $request)
    {

        
        $vouchers = json_decode($request->vouchers, true); 

        $prepaid_amount= 0;
        $order_amount= 0;
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

            if($request->delivery == "true"){
                $delivery_opt = "Delivery";
            }elseif($request->collection == "true"){
                $delivery_opt = "Collection";
            }else{
                $delivery_opt = null;
            }

            foreach($voucher_ids as $key => $id){
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

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->order_id = time() . "-" . Auth::user()->id;
        $order->amount = $prepaid_amount;
        $order->delivery_option = $delivery_opt;
        $order->notification = 1;
        $order->status = 0;
        if($order->save()){



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
                    $utransaction->t_id = time() . "-" . Auth::user()->id;
                    $utransaction->user_id = Auth::user()->id;
                    $utransaction->t_type = "Out";
                    $utransaction->amount =  $qtys[$key]*$amount;
                    $utransaction->t_unq = $unique;
                    $utransaction->order_id = $order->id;
                    $utransaction->title ="Prepaid Voucher Book";
                    $utransaction->status =  1;
                    $utransaction->save();
                }

                }
            }

            if($prepaid_amount !=0){
                $user = User::find(Auth::user()->id);
                $user->decrement('balance',$prepaid_amount);
                $user->save();

                // card balance update
                // if (isset($user->CreditProfileId)) {
                //     $CreditProfileId = $user->CreditProfileId;
                //     $CreditProfileName = $user->name;
                //     $AvailableBalance = 0 - $prepaid_amount;
                //     $comment = "Voucher Store";
                //     $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                //         ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                //             'CreditProfileId' => $CreditProfileId,
                //             'CreditProfileName' => $CreditProfileName,
                //             'AvailableBalance' => $AvailableBalance,
                //             'comment' => $comment,
                //         ]);
                // }
                // card balance update end
            }

            $user = User::where('id',Auth::user()->id)->first();

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


            $success['message'] = 'Voucher order place successfully.';
            $success['data'] = $order;
            return response()->json(['success'=>true,'response'=> $success], 200);

        }


    }
}
