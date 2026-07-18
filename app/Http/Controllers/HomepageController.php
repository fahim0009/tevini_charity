<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\Campaign;
use App\Models\Charity;
use App\Models\CompanyDetail;
use App\Models\FingerprintDonation;
use App\Models\Gateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\{DB, Log};

class HomepageController extends Controller
{
    public function index()
    {
        // return view('frontend.maintenance');
        return view('frontend.index');
    }

    public function indexV2()
    {
        return view('frontend.indexv2');
    }

    public function news()
    {
        return view('news.index');
    }
    public function faq()
    {
        return view('faq.index');
    }

    public function userNews()
    {
        return view('frontend.user.news');
    }

    public function userfaq()
    {
        return view('frontend.user.faq');
    }

    public function inviteFriend()
    {
        return view('frontend.user.invitefriend');
    }
    public function userSettings()
    {
        return view('frontend.user.settings');
    }

    public function adminSettings()
    {
        // Get the single company detail row
        $companyDetail = CompanyDetail::first(); 
        
        return view('setting.index', compact('companyDetail'));
    }
    public function updateAutoPaymentTime(Request $request)
    {
        $request->validate([
            'auto_payment_time' => 'required|date_format:H:i',
        ]);

        // Since there is only one record, we grab the first one and update it
        $companyDetail = CompanyDetail::first();
        
        if ($companyDetail) {
            $companyDetail->update([
                'auto_payment_time' => $request->auto_payment_time
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'Auto payment time updated successfully.');
    }

    public function tdf()
    {
        return view('frontend.tdf');
    }

    public function howitWorks()
    {
        return view('frontend.how-it-works');
    }

    public function apidonation()
    {
        return view('frontend.thirdpartydonation');
    }

    public function card()
    {
        return view('frontend.user.card');
    }



    public function apidonationCheck2(Request $request)
    {
        // 1. Initial Validation
        if (empty($request->password)) {
            return response()->json([
                'status' => 303,
                'message' => '<span id="msg" style="color: rgb(255, 0, 0);">Enter password</span>'
            ]);
        }

        // 2. Fetch required data before entering transaction
        $campaign_dtls = Campaign::findOrFail($request->tevini_campaignid);
        $gateway = Gateway::findOrFail($request->identifier);
        $return_url = $gateway->return_url;
        
        // Clean and cast the amount to prevent "Non-numeric" crash
        $amt = (float) str_replace(',', '', $request->amt ?? 0);

        // 3. Attempt Authentication
        if (auth()->attempt(['accountno' => $request->acc, 'password' => $request->password])) {
            
            // Wrap everything in a transaction to ensure data integrity
            return DB::transaction(function () use ($request, $campaign_dtls, $amt, $return_url) {
                
                /** @var \App\Models\User $user */
                $user = auth()->user(); 
                
                $amt = (float) $amt; 
                if ($user->getAvailableLimit() < $amt) {
                    return response()->json([
                        'status' => 303,
                        'message' => '<span id="msg" style="color: rgb(255, 0, 0);">Insufficient funds. Overdrawn limit exceeded.</span>'
                    ]);
                }

                // 4. Create User Transaction Record
                $utransaction = new Usertransaction();
                $utransaction->t_id = time() . "-" . $user->id;
                $utransaction->charity_id = $campaign_dtls->charity_id;
                $utransaction->user_id = $user->id;
                $utransaction->t_type = "Out";
                $utransaction->amount = $amt;
                $utransaction->note = $request->comment;
                $utransaction->title = "Online Campaign (" . $campaign_dtls->campaign_title . ")";
                $utransaction->gateway_id = $request->identifier;
                $utransaction->campaign_id = $campaign_dtls->id;
                $utransaction->status = 1;
                $utransaction->save();

                // 5. Update User Balance (Safe Numeric Update)
                // Note: decrement() saves to DB automatically. No need for $user->save().
                $user->decrement('balance', $amt);

                // 7. Update Charity Balance
                $charity = Charity::findOrFail($campaign_dtls->charity_id);
                $charity->increment('balance', $amt);

                // 8. Generate Success Hash and URL
                $success_params = [
                    'campaign' => $request->tevini_campaignid,
                    'transid'  => $request->transid,
                    'cid'      => $request->acc,
                    'donation' => $amt,
                    'intid'    => $utransaction->id,
                    'rtncode'  => 0
                ];
                
                $success_query = "?" . http_build_query($success_params);
                $tevini_hash = hash_hmac("sha256", $success_query, $campaign_dtls->hash_code);
                $success_url = $return_url . $success_query . "&hash=" . $tevini_hash;

                return response()->json([
                    'status' => 300,
                    'url' => $success_url,
                    'message' => '<span id="msg" style="color: rgb(0,128,0);">Donation complete successfully</span>'
                ]);
            });

        } else {
            // 9. Handle Authentication Failure
            $user_tran = time();
            $unsuccess_params = [
                'campaign' => $request->tevini_campaignid,
                'transid'  => $request->transid,
                'cid'      => $request->acc,
                'donation' => $amt,
                'intid'    => $user_tran,
                'rtncode'  => 1
            ];

            $unsuccess_query = "?" . http_build_query($unsuccess_params);
            $tevini_hash = hash_hmac("sha256", $unsuccess_query, $campaign_dtls->hash_code);
            $unsuccess_url = $return_url . $unsuccess_query . "&hash=" . $tevini_hash;

            return response()->json([
                'status' => 301,
                'url' => $unsuccess_url,
                'message' => '<span id="msg" style="color: rgb(255, 0, 0);">Incorrect account number or password</span>'
            ]);
        }
    }


    public function apidonationCheck(Request $request)
    {
        // 1. Initial Validation
        if (empty($request->password)) {
            return response()->json([
                'status' => 303,
                'message' => '<span id="msg" style="color: rgb(255, 0, 0);">Enter password</span>'
            ]);
        }

        // 2. Fetch required data
        try {
            $campaign_dtls = Campaign::findOrFail($request->tevini_campaignid);
            $gateway = Gateway::findOrFail($request->identifier);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Campaign/Gateway Not Found", ['request' => $request->all()]);
            return response()->json(['status' => 303, 'message' => 'Invalid campaign or gateway.']);
        }
        
        $return_url = $gateway->return_url;
        
        // Clean and cast the amount
        $amt = (float) str_replace(',', '', $request->amt ?? 0);

        // 3. Attempt Authentication
        if (auth()->attempt(['accountno' => $request->acc, 'password' => $request->password])) {
            
            /** @var \App\Models\User $user */
            $user = auth()->user(); 
            Log::info("Donation Auth Success", ['user_id' => $user->id, 'amount' => $amt]);

            // 4. Check Balance BEFORE opening a transaction (Saves DB resources)
            if ($user->getAvailableLimit() < $amt) {
                Log::warning("Donation Failed: Insufficient Funds", [
                    'user_id' => $user->id, 
                    'requested_amount' => $amt, 
                    'available_limit' => $user->getAvailableLimit()
                ]);
                return response()->json([
                    'status' => 303,
                    'message' => '<span id="msg" style="color: rgb(255, 0, 0);">Insufficient funds. Overdrawn limit exceeded.</span>'
                ]);
            }

            // 5. Wrap DB operations in a transaction
            return DB::transaction(function () use ($request, $campaign_dtls, $amt, $return_url, $user) {
                
                // Create User Transaction Record
                $utransaction = new Usertransaction();
                $utransaction->t_id = time() . "-" . $user->id;
                $utransaction->charity_id = $campaign_dtls->charity_id;
                $utransaction->user_id = $user->id;
                $utransaction->t_type = "Out";
                $utransaction->amount = $amt;
                $utransaction->note = $request->comment;
                $utransaction->title = "Online Campaign (" . $campaign_dtls->campaign_title . ")";
                $utransaction->gateway_id = $request->identifier;
                $utransaction->campaign_id = $campaign_dtls->id;
                $utransaction->status = 1;
                $utransaction->save();

                // ⚠️ REMOVED: $user->decrement('balance', $amt); 
                // Because getLiveBalance() calculates from transactions dynamically, 
                // saving this "Out" transaction is enough!

                // Update Charity Balance
                $charity = Charity::findOrFail($campaign_dtls->charity_id);
                $charity->increment('balance', $amt);

                // Generate Success Hash and URL
                $success_params = [
                    'campaign' => $request->tevini_campaignid,
                    'transid'  => $request->transid,
                    'cid'      => $request->acc,
                    'donation' => $amt,
                    'intid'    => $utransaction->id,
                    'rtncode'  => 0
                ];
                
                $success_query = "?" . http_build_query($success_params);
                $tevini_hash = hash_hmac("sha256", $success_query, $campaign_dtls->hash_code);
                $success_url = $return_url . $success_query . "&hash=" . $tevini_hash;

                Log::info("Donation Success & Committed", ['transaction_id' => $utransaction->id, 'amount' => $amt]);

                return response()->json([
                    'status' => 300,
                    'url' => $success_url,
                    'message' => '<span id="msg" style="color: rgb(0,128,0);">Donation complete successfully</span>'
                ]);
            });

        } else {
            // 6. Handle Authentication Failure
            Log::warning("Donation Auth Failed", ['account' => $request->acc, 'campaign_id' => $request->tevini_campaignid]);
            
            $user_tran = time();
            $unsuccess_params = [
                'campaign' => $request->tevini_campaignid,
                'transid'  => $request->transid,
                'cid'      => $request->acc,
                'donation' => $amt,
                'intid'    => $user_tran,
                'rtncode'  => 1
            ];

            $unsuccess_query = "?" . http_build_query($unsuccess_params);
            $tevini_hash = hash_hmac("sha256", $unsuccess_query, $campaign_dtls->hash_code);
            $unsuccess_url = $return_url . $unsuccess_query . "&hash=" . $tevini_hash;

            return response()->json([
                'status' => 301,
                'url' => $unsuccess_url,
                'message' => '<span id="msg" style="color: rgb(255, 0, 0);">Incorrect account number or password</span>'
            ]);
        }
    }
        
    protected function generateUniqueCode()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $unique = false;
        $code = '';

        while (!$unique) {
            $code = '';
            for ($i = 0; $i < 16; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Check if the code already exists in the database
            $existingCode = User::where('hid', $code)->first();
            
            if (!$existingCode) {
                $unique = true;
            }
        }

        return $code;
    }


    public function cardEnrolFingerprint(Request $request)
    {
        
        $chkaccount = User::where('accountno',$request->account)->first();

        if(empty($chkaccount)){
            $reason ='User account not found';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }

        if(auth()->attempt(array('accountno' => $request->account, 'password' => $request->pwd)))
        {
            $code = $this->generateUniqueCode();
            
            $data = User::find($chkaccount->id);
            $data->fingerprint = $request->fingerprint;
            $data->hid = $code;
            $data->save();

            // $tevini_hash =  hash('sha256', $code);
            $tevini_hash = encrypt($code);

            $reason ='Fingerprint assigned to user';
            return response()->json(['status'=> 'success','http code'=> 200,'reason'=>$reason,'HID'=>$tevini_hash]);
            exit();
        }else {
            $reason ='User not authenticated';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }
    }



    public function cardFingerprintDonation(Request $request)
    {

        $campaign = $request->campaign;
        $hash = $request->hash;
        $fingerprint = $request->fingerprint;
        $HID = decrypt($request->HID);
        // $HID = $request->HID;
        $amount = (float)($request->Amount ?? 0); 
        $FunddAccountNumber = $request->FunddAccountNumber;

        $uniqueId = rand(100000, 999999);
            // Ensure uniqueness by checking the database (optional)
        while (Usertransaction::where('t_id', $uniqueId)->exists()) {
            $uniqueId = rand(100000, 999999);
        }

        $chkuser = User::where('hid',$HID)->first();



        if(empty($chkuser)){
            $reason ='No matching HID found';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }

        $campaign_dtls = Campaign::where('id',$campaign)->first();
        if(empty($campaign_dtls)){
            $reason ='Invalid campaign details provided';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }

        

        if($chkuser)
        {
            
            $donor_id = $chkuser->id;
            $limitChk = $chkuser->getAvailableLimit() ?? 0;

            if($limitChk < $amount ){
                $reason ='Overdrawn limit exceed.';
                return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
                exit();
            }

            $utransaction = new Usertransaction();
            $utransaction->t_id = $uniqueId;
            $utransaction->charity_id = $campaign_dtls->charity_id;
            $utransaction->user_id = $donor_id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $amount;
            $utransaction->note =  $request->comment;
            $utransaction->title ="Online Campaign (".$campaign_dtls->campaign_title.") - Fingerprint Donation";
            // $utransaction->gateway_id =  $request->identifier;
            $utransaction->campaign_id =  $campaign_dtls->id;
            $utransaction->status =  1;
            $utransaction->save();

            
            $user = User::find($donor_id);
            if ($amount > 0) {
                $user->decrement('balance', $amount);
            }
            $user->save();

            $ch = Charity::find($campaign_dtls->charity_id);
            $ch->increment('balance',$amount);
            $ch->save();
            
            $fingerData = new FingerprintDonation();
            $fingerData->date = date('Y-m-d');
            $fingerData->user_id = $donor_id;
            $fingerData->campaign = $campaign;
            $fingerData->hash = $hash;
            $fingerData->fingerprint = $fingerprint;
            $fingerData->hid = $HID;
            $fingerData->amount = $amount;
            $fingerData->FunddAccountNumber = $FunddAccountNumber;
            $fingerData->title = "Fingerprint Donation";
            $fingerData->save();


            $reason ='Donation completed successfully';
            return response()->json(['status'=> 'success','reason'=>$reason,'intid'=>$utransaction->t_id]);
            exit();


        }else{

            $reason ='No matching HID found';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }
    }


    public function cardIsFingerprintUserEnrolled(Request $request)
    {
        
        $chkaccount = User::where('accountno',$request->account)->first();

        if(empty($chkaccount)){
            $reason ='User account not found';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }

        if(auth()->attempt(array('accountno' => $request->account, 'password' => $request->pwd)))
        {

            $user = User::where('accountno', $request->account)->first();

            if (($user->hid) && ($user->fingerprint)) {
                $reason ='Enrolment details provided';
                return response()->json(['status'=> 'success','http code'=> 200,'enrolled'=>true,'reason'=>$reason]);
                exit();
            } else {
                $reason ='Enrolment details not provided';
                return response()->json(['status'=> 'success','http code'=> 400,'enrolled'=>false,'reason'=>$reason]);
                exit();
            }
            
            
        }else {
            
            $reason ='User not authenticated';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }
    }

    public function cardDeregisterFingerprint(Request $request)
    {
        
        $chkaccount = User::where('accountno',$request->account)->first();

        if(empty($chkaccount)){
            $reason ='User account not found';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }

        if(auth()->attempt(array('accountno' => $request->account, 'password' => $request->pwd)))
        {

            $user = User::find($chkaccount->id);
            $user->fingerprint = null;
            $user->hid = null;
            if ($user->save()) {
                $reason ='Fingerprint has been deregistered';
                return response()->json(['status'=> 'success','http code'=> 200,'reason'=>$reason]);
                exit();
            } else {
                $reason ='Deregister failed';
                return response()->json(['status'=> 'success','http code'=> 400,'reason'=>$reason]);
                exit();
            }
            
            
        }else {
            
            $reason ='User not authenticated';
            return response()->json(['status'=> 'rejected','http code'=> 400,'reason'=>$reason]);
            exit();
        }
    }


}
