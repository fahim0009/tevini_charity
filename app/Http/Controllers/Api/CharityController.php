<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Charity;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Mail\CharitypayReport;
use App\Mail\CharitylinkRequest;
use App\Mail\DonationReport;
use App\Mail\UrgentRequest;
use App\Models\CharityLink;
use App\Models\User;

class CharityController extends Controller
{
    public function getAllCharity()
    {
        $data = Charity::all();
        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data
        ]; 
        return response()->json($responseArray,200);
    }

    

    public function profileShow($id)
    {
        $data = Charity::where('id', $id)->first();
        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data
        ]; 
        return response()->json($responseArray,200);
    }


    public function updateCharity_profile(Request $request)
    {

        if ($request->password) {
            if ($request->password != $request->cpassword) {                
                $success['message'] = "Password doesn't match!!";
                return response()->json(['success'=>false,'response'=> $success], 202);
            }
        }



        $charity = Charity::findOrFail($request->charityid);
        $charity->name = $request->name;
        $charity->number = $request->phone;
        $charity->address = $request->address_first_line;
        $charity->address_second_line = $request->address_second_line;
        $charity->address_third_line = $request->address_third_line;
        $charity->town = $request->town;
        $charity->post_code = $request->postcode;
        $charity->account_name = $request->account_name;
        $charity->account_number = $request->account_number;
        $charity->account_sortcode = $request->account_sortcode;

        if ($request->password) {
            $charity->password= Hash::make($request->password);
        }

        if ($charity->save()) {
            $success['message'] = 'Profile Update Successfully.';
            $success['data'] = $charity;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }else{
            
            $success['message'] = "Server Error!!";
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

    }


    public function charityTransaction(Request $request, $id)
    {

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $intransactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions = Transaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['pending','=', '0']
            ])->orderBy('id','DESC')->get();

        }else{

            $intransactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions= Transaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['pending','=', '0']
            ])->orderBy('id','DESC')->get();


        }

        $responseArray = [
            'status'=>'ok',
            'intransactions'=>$intransactions,
            'outtransactions'=>$outtransactions,
            'pending_transactions'=>$pending_transactions
        ]; 
        return response()->json($responseArray,200);

    }

    public function charityLinkStore(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $visitor_subject = "Charity Donation Link";
        $amount = $request->amount;

        $emailValidation = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,10}$/";

        if(empty($name)){
            $success['message'] = "Please fill name field, thank you!";
            return response()->json(['success'=>false,'response'=> $success], 202);
        }
        
        if(empty($email)){
            
            $success['message'] = "Please fill email field, thank you!";
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if(!preg_match($emailValidation,$email)){
            
            $success['message'] = "Your mail ".$email." is not valid mail. Please wirite a valid mail, thank you!";
            return response()->json(['success'=>false,'response'=> $success], 202);
            
        }
        
        if(empty($amount)){
            
            $success['message'] = "Please write amount field, thank you!";
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        $data = new CharityLink();
        $data->charity_id = $request->charity_id;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->amount = $request->amount;
        $data->save();
        $contactmail = ContactMail::where('id', 1)->first()->name;

	    $array['name'] = $data->name;
            $array['cc'] = $contactmail;
            $array['client_no'] = Charity::where('id',$data->charity_id)->first()->id;
            $email = $data->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$data->charity_id)->first()->name;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new CharitylinkRequest($array));

            $success['message'] = 'Request send successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);
    }

    public function urgentRequest(Request $request)
    {

        $charity = Charity::where('id', $request->charity_id)->first();
        
        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['name'] = $charity->name;
        $array['subject'] = 'Urgent request';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $email = $charity->email;

        Mail::to($email)
                ->cc($contactmail)
                ->send(new UrgentRequest($array));
        
        $success['message'] = 'Email send successfully.';
        return response()->json(['success'=>true,'response'=> $success], 200);

    }

    public function closecharityLink(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        // dd($user);
        $values = CharityLink::where('email', Auth::user()->email)->update(['donor_notification'=>"1"]);
        
        $success['message'] = 'Donation request close Successfully.';
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

}
