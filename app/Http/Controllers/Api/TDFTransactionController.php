<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMail;
use Illuminate\Http\Request;
use App\Models\TdfTransaction;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Mail\TDFTransfer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TDFTransactionController extends Controller
{
    public function getTDFtransaction()
    {
        $data = TdfTransaction::where([
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

    public function transferToTDF(Request $request)
    {
        if(empty($request->tdfaccount)){
            $success['message'] = 'Account Field Required.';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        // ✅ FIX: Validate numeric, minimum amount, and max 2 decimal places
        if(!is_numeric($request->tdfamount) || $request->tdfamount < 18){
            $success['message'] = 'Minimum transfer is £18.00.';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }
        
        // ✅ FIX: Use Regex for exact 2 decimal places validation
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $request->tdfamount)) {
            $success['message'] = 'Invalid amount format.';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        // ✅ FIX: Prepare email data OUTSIDE the transaction
        $userId = auth()->user()->id;
        $userName = auth()->user()->name;
        $userEmail = auth()->user()->email;
        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';

        try {
            // ✅ FIX: Wrap entire operation in DB transaction with row locking
            DB::transaction(function () use ($request, $userId) {
                
                // ✅ FIX: Lock donor row for concurrent safety
                $user = User::where('id', $userId)->lockForUpdate()->first();
                
                // ✅ FIX: Use User model's getAvailableLimit() method for accurate balance
                $donorBlanacewithLimit = $user->getAvailableLimit();

                if($request->tdfamount > $donorBlanacewithLimit){
                    throw new \Exception("You don't have enough balance to transfer.");
                }
                
                // Save TDF record
                $data = new TdfTransaction();
                $data->issue_date = date('Y-m-d');
                $data->user_id = $userId;
                $data->tdfaccount = $request->tdfaccount;
                $data->tdf_amount = $request->tdfamount;
                $data->current_dollar_amount = $request->tdfamount;
                $data->save();

                // Decrement balance
                $user->balance = $user->balance - $request->tdfamount;
                $user->save();

                // Save Usertransaction
                $udtransaction = new Usertransaction();
                $udtransaction->t_id =  "TDF-" . uniqid() . "-" . $userId;
                $udtransaction->user_id = $userId;
                $udtransaction->t_type = "Out";
                $udtransaction->amount =  $request->tdfamount;
                $udtransaction->t_unq = uniqid().rand(1,100);
                $udtransaction->title ="Transfer to TDF (App)";
                $udtransaction->status =  1;
                $udtransaction->save();

                // Save Transaction
                $transaction = new Transaction();
                $transaction->t_id =  "TDF-" . uniqid() . "-" . $userId;
                $transaction->user_id = $userId;
                $transaction->t_type = "Out";
                $transaction->amount =  $request->tdfamount;
                $transaction->note ="Transfer to TDF (App)";
                $transaction->status =  1;
                $transaction->save();
            });

            // ✅ FIX: Email sent completely outside transaction scope
            try {
                $array['name'] = $userName;
                $array['subject'] = 'TDF transferred successfully.';
                $array['from'] = 'info@tevini.co.uk';
                $array['cc'] = $contactmail;
                
                Mail::to($userEmail)
                    ->cc($contactmail)
                    ->send(new TDFTransfer($array));
            } catch (\Exception $e) {
                Log::error('API TDF Transfer email failed for user ' . $userId . ': ' . $e->getMessage());
            }

            $success['message'] = 'TDF transferred successfully.';
            $success['balance'] = User::find($userId)->balance;
            return response()->json(['success'=>true,'response'=> $success], 200);

        } catch (\Exception $e) {
            // Log the actual error to find the issue
            Log::error('API TDF Transfer failed for user ' . $userId . ': ' . $e->getMessage());
            
            $errorMessage = $e->getMessage() === "You don't have enough balance to transfer." 
                ? $e->getMessage() 
                : "Server error!!";

            $success['message'] = $errorMessage;
            return response()->json(['success'=>false,'response'=> $success], 400);
        }
    }
}