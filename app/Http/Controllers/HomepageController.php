<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\Campaign;
use App\Models\Charity;
use App\Models\FingerprintDonation;
use App\Models\Gateway;
use Illuminate\Support\Facades\Http;

class HomepageController extends Controller
{
    public function index()
    {
        // return view('frontend.maintenance');
        return view('frontend.index');
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
        return view('setting.index');
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


    public function apidonationCheck(Request $request)
    {

        if(empty($request->password)){
            $message ='<span id="msg" style="color: rgb(255, 0, 0);">Enter password</span>';
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $campaign_dtls =Campaign::where('id',$request->tevini_campaignid)->first();
        $return_url = Gateway::where('id', $request->identifier)->first()->return_url;

        if(auth()->attempt(array('accountno' => $request->acc, 'password' => $request->password)))
        {

            $u_bal = User::where('accountno',$request->acc)->first()->balance;
            $donor_id = User::where('accountno',$request->acc)->first()->id;

            $overdrawn = (User::where('id',$donor_id)->first()->overdrawn_amount);
            $limitChk = $u_bal + $overdrawn;

            if($limitChk < $request->amt ){
                $message ='<span id="msg" style="color: rgb(255, 0, 0);">Overdrawn limit exceed.</span>';
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }



            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $donor_id;
            $utransaction->charity_id = $campaign_dtls->charity_id;
            $utransaction->user_id = $donor_id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $request->amt;
            $utransaction->note =  $request->comment;
            $utransaction->title ="Online Campaign (".$campaign_dtls->campaign_title.")";
            $utransaction->gateway_id =  $request->identifier;
            $utransaction->campaign_id =  $campaign_dtls->id;
            $utransaction->status =  1;
            $utransaction->save();

            $amount = (float)($request->amt ?? 0); 
            $user = User::find($donor_id);
            if ($amount > 0) {
                $user->decrement('balance', $amount);
            }
            $user->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->amt;
                $comment = "third party donation by tevini";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            $ch = Charity::find($campaign_dtls->charity_id);
            $ch->increment('balance',$request->amt);
            $ch->save();

            $success_hash = "?campaign=".$request->tevini_campaignid."&transid=".$request->transid."&cid=".$request->acc."&donation=".$request->amt."&intid=".$utransaction->id."&rtncode=0";

            $tevini_hash = hash_hmac("sha256", $success_hash, $campaign_dtls->hash_code);

            $success_url = $return_url.$success_hash."&hash=".$tevini_hash;

            $message ='<span id="msg" style="color: rgb(0,128,0);">Donation complete successfully</span>';
            return response()->json(['status'=> 300,'url'=> $success_url,'message'=>$message]);


        }else{

            $user_tran = time();

            $unsuccess_hash = "?campaign=".$request->tevini_campaignid."&transid=".$request->transid."&cid=".$request->acc."&donation=".$request->amt."&intid=".$user_tran."&rtncode=1";

            $tevini_hash = hash_hmac("sha256", $unsuccess_hash, $campaign_dtls->hash_code);

            $unsuccess_url = $return_url.$unsuccess_hash."&hash=".$tevini_hash;

            $message ='<span id="msg" style="color: rgb(255, 0, 0);">Incorrect account number or password</span>';
            return response()->json(['status'=> 301,'url'=> $unsuccess_url,'message'=>$message]);
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

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $amount;
                $comment = "third party donation by tevini";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            

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
