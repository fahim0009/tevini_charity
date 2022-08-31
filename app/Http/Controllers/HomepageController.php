<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\Campaign;
use App\Models\Charity;

class HomepageController extends Controller
{
    public function index()
    {
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

    public function apidonation()
    {
        return view('frontend.thirdpartydonation');
    }


    public function apidonationCheck(Request $request)
    {

        if(empty($request->password)){
            $message ='<span id="msg" style="color: rgb(255, 0, 0);">Enter password</span>';
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $campaign_dtls =Campaign::where('id',$request->campaignid)->first();

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

            // if($u_bal < $request->amt){
            //     $message ='<span id="msg" style="color: rgb(255, 0, 0);">Insufficient balance</span>';
            //     return response()->json(['status'=> 303,'message'=>$message]);
            //     exit();
            // }

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $donor_id;
            $utransaction->user_id = $donor_id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $request->amt;
            $utransaction->note =  $request->comment;
            $utransaction->title ="Online Campaign (".$campaign_dtls->campaign_title.")";
            $utransaction->status =  1;
            $utransaction->save();

            $user = User::find($donor_id);
            $user->decrement('balance',$request->amt);

            $ch = Charity::find($campaign_dtls->charity_id);
            $ch->increment('balance',$request->amt);

            $s_hash = "?campaignid=".$request->campaignid."&transid=".$request->transid."&acc=".$request->acc."&amt=".$request->amt."&intid=".$utransaction->id."&rtncode=0";

            $tevini_hash1 = hash_hmac("sha256", $s_hash, $campaign_dtls->hash_code);

            $success_url = "https://api.charidy.com/api/v1/campaign/donation/statusupdate/tevini".$s_hash."&hash=".$tevini_hash1;

            $message ='<span id="msg" style="color: rgb(0,128,0);">Donation complete successfully</span>';
            return response()->json(['status'=> 300,'url'=> $success_url,'message'=>$message]);


        }else{

            $user_tran = time();

            $us_hash = "?campaignid=".$request->campaignid."&transid=".$request->transid."&acc=".$request->acc."&amt=".$request->amt."&intid=".$user_tran."&rtncode=1";

            $tevini_hash2 = hash_hmac("sha256", $us_hash, $campaign_dtls->hash_code);

            $unsuccess_url = "https://api.charidy.com/api/v1/campaign/donation/statusupdate/tevini".$us_hash."&hash=".$tevini_hash2;

            $message ='<span id="msg" style="color: rgb(255, 0, 0);">Incorrect account number or password</span>';
            return response()->json(['status'=> 301,'url'=> $unsuccess_url,'message'=>$message]);
        }
    }

}
