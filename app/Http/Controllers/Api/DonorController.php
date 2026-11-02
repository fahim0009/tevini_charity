<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Support\Facades\Mail;
use App\Models\Provoucher;
use App\Models\User;
use App\Models\Donation;
use App\Models\StandingDonation;
use App\Models\StandingdonationDetail;
use App\Models\OverdrawnRecord;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Charity;
use App\Models\ContactMail;
use App\Mail\TopupReport;
use App\Mail\DonationReport;
use App\Mail\DonationstandingReport;
use App\Mail\DonerReport;
use App\Mail\DonationreportCharity;
use PDF;
use Hash;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DonorController extends Controller
{
    public function userDonationStore(Request $request)
    {
        if ((empty($request->charity_id))) {
            $success['message'] = 'Please select beneficiary field..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if ((empty($request->amount))) {
            $success['message'] = 'Please fill amount field..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if ($request->c_donation == "false") {
            $success['message'] = 'Please accept condition..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if(($request->standard == "true") && ($request->payments_type == "1") && (empty($request->number_payments))){
            $success['message'] = 'You have to fill number of payment field!.';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        // ✅ FIX: Prepare email data OUTSIDE the transaction
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';
        $charityName = Charity::where('id', $request->charity_id)->value('name');

        try {
            // ✅ FIX: Wrap entire operation in DB transaction with row locking
            DB::transaction(function () use ($request, $userId, $user, $contactmail, $charityName) {
                
                // ✅ FIX: Lock donor row for concurrent safety
                $lockedUser = User::where('id', $userId)->lockForUpdate()->first();
                
                // ✅ FIX: Use User model's getAvailableLimit() method for accurate balance
                $donorBlanacewithLimit = $lockedUser->getAvailableLimit();

                if ($donorBlanacewithLimit < $request->amount) {
                    throw new \Exception("You don't have sufficient balance for this donation.");
                }

                $data = new Donation;
                $data->user_id = $userId;
                $data->charity_id = $request->charity_id;
                $data->amount = $request->amount;
                $data->currency = "GBP";
                $data->ano_donation = $request->ano_donation;
                $data->standing_order = $request->standard;
                $data->confirm_donation = $request->c_donation;
                $data->charitynote = $request->charitynote;
                $data->mynote = $request->mynote;
                $data->notification = 1;
                $data->status = 0;
                $data->save();

                $utransaction = new Usertransaction();
                // ✅ FIX: Use prefixed uniqid() for t_id
                $utransaction->t_id = "DON-" . uniqid() . "-" . $userId;
                $utransaction->user_id = $userId;
                $utransaction->charity_id = $request->charity_id;
                $utransaction->donation_id = $data->id;
                $utransaction->t_type = "Out";
                $utransaction->amount =  $request->amount;
                $utransaction->title =  "Online Donation";
                $utransaction->status =  1;
                $utransaction->save();

                $lockedUser->decrement('balance', $request->amount);

                $charity = Charity::find($request->charity_id);
                $charity->increment('balance', $request->amount);

                // ✅ FIX: Email sent completely outside transaction scope (DB::afterCommit)
                $email = $lockedUser->email;
                $array = [
                    'name' => $lockedUser->name,
                    'cc' => $contactmail,
                    'client_no' => $lockedUser->accountno,
                    'amount' => $request->amount,
                    'charity_note' => $request->charitynote,
                    'charity_name' => $charityName
                ];

                DB::afterCommit(function () use ($array, $email) {
                    try {
                        Mail::to($email)
                            ->cc($array['cc'])
                            ->send(new DonationReport($array));
                    } catch (\Exception $e) {
                        Log::error('Donation email failed for user ' . $email . ': ' . $e->getMessage());
                    }
                });
            });

            $success['message'] = 'Donation create Successfully.';
            return response()->json(['success'=>true,'response'=> $success], 200);

        } catch (\Exception $e) {
            Log::error('Donation store failed for user ' . $userId . ': ' . $e->getMessage());
            
            $errorMessage = $e->getMessage() === "You don't have sufficient balance for this donation." 
                ? $e->getMessage() 
                : "Server error!!";

            $success['message'] = $errorMessage;
            return response()->json(['success'=>false,'response'=> $success], 400);
        }
    }


    public function userDonationrecod()
    {
        $data = Donation::with(['charity:id,name'])->where([
            ['standing_order','=', 'false'],
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

    public function userStandingrecod()
    {
        $data = StandingDonation::with('standingdonationDetail')
            ->withCount(['standingdonationDetail as donations_count'])
            ->where([
                ['user_id', '=', auth()->user()->id]
            ])
            ->orderBy('id', 'DESC')
            ->get();

        Log::info('User standing donation records retrieved.', ['data' => $data]);

        $success['data'] = $data;
        return response()->json(['success' => true, 'response' => $success], 200);
    }

    public function usersingleStanding($id)
    {

        $singleStddonation = StandingdonationDetail::where([
            ['standing_donation_id','=', $id],
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        Log::info('User standing donation records details retrieved.', ['singleStddonation' => $singleStddonation]);

        $success['data'] = $singleStddonation;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }


    public function activeStandinguser_old(Request $request)
    {

        if($request->status==1){
            $active = StandingDonation::where([
                ['id','=', $request->id],
                ['user_id','=', Auth::user()->id],
            ])->first();
            $active->status = $request->status;
            $active->save();

            $success['message'] = 'Active Successfully.';
            $success['data'] = $active;
            return response()->json(['success'=>true,'response'=> $success], 200);

        }else{
            $deactive = StandingDonation::where([
                ['id','=', $request->id],
                ['user_id','=', Auth::user()->id],
            ])->first();
            $deactive->status = $request->status;
            $deactive->save();
            
            $success['message'] = 'Inactive Successfully.';
            $success['data'] = $deactive;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }

    }

    public function activeStandinguser(Request $request)
    {
        // Find the record and verify ownership
        $donation = StandingDonation::where('id', $request->id)
                                    ->where('user_id', Auth::user()->id)
                                    ->first();

        if (!$donation) {
            return response()->json([
                'success' => false, 
                'message' => 'Record not found.'
            ], 404);
        }

        if ($request->status == 1) {
            // 1. Deactivate the old record so it's no longer "Active"
            // This prevents the "double data" issue in your active view
            $donation->status = 0;
            $donation->save();

            // 2. Create a new copy of that record
            $newDonation = $donation->replicate();
            
            // 3. Set properties for the NEW record
            $newDonation->status = 1;
            $newDonation->starting = now()->format('Y-m-d');
            $newDonation->created_at = now();
            $newDonation->save();

            $success['message'] = 'New donation activated successfully.';
            $success['data'] = $newDonation;
            
            return response()->json(['success' => true, 'response' => $success], 200);

        } else {
            // Standard deactivation (turning it off)
            $donation->status = 0;
            $donation->save();
            
            $success['message'] = 'Inactive Successfully.';
            $success['data'] = $donation;
            
            return response()->json(['success' => true, 'response' => $success], 200);
        }
    }

    // this function is use for mobile app
    public function getMakeDonation()
    {
        if (Auth::check()) {
            Log::info('getMakeDonation called for user ID: ' . Auth::id());
        } else {
            Log::info('getMakeDonation called, but user is NOT authenticated.');
        }
        
        return view('frontend.user.donationapp');
    }

    
    public function makeDonationAppMessage()
    {
        return view('frontend.user.donationsuccess');
    }
    // this function is use for mobile app

   
}
