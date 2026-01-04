<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MobileVerify;
use App\Models\CardHolder;
use App\Models\User;
use Illuminate\support\Facades\Auth;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Exception;

class OTPController extends Controller
{
    public function mobileVerify(Request $request)
    {

        try {

            $premsg =  MobileVerify::where('user_id', Auth::user()->id)->get();
            if (isset($premsg)) {
                # code...
                $collection = MobileVerify::where('user_id', Auth::user()->id)->get(['id']);
                MobileVerify::destroy($collection->toArray());

            }
        
        $cardholder = CardHolder::where('user_id', Auth::user()->id)->first();
        
        $length = 6;
        $codeverify = substr(str_shuffle('123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'),1,$length);
        $message = 'Your Tevini verification code is: '.$codeverify;

        $receiver_number = $cardholder->Mobile;
        // $receiver_number = "+447533498883";

        
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');
        
        $twilio = new Client($sid, $token);
        $message = $twilio->messages
        ->create($receiver_number,
            array(
            "from" => $from,
            "body" => $message
            )
        );


        $newDateTime = Carbon::now()->addMinute(5);
            
        $mobileverify = new MobileVerify();
        $mobileverify->user_id = Auth::user()->id;
        $mobileverify->otp = $codeverify;
        $mobileverify->expire_at = $newDateTime;
        $mobileverify->save();

        $Mobile = CardHolder::where('user_id', Auth::user()->id)->first()->Mobile;
        $MobileLstDgt = substr($Mobile, -4);
        return view('frontend.user.card.verify', compact('MobileLstDgt'));



        
        }catch (Exception $e) {
            \Log::error('OTPController@mobileVerify error: '.$e->getMessage(), ['exception' => $e]);
            return redirect()->route('userCardService')->with('pinerrmsg', 'We can not send OTP in' .$receiver_number.' this number . Please check your phone number.!');
        }
 
    }


    public function OtpConfirmation(Request $request)
    {

        try {
            $otpmsg =  MobileVerify::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();

            $otp = $otpmsg->otp;
            $created_at = $otpmsg->created_at;
            $expire_at = $otpmsg->expire_at;

            $newDateTime = Carbon::now();
            if ($newDateTime > $expire_at) {
                return redirect()->back()->with('error', 'Time over!! Click Resend Button to get new OTP.');
            } else {

                if ($request->Code == $otp) {
                    $rand = Auth::user()->id;

                    return redirect()->route('cardSetPin', encrypt($rand));

                } else {
                    return redirect()->back()->with('error', 'OTP doesn\'t match. Please try again.');
                }
                
            }
            

        }catch (Exception $e) {
            return redirect()->route('userCardService')->with('pinerrmsg', 'We can not send OTP. Please check your phone number.!');
        }
 
    }


    public function statusVerify(Request $request)
    {
        try {

            $premsg =  MobileVerify::where('user_id', Auth::user()->id)->get();
            if (isset($premsg)) {
                # code...
                $collection = MobileVerify::where('user_id', Auth::user()->id)->get(['id']);
                MobileVerify::destroy($collection->toArray());

            }
        
        $cardholder = CardHolder::where('user_id', Auth::user()->id)->first();
        
        $length = 6;
        $codeverify = substr(str_shuffle('123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'),1,$length);
        $message = 'Your Tevini verification code is: '.$codeverify;

        $receiver_number = $cardholder->Mobile;
        // $receiver_number = "+447533498883";

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');
        
        $twilio = new Client($sid, $token);
        $message = $twilio->messages
        ->create($receiver_number,
            array(
            "from" => $from,
            "body" => $message
            )
        );



        $newDateTime = Carbon::now()->addMinute(5);
            
        $mobileverify = new MobileVerify();
        $mobileverify->user_id = Auth::user()->id;
        $mobileverify->otp = $codeverify;
        $mobileverify->expire_at = $newDateTime;
        $mobileverify->save();

        $Mobile = CardHolder::where('user_id', Auth::user()->id)->first()->Mobile;
        $MobileLstDgt = substr($Mobile, -4);
        return view('frontend.user.card.statusverify', compact('MobileLstDgt'));
        }catch (Exception $e) {
            \Log::error('OTPController@statusVerify error: '.$e->getMessage(), ['exception' => $e]);
            return redirect()->route('userCardService')->with('pinerrmsg', 'We can not send OTP. Please check your phone number.!!');
        }
 
    }
    public function statusOtpConfirmation(Request $request)
    {

        try {
            $otpmsg =  MobileVerify::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();

            $otp = $otpmsg->otp;
            $created_at = $otpmsg->created_at;
            $expire_at = $otpmsg->expire_at;

            $newDateTime = Carbon::now();
            if ($newDateTime > $expire_at) {
                return redirect()->back()->with('error', 'Time over!! Click Resend Button to get new OTP.');
            } else {

                if ($request->Code == $otp) {
                    $rand = Auth::user()->id;

                    return redirect()->route('cardStatusChange', encrypt($rand));

                } else {
                    return redirect()->back()->with('error', 'OTP doesn\'t match. Please try again.');
                }
                
            }
            

        }catch (Exception $e) {
            return redirect()->route('userCardService')->with('pinerrmsg', 'We can not send OTP. Please check your phone number.!');
        }
 
    }


    // card activation verification 

    public function activationVerify(Request $request)
    {

        try {

            $premsg =  MobileVerify::where('user_id', Auth::user()->id)->get();
            if (isset($premsg)) {
                # code...
                $collection = MobileVerify::where('user_id', Auth::user()->id)->get(['id']);
                MobileVerify::destroy($collection->toArray());

            }
        
        $cardholder = CardHolder::where('user_id', Auth::user()->id)->first();
        
        $length = 6;
        $codeverify = substr(str_shuffle('123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'),1,$length);
        $message = 'Your Tevini verification code is: '.$codeverify;

        $receiver_number = $cardholder->Mobile;
        // $receiver_number = "+447533498883";

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');
        
        $twilio = new Client($sid, $token);
        $message = $twilio->messages
        ->create($receiver_number,
            array(
            "from" => $from,
            "body" => $message
            )
        );



        $newDateTime = Carbon::now()->addMinute(5);
            
        $mobileverify = new MobileVerify();
        $mobileverify->user_id = Auth::user()->id;
        $mobileverify->otp = $codeverify;
        $mobileverify->expire_at = $newDateTime;
        $mobileverify->save();

        $Mobile = CardHolder::where('user_id', Auth::user()->id)->first()->Mobile;
        $MobileLstDgt = substr($Mobile, -4);
        return view('frontend.user.card.activationverify', compact('MobileLstDgt'));

        }catch (Exception $e) {
            return redirect()->route('userCardService')->with('pinerrmsg', 'We can not send OTP. Please check your phone number.!');
        }
 
    }


    public function activationOtpConfirmation(Request $request)
    {

        try {
            $otpmsg =  MobileVerify::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();
            $otp = $otpmsg->otp;
            $created_at = $otpmsg->created_at;
            $expire_at = $otpmsg->expire_at;

            $newDateTime = Carbon::now();
            if ($newDateTime > $expire_at) {
                return redirect()->back()->with('error', 'Time over!! Click Resend Button to get new OTP.');
            } else {
                if ($request->Code == $otp) {
                    $rand = Auth::user()->id;
                    return redirect()->route('cardActivation', encrypt($rand));
                } else {
                    return redirect()->back()->with('error', 'OTP doesn\'t match. Please try again.');
                }
            }
        }catch (Exception $e) {
            return redirect()->route('userCardService')->with('pinerrmsg', 'We can not send OTP. Please check your phone number.!');
        }
 
    }

    public function sendText(Request $request, $id)
    {
        try {

            $donor = User::findOrFail($id);

            $receiver_number = $donor->phone;

            $sid   = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from  = config('services.twilio.from');

            $twilio = new Client($sid, $token);

            $twilio->messages->create(
                $receiver_number,
                [
                    'from' => $from,
                    'body' => $request->message,
                ]
            );

            return redirect()->back()
                ->with('message', 'Message sent successfully to ' . $donor->name . '.');

        } catch (\Exception $e) {
            \Log::error('OTPController@sendText error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send message. Please try again.');
        }
    }
 

    
    
}
