<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Provoucher;
use App\Models\User;
use App\Models\Donation;
use App\Models\OverdrawnRecord;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Charity;
use App\Models\ContactMail;
use App\Mail\TopupReport;
use App\Mail\DonationReport;
use App\Mail\DonerReport;
use App\Mail\DonationreportCharity;
use PDF;
use Hash;
use Auth;

class DonorController extends Controller
{
    public function index()
    {
        return view('donor.index');
    }

    public function addDonor()
    {
        $users = User::where('is_type','=' ,'user')->orderBy('id','ASC')->get();
        return view('donor.adddonor', compact('users'));
    }

    public function donorStore(Request $request)
    {
        $email=User::where('email', $request->email)->count();
        if( $email == 1){
            $message ="This email already used!!";
            return back()->with('error', $message);
            exit();
        }


        $this->validate($request,[
            'title' => 'required',
            'fname' => 'required',
            'surname' => 'required',
            'email' => 'required|email'
        ]);

        $user = new User;
        $user->name = $request->fname;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->title = $request->title;
        $user->street = $request->street;
        $user->postcode = $request->postcode;
        $user->houseno = $request->houseno;
        $user->town = $request->town;
        if($request->accno){
        $user->accountno = $request->accno;
        $user->status = "1";
        }
        if(empty($request->balance)){
        $user->balance = 00;
        }else{
        $user->balance = $request->balance;
        }
        $user->password = Hash::make('123456');
        if($user->save()){

            $message ="Donor Created Successfully";
            return back()->with('message', $message);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function donorEdit($id)
    {

        $users = User::where('id','=' ,decrypt($id))->first();
        return view('donor.editdonor', compact('users'));

        // return response()->json($info);
    }

    public function donorUpdate(Request $request, $id)
    {

        $chkemail = User::where('email','=', $request->email)->whereNotIn('id', [$id])->count();


        if( $chkemail == 1){
            $message ="This email has already exists.";
            return redirect()->route('donor')->with(['status'=> 303,'error'=> $message]);

        }

        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $message ="Password doesn't match!!";
                return redirect()->route('donor')->with(['status'=> 303,'error'=> $message]);
            }
        }



        $user = User::findOrFail($id);
        $user->name = $request->fname;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->title = $request->title;
        $user->street = $request->street;
        $user->postcode = $request->postcode;
        $user->houseno = $request->houseno;
        $user->town = $request->town;
        $user->accountno = $request->accountno;

        if ($request->password) {
            $user->password= Hash::make($request->password);
        }
        if($user->save()){

            $message ="Donor Update Successfully";

        return redirect()->route('donor')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function deleteDonor(Request $request)
    {
        if( User::destroy($request->donorId)){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donor deleted successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Delete Fail...</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
    }


    public function topup($id,$amount)
    {
        $topup = User::where('id','=', $id)->first();
        return view('donor.topup')
        ->with('topup',$topup)
        ->with('amount',$amount);
    }

    public function topupStore(Request $request)
    {
        if(empty($request->gbalance)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill amount fill.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $donationBy = $request->donationBy;

        $user = User::find($request->topupid);
        $user->balance = $user->balance + $request->balance;
        if($user->save()){
            if ($request->commission != '0') {
                $topup = new Commission();
                $topup->user_id = $request->topupid;
                $topup->commission = $request->commission;
                $topup->save();
            }
            $transaction = new Transaction();
            $transaction->t_id = time() . "-" . $request->topupid;
            $transaction->user_id = $request->topupid;
            $transaction->t_type = "In";
            $transaction->name = $request->source;
            $transaction->commission = $request->commission;
            $transaction->amount = $request->gbalance;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->save();

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $request->topupid;
            $utransaction->user_id = $request->topupid;
            $utransaction->source = $request->source;
            $utransaction->t_type = "In";
            $utransaction->commission = $request->commission;
            $utransaction->amount = $request->balance;
            $utransaction->note = $request->note;
            if($request->gift == "true"){
            $utransaction->gift =  1;
            }
            $utransaction->title = 'Credit';
            $utransaction->status =  1;
            $utransaction->save();

            if($request->receipt == 'true'){
                $user = User::where('id',$request->topupid)->first();

                $contactmail = ContactMail::where('id', 1)->first()->name;

                $balance = $request->gbalance;
                $source = $request->source;
                $pdf = PDF::loadView('invoices.topup_report', compact('balance','source','user','donationBy'));
                $output = $pdf->output();
                file_put_contents(public_path().'/invoices/'.'Donation-report#'.$user->id.'.pdf', $output);
                $array['file'] = public_path().'/invoices/Donation-report#'.$user->id.'.pdf';
                $array['file_name'] = 'Donation-report#'.$user->id.'.pdf';
                $array['cc'] = $contactmail;
                $array['name'] = $user->name;
                $array['email'] = $user->email;
                $array['phone'] = $user->phone;
                $email = $user->email;
                $array['balance'] = $request->balance;
                $array['gbalance'] = $request->gbalance;
                $array['commission'] = $request->commission;
                $array['source'] = $request->source;

                Mail::to($email)
                ->cc($contactmail)
                ->send(new TopupReport($array));

                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully & receipt sent to donor mail.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function notification(Request $request, $id)
    {

        $data = Usertransaction::findOrFail($id);
        $data->notification = 0;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Notification Delete Successfully.</b></div>";
            
        return response()->json(['status'=> 300,'message'=>$message]);
        }
        return response()->json(['status'=> 300,'message'=>'Server Error!!']);

    }



    // donor report in admin
    public function userReportinAdmin(Request $request, $id)
    {
        $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
        $user = User::find($id);
        $donor_id = $user->id;
        if(!empty($request->fromdate) && !empty($request->todate)){
             $fromDate = $request->fromdate;
             $toDate   = $request->todate;
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['status','=', '1']
             ])->orwhere([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['pending','=', '0']
                 ])->orderBy('id','DESC')->get();
         }else{
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['status','=', '1']
                 ])->orwhere([
                ['user_id','=', $id],
                ['pending','=', '0']
                ])->orderBy('id','DESC')->get();
             $fromDate = "";
             $toDate   = "";
         }

         return view('donor.report',compact('report','fromDate','toDate','donor_id','user','tamount'));

    }


    public function userReportMailinAdmin(Request $request)
    {
            $id = $request->user_id;
            $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
            if(!empty($request->input('fromdate')) && !empty($request->input('todate'))){
                $fromDate = $request->input('fromdate');
                $toDate   = $request->input('todate');
                $report = Usertransaction::where([
                    ['user_id','=', $id],
                    ['created_at', '>=', $fromDate],
                    ['created_at', '<=', $toDate.' 23:59:59'],
                    ['status','=', '1']
                ])->orwhere([
                    ['user_id','=', $id],
                    ['pending','=', '0']
                    ])->orderBy('id','DESC')->get();
            }else{
                $report = Usertransaction::where([
                    ['user_id','=', $id],
                    ['status','=', '1']
                ])->orwhere([
                   ['user_id','=', $id],
                   ['pending','=', '0']
                   ])->orderBy('id','DESC')->get();
                $fromDate = "";
                $toDate   = "";
            }
            $user = User::find($id);
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['cc'] = $contactmail;
            $pdf = PDF::loadView('invoices.donor_report', compact('report','fromDate','toDate','user','tamount'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Report#'.$id.'.pdf', $output);
            $array['name'] = $user->name;
            $array['view'] = 'mail.donorreport';
            $array['subject'] = 'Donor Report';
            $array['from'] = 'info@tevini.co.uk';
            $array['content'] = 'Hi, Your donation report has been placed';
            $array['file'] = public_path().'/invoices/Report#'.$id.'.pdf';
            $array['file_name'] = 'Report#'.$id.'.pdf';
            $array['subjectsingle'] = 'Report Placed - '.$id;

            Mail::to($user->email)->queue(new DonerReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Mail sent successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);


    }

    // report to multiple donor

    public function multiUserreport(Request $request)
    {
        $donorIds = $request->donorIds;
        $fromDate = $request->fromdate;
        $toDate   = $request->todate;

        if(empty($request->donorIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select donor.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->fromdate)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select From Date.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->todate)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select To Date.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        foreach($donorIds as $id)
        {

            $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
            if(!empty($fromDate) && !empty($toDate)){

                $report = Usertransaction::where([
                    ['user_id','=', $id],
                    ['created_at', '>=', $fromDate],
                    ['created_at', '<=', $toDate.' 23:59:59'],
                    ['status','=', '1']
                ])->orwhere([
                    ['user_id','=', $id],
                    ['pending','=', '0']
                    ])->orderBy('id','DESC')->get();
            }else{
                $report = Usertransaction::where([
                    ['user_id','=', $id],
                    ['status','=', '1']
                ])->orwhere([
                   ['user_id','=', $id],
                   ['pending','=', '0']
                   ])->orderBy('id','DESC')->get();
                $fromDate = "";
                $toDate   = "";
            }
            $user = User::find($id);
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['cc'] = $contactmail;
            $pdf = PDF::loadView('invoices.donor_report', compact('report','fromDate','toDate','user','tamount'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Report#'.$id.'.pdf', $output);
            $array['name'] = $user->name;
            $array['view'] = 'mail.donorreport';
            $array['subject'] = 'Donor Report';
            $array['from'] = 'info@tevini.co.uk';
            $array['content'] = 'Hi, Your donation report has been placed';
            $array['file'] = public_path().'/invoices/Report#'.$id.'.pdf';
            $array['file_name'] = 'Report#'.$id.'.pdf';
            $array['subjectsingle'] = 'Report Placed - '.$id;

            Mail::to($user->email)->queue(new DonerReport($array));

        }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Mail sent successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);


    }



    public function adminDonorGiving()
    {
        return view('donor.donorgiving');
    }

    public function userDonationShow()
    {
        return view('frontend.user.donation');
    }

    public function userDonationStore(Request $request)
    {


        if(empty($request->charity_id)){
            // $message ="<div class='alert alert-danger'>Please select beneficiary field.</div>";
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select beneficiary field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(($request->standard == "true") && ($request->payments_type == "1") && (empty($request->number_payments))){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill number of payments field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if($request->c_donation == "false"){
            $message ="<div class='alert alert-danger'>Please accept contidion.</div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new Donation;
        $data->user_id = Auth::user()->id;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        if($data->standing_order == "true"){
        $data->payments = $request->payments_type;
        $data->number_payments = $request->number_payments;
        $data->starting = $request->starting;
        $data->interval = $request->interval;
        }else{
        $data->payments = null;
        $data->number_payments = null;
        $data->starting = null;
        $data->interval = null;
        }
        $data->confirm_donation = $request->c_donation;
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->status = 0;

        if($data->save()){

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . Auth::user()->id;
            $utransaction->user_id = Auth::user()->id;
            $utransaction->charity_id = $request->charity_id;
            $utransaction->donation_id = $data->id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $request->amount;
            $utransaction->title =  "Online Donation";
            $utransaction->status =  1;
            $utransaction->save();

            $user = User::find(Auth::user()->id);
            $user->decrement('balance',$request->amount);
            $user->save();

            $charity = Charity::find($request->charity_id);
            $charity->increment('balance',$request->amount);
            $charity->save();

            $user = User::where('id',Auth::user()->id)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['name'] = $user->name;
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation submited Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }


    }


    public function addAccount(Request $request)
    {
        if(empty($request->accno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill account field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = User::find($request->donnerId);
        $user->accountno = $request->accno;
        $user->status = 1;
        if($user->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Account added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }

    public function updateOverdrawnAmount(Request $request)
    {

        if(empty($request->overdrawnno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Overdrawn field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = User::find($request->overdrawnid);
        $user->overdrawn_amount = $request->overdrawnno;
        if($user->save()){

            $record = new OverdrawnRecord;
            $record->user_id = $request->overdrawnid;
            $record->amount = $request->overdrawnno;
            $record->created_by = Auth::user()->name;
            $record->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn amount update successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function updateUserOverdrawnAmount(Request $request)
    {
        $checkadmin = OverdrawnRecord::where('user_id','=', Auth::user()->id)->where('created_by','=','Admin')->count();

        if($checkadmin > 0 ){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Some comment.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->overdrawnno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Overdrawn field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = User::find($request->overdrawnid);
        $user->overdrawn_amount = $request->overdrawnno;
        if($user->save()){

            $record = new OverdrawnRecord;
            $record->user_id = $request->overdrawnid;
            $record->amount = $request->overdrawnno;
            $record->created_by = Auth::user()->name;
            $record->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn amount update successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }



    public function userStandingOrder()
    {
        $donation = Donation::where([
            ['standing_order','=', 'true'],
            ['user_id','=', auth()->user()->id],
            ['status','!=','3']
        ])->get();

        $pdonation = Donation::where([
            ['standing_order','=', 'true'],
            ['user_id','=', auth()->user()->id],
            ['status','=','3']
        ])->get();

        return view('frontend.user.standingorder')
        ->with('donation',$donation)
        ->with('pdonation',$pdonation);
    }

    public function userStandingOrderinAdmin($id)
    {
        $donation = Donation::where([
            ['standing_order','=', 'true'],
            ['user_id','=', $id],
            ['status','!=','3']
        ])->get();

        $pdonation = Donation::where([
            ['standing_order','=', 'true'],
            ['user_id','=', $id],
            ['status','=','3']
        ])->get();

        return view('donor.standingorder')
        ->with('donor_id',$id)
        ->with('donation',$donation)
        ->with('pdonation',$pdonation);
    }

    public function userDonationrecod()
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['user_id','=', auth()->user()->id]
        ])->get();

        return view('frontend.user.donationrecord')
        ->with('donation',$donation);
    }

    public function userDonationrecodinAdmin($id)
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['user_id','=', $id]
        ])->get();

        return view('donor.donationrecord')
        ->with('donation',$donation)
        ->with('donor_id',$id);
    }

    public function donationlist()
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['status','=','0']
        ])->get();
        return view('donor.donationlist',compact('donation'));
    }

    public function donationStanding()
    {
        $donation = Donation::where([
            ['standing_order','=', 'true'],
            ['status','=','0']
        ])->get();
        return view('donor.standing',compact('donation'));
    }

    public function donationRecord()
    {
        $donation = Donation::where([
            ['status','!=','0']
        ])->orderBy('id','DESC')->get();
        return view('donor.record',compact('donation'));
    }


    public function donationStatus(Request $request)
    {
   $user_id = Donation::where('id',$request->did)->first()->user_id;
        $charity_id = Donation::where('id',$request->did)->first()->charity_id;
        $donation = Donation::where('id',$request->did)->first();
        $balance = Donation::where('id',$request->did)->first()->amount;

        if($request->status == "3"){

        $user = User::find($user_id);
        $user->increment('balance',$balance);
        $user->save();

        $charity = Charity::find(Donation::where('id',$request->did)->first()->charity_id);
        $charity->decrement('balance',$balance);
        $charity->save();

        $utran = Usertransaction::find(Usertransaction::where('donation_id',$request->did)->first()->id);
        $utran->status = 0;
        $utran->save();

        }

        if($request->status == "1"){

            $user = User::where('id',$user_id)->first();
            $charity = Charity::where('id',$charity_id)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $pdf = PDF::loadView('invoices.donation_report_charity', compact('user','charity','donation'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Donation-report-charity#'.$charity->id.'.pdf', $output);
            $array['file'] = public_path().'/invoices/Donation-report-charity#'.$charity->id.'.pdf';
            $array['file_name'] = 'Donation-report-charity#'.$charity->id.'.pdf';
            $array['cc'] = $contactmail;
            $array['charity'] = $charity;
            $array['user'] = $user;
            $email = $charity->email;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationreportCharity($array));

        }

        $order = Donation::find($request->did);
        $order->status = $request->status;
        if($order->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }


}
