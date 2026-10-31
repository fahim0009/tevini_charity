<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TDFCompleteMail;
use App\Models\ContactMail;
use App\Models\TdfTransaction;
use App\Models\User;
use App\Models\Usertransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TDFTransactionController extends Controller
{
    public function getTDFTransaction(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->where('status', 0)->get();
        return view('admin.tdf.transaction', compact('data'));
    }

    public function getTDFTransactionComplete(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->where('status', 1)->get();
        return view('admin.tdf.completetransaction', compact('data'));
    }

    public function getTDFTransactionCancel(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->where('status', 3)->get();
        return view('admin.tdf.canceltransaction', compact('data'));
    }

    public function tdfBlanceStore(Request $request)
    {
        
        $data = TdfTransaction::findOrFail($request->tdfid);
        $data->payment_date = date('Y-m-d');
        $data->payment_dollar_amount = $request->amount;
        $data->status = "1";
        if($data->save()){

            $message ="Data Update Successfully";

        return redirect()->route('tdfTransaction')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);
    }

    public function changeStatus(Request $request)
    {
        try {
            // ✅ FIX: Wrap status lookup, cancellation check, and refund inside DB transaction with row locking
            return DB::transaction(function () use ($request) {
                
                // ✅ FIX: Lock TDF row to prevent concurrent status changes
                $order = TdfTransaction::where('id', $request->tdfid)->lockForUpdate()->first();
                
                if (!$order) {
                    return response()->json(['status' => 303, 'message' => 'TDF Transaction not found.']);
                }

                $user_id = $order->user_id;
                $tdf_amount = $order->tdf_amount;
                $newStatus = $request->status;
                $oldStatus = $order->status;

                // ✅ FIX: Prevent refunds/cancellations from completed status
                $allowedTransitions = [
                    0 => [1, 3], // Pending -> Complete, Cancel
                    1 => [],     // Complete -> (No transitions allowed, no refund!)
                    3 => [],     // Cancel -> (No transitions allowed)
                ];

                if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
                    return response()->json([
                        'status' => 303,
                        'message' => "<div class='alert alert-danger'><b>Invalid status transition. Cannot cancel a completed transfer.</b></div>"
                    ]);
                }

                $order->status = $newStatus;
                $order->save();

                if ($newStatus == "3") {
                    // Cancel & Refund: Lock user and increment balance
                    $user = User::where('id', $user_id)->lockForUpdate()->first();
                    $user->increment('balance', $tdf_amount);
                    $user->save();

                    $udtransaction = new Usertransaction();
                    $udtransaction->t_id = "TDF-RET-" . uniqid() . "-" . $user_id;
                    $udtransaction->user_id = $user_id;
                    $udtransaction->t_type = "In";
                    $udtransaction->amount =  $tdf_amount;
                    $udtransaction->t_unq = uniqid() . rand(1, 100);
                    $udtransaction->title = "Return from TDF";
                    $udtransaction->status =  1;
                    $udtransaction->save();
                }

                // ✅ FIX: Email sent completely outside transaction scope (DB::afterCommit)
                if ($newStatus == 1) {
                    DB::afterCommit(function () use ($user_id) {
                        $donor = User::find($user_id);
                        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';
                        $array['subject'] = 'TDF transfer complete';
                        $array['from'] = 'info@tevini.co.uk';
                        $array['cc'] = $contactmail;
                        $array['name'] = $donor->name;
                        $email = $donor->email;

                        try {
                            Mail::to($email)
                                ->cc($contactmail)
                                ->send(new TDFCompleteMail($array));
                        } catch (\Exception $e) {
                            Log::error('TDF Complete email failed for user ' . $user_id . ': ' . $e->getMessage());
                        }
                    });
                }

                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Status change successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            });
        } catch (\Exception $e) {
            Log::error('Admin TDF status change failed for TDF ID ' . $request->tdfid . ': ' . $e->getMessage());
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Server Error!!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }
    }
}