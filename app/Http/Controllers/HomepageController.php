<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\Campaign;
use App\Models\Charity;
use App\Models\Gateway;

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

    public function howitWorks()
    {
        return view('frontend.how-it-works');
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
            $utransaction->status =  1;
            $utransaction->save();

            $user = User::find($donor_id);
            $user->decrement('balance',$request->amt);

            $ch = Charity::find($campaign_dtls->charity_id);
            $ch->increment('balance',$request->amt);

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

}
