<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Commission;
use App\Models\User;
use App\Models\Charity;
use App\Models\ContactMail;
use App\Models\Usertransaction;
use App\Models\Provoucher;
use App\Models\Batchprov;
use App\Models\Draft;

use PDF;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

use App\Mail\CharitypayReport;
use App\Mail\CharitylinkRequest;
use App\Mail\DonationReport;
use App\Mail\InstantReport;
use App\Mail\UrgentRequest;
use App\Models\CharityLink;


class CharityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Charity::orderBy('id','DESC')->get();
        return view('charity.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
            'acc' => 'required',
            ]);
          
        $password = '111111';    

        $user = new Charity;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->address = $request->address;
        $user->town = $request->town;
        $user->post_code = $request->post_code;
        $user->account_name = $request->account_name;
        $user->account_number = $request->account_number;
        $user->account_sortcode = $request->account_sortcode;
        $user->acc_no = $request->acc;
        if($request->balance == "") {
            $user->balance =0;
          }else{
            $user->balance = $request->balance;
          }
        if ($password) {
            $user->password= Hash::make($password);
        }   
        if($user->save()){

            $message ="Charity Created Successfully";
            return back()->with('message', $message);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function show(Charity $charity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $users = Charity::where('id','=' ,decrypt($id))->first();
        return view('charity.editcharity', compact('users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
            'number' => 'required',
            'address' => 'required',
            'acc_no' => 'required'
            ]);


        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $message ="Password doesn't match!!";
                return redirect()->route('charitylist')->with(['status'=> 303,'error'=> $message]);
            }
        }


        $user = Charity::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->address = $request->address;
        $user->town = $request->town;
        $user->post_code = $request->post_code;
        $user->acc_no = $request->acc_no;
        $user->account_name = $request->account_name;
        $user->account_number = $request->account_number;
        $user->account_sortcode = $request->account_sortcode;
        if ($request->password) {
            $user->password= Hash::make($request->password);
        }
        if($user->save()){

            $message ="Charity Update Successfully";

        return redirect()->route('charitylist')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Charity $charity)
    {
        //
    }

    public function deleteCharity($id)
    {
        if( Charity::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Charity has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function activeCharity(Request $request)
    {
        if($request->status==1){
            $active = Charity::find($request->id);
            $active->status = $request->status;
            $active->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $deactive = Charity::find($request->id);
            $deactive->status = $request->status;
            $deactive->save();
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Inactive Successfully.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

    }


    public function topup($id)
    {
        $topup = Charity::where('id','=', $id)->first();
        return view('charity.topup',compact('topup'));
    }

    public function topupStore(Request $request)
    {
        $user = Charity::find($request->topupid);
        $user->balance = $user->balance + $request->balance;
        if($user->save()){
            $message ="Amount added Successfully";
            return back()->with('message', $message);
        }

        return back();
    }

    public function pay($id)
    {
        $charity = Charity::where('id','=', $id)->first();
        return view('charity.pay',compact('charity'));
    }

    public function payStore(Request $request)
    {
        $charity_bal = Charity::find($request->topupid);
        $charity_bal->decrement('balance',$request->balance);
        $t_id = time() . "-" . $request->topupid;

        $charity = Charity::where('id','=',$request->topupid)->first();


            $transaction = new Transaction();
            $transaction->t_id = $t_id;
            $transaction->charity_id = $request->topupid;
            $transaction->t_type = "Out";
            $transaction->name = $request->source;
            $transaction->amount = $request->balance;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->save();

            $contactmail = ContactMail::where('id', 1)->first()->name;
    
            $array['subject'] = 'Payment Confirmation';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $array['name'] = $charity->name;
            $email = $charity->email;
            $array['charity'] = $charity;
            $array['amount'] = $request->balance;
            $array['note'] = $request->note;
            $array['t_id'] = $t_id;
            $array['subjectsingle'] = 'Report Placed - '.$charity->name;

    
            Mail::to($email)
            ->cc($contactmail)
            ->send(new CharitypayReport($array));


            $message ="Amount pay Successfully. Transaction id is: ". $t_id;
            return back()->with('message', $message);

    }


    // charity function start 

    public function charity_login_show()
    {
        return view('frontend.charity.charity_login');
    }

    public function charityDashboard()
    {

        return view('frontend.charity.dashboard');
    }

    public function profileShow()
    {
        return view('frontend.charity.profile');
    }


    public function updateCharity_profile(Request $request)
    {

        // $chkemail = Charity::where('email','=', $request->email)->whereNotIn('id', [auth('charity')->user()->id])->count();

        // if( $chkemail > 0){
        //     $message ="This email has already exists.";
        //     return redirect()->route('user.profile')->with(['status'=> 303,'error'=> $message]);

        // }

        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $message ="Password doesn't match!!";
                return redirect()->route('charity.profile')->with(['status'=> 303,'error'=> $message]);
            }
        }



        $charity = Charity::findOrFail(auth('charity')->user()->id);
        $charity->name = $request->name;
        $charity->number = $request->phone;
        $charity->address = $request->houseno;
        $charity->address_second_line = $request->address_second_line;
        $charity->address_third_line = $request->address_third_line;
        $charity->town = $request->town;
        $charity->post_code = $request->postcode;
        $charity->account_name = $request->account_name;
        $charity->account_number = $request->account_number;
        $charity->account_sortcode = $request->account_sortcode;

   

            // if ($request->image) {

            //     $request->validate([
            //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     ]);
            //     $rand = mt_rand(100000, 999999);
            //     $imageName = time(). $rand .'.'.$request->image->extension();
            //     $request->image->move(public_path('images'), $imageName);
            //     $userdata->photo= $imageName;
            // }

        if ($request->password) {
            $charity->password= Hash::make($request->password);
        }

        if ($charity->save()) {
            $message ="Profile Update Successfully";

        return redirect()->route('charity.profile')->with(['status'=> 303,'message'=> $message]);
        }
        else{
            return back()->with(['status'=> 303,'message'=>'Server Error!!']);
        }

    }

    
    public function charityTransaction(Request $request)
    {

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $intransactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions = Transaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['pending','=', '0']
            ])->orderBy('id','DESC')->get();

        }else{

            $intransactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions= Transaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['pending','=', '0']
            ])->orderBy('id','DESC')->get();


        }

        // dd($outtransactions);

        return view('frontend.charity.transaction')
        ->with('intransactions',$intransactions)
        ->with('outtransactions',$outtransactions)
        ->with('pending_transactions',$pending_transactions);

    }

    public function charityLink()
    {
        return view('frontend.charity.link');
    }

    public function charityLinkStore(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $visitor_subject = "Charity Donation Link";
        $amount = $request->amount;

        $emailValidation = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,10}$/";

        if(empty($name)){
            $message ="<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Please fill name field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($email)){
            $message ="<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Please fill email field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(!preg_match($emailValidation,$email)){
	    
            $message ="<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Your mail ".$email." is not valid mail. Please wirite a valid mail, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            
        }
        
        

        if(empty($amount)){
            $message ="<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Please write amount field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new CharityLink();
        $data->charity_id = auth('charity')->user()->id;
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

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Request send successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
    }

  
    public function closecharityLink(Request $request)
    {
        
        $deactive = CharityLink::find($request->linkid);
        $deactive->donor_notification = "1";
        $deactive->save();
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation request close Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }


    public function urgentRequest(Request $request)
    {

        
        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['name'] = $request->charityname;
        $array['subject'] = 'Urgent request';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $email = auth('charity')->user()->email;

        Mail::to($email)
                ->cc($contactmail)
                ->send(new UrgentRequest($array));
        
        $message ="Email send successfully";
        return back()->with('message', $message);

    }

    public function processVoucher()
    {
        $charities = Charity::all();
        $donors = User::where([
            ['is_type', '=', 'user'],
            ['status', '=', '1']
        ])->get();

        return view('frontend.charity.processvoucher')
        ->with('charities',$charities)
        ->with('donors',$donors);
    }

    public function pvoucherStore(Request $request)
    {
        $charity_id= auth('charity')->user()->id;
        $donor_ids = $request->donorIds;
        $donor_accs = $request->donorAccs;
        $chqs = $request->chqNos;
        $amounts = $request->amts;
        $notes = $request->notes;
        $waitings = $request->waitings;


        $check_chqs = Provoucher::all();

        if(empty($charity_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select a charity first.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        foreach( array_count_values($chqs) as $key => $val ) {
            if ( $val > 1 ){
                $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher ".$key." is more than one entry. </b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }

        foreach($chqs as $chq){
            foreach($check_chqs as $check_chq){
            if($chq == $check_chq->cheque_no){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher number ".$chq." is already proccesed. </b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
                }
            }
        }

        foreach($donor_ids as $key => $donor_id){
            if($donor_id == "" || $donor_accs[$key] == "" || $chqs[$key] == "" || $amounts[$key] == ""){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill all field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            }
        }

        // foreach($donor_ids as $key => $donor_id){
        //     $u_bal = User::where('id',$donor_id)->first()->balance;
        //     $overdrawn = (User::where('id',$donor_id)->first()->overdrawn_amount);
        //     $limitChk = $u_bal + $overdrawn;
        //     if($limitChk <= $amounts[$key]){
        //     $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This donor didn't have sufficient balance.</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        //     }
        // }


        // new code
        $batch = new Batchprov();
        $batch->charity_id = $charity_id;
        $batch->status = 0;

        if($batch->save()){

            foreach($donor_ids as $key => $donor_id)
            {

                $u_bal = User::where('id',$donor_id)->first()->balance;
                $overdrawn = (User::where('id',$donor_id)->first()->overdrawn_amount);
                $limitChk = $u_bal + $overdrawn;

                $utransaction = new Usertransaction();
                $utransaction->t_id = time() . "-" . $donor_id;
                $utransaction->user_id = $donor_id;
                $utransaction->charity_id = $charity_id;
                $utransaction->t_type = "Out";
                $utransaction->amount =  $amounts[$key];
                $utransaction->cheque_no =  $chqs[$key];
                $utransaction->title =  "Voucher";
                if($limitChk < $amounts[$key]){
                $utransaction->pending = 0; //transaction pending e ase
                $utransaction->status =  0; //status pending
                }else{
                $utransaction->pending = 1; //transaction complete
                $utransaction->status =  1; //status complete  
                }
                $utransaction->save();

                $pvsr =  new Provoucher();
                $pvsr->charity_id = $charity_id;
                $pvsr->user_id = $donor_id;
                $pvsr->batch_id = $batch->id;
                $pvsr->donor_acc = $donor_accs[$key];
                $pvsr->cheque_no = $chqs[$key];
                $pvsr->amount = $amounts[$key];
                $pvsr->note = $notes[$key];
                $pvsr->waiting = $waitings[$key];
                if($limitChk < $amounts[$key]){
                    $pvsr->status = 0;  //process voucher pending
                }else{
                    $pvsr->status = 1;  //process voucher complete
                }
                $pvsr->tran_id =  $utransaction->id;
                $pvsr->save();


                if($limitChk >= $amounts[$key]){
                    $ch = Charity::find($charity_id);
                    $ch->increment('balance',$amounts[$key]);
                    $ch->save();
                    $user = User::find($donor_id);
                    $user->decrement('balance',$amounts[$key]);
                    $user->save();

                    // card balance update
                    if (isset($user->CreditProfileId)) {
                        $CreditProfileId = $user->CreditProfileId;
                        $CreditProfileName = $user->name;
                        $AvailableBalance = 0 - $amounts[$key];
                        $comment = "Pending Voucher Balance update";
                        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                                'CreditProfileId' => $CreditProfileId,
                                'CreditProfileName' => $CreditProfileName,
                                'AvailableBalance' => $AvailableBalance,
                                'comment' => $comment,
                            ]);
                    }
                    // card balance update end


                }

                

            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Voucher Process successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message, 'charity_id'=>$charity_id, 'batch_id'=>$batch->id, ]);
        }

    }

    public function instReport($id)
    {
        $batch_id = $id;
        $charityid = Provoucher::where('batch_id','=', $id)->first()->charity_id;
        $charity = Charity::where('id','=',$charityid)->first();
        $remittance = Provoucher::where('batch_id','=', $id)->get();
        $total = Provoucher::where([
            ['batch_id','=', $id],
            ['status', '=', '1']
            ])->sum('amount');
        return view('frontend.charity.instreport',compact('remittance','total','charity','batch_id'));
    }

    public function instReportmail(Request $request)
    {
        $charityid = Provoucher::where('batch_id','=', $request->batch_id)->first()->charity_id;
        $charity = Charity::where('id','=',$charityid)->first();
        $remittance = Provoucher::where('batch_id','=', $request->batch_id)->orderBy('id','ASC')->get();
        $total = Provoucher::where([
            ['batch_id','=', $request->batch_id],
            ['status', '=', '1']
            ])->sum('amount');

        $user = User::where('id',$request->did)->first();

        $previous_pending = Provoucher::where([
            ['charity_id','=', $charityid],
            ['status', '=', '0']
            ])->sum('amount');

        $pdf = PDF::loadView('invoices.inst_report', compact('total','remittance','charity','previous_pending'));
        $output = $pdf->output();
        file_put_contents(public_path().'/invoices/'.'voucherReport#'.$charityid.'.pdf', $output);

        $contactmail = ContactMail::where('id', 1)->first()->name;

        $array['subject'] = 'Remittance Report';
        $array['from'] = 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        $array['name'] = $charity->name;
        $email = $request->mail;
        $array['charity'] = $charity;
        $array['remittance'] = $remittance;
        $array['total'] = $total;
        $array['file'] = public_path().'/invoices/voucherReport#'.$charityid.'.pdf';
        $array['file_name'] = 'voucherReport#'.$charityid.'.pdf';
        $array['subjectsingle'] = 'Report Placed - '.$charityid;

        Mail::to($email)
        ->cc($contactmail)
        ->send(new InstantReport($array));


        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Mail sent successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);


    }


    public function pendingVoucher()
    {
        
        $charity_id = auth('charity')->user()->id;
        $cvouchers = Provoucher::where([
            ['charity_id', '=', $charity_id],
            ['waiting', '=', 'No'],
            ['status', '=', '0']
        ])->orderBy('id','DESC')->get();

        return view('frontend.charity.pendingvoucher')
        ->with('cvouchers',$cvouchers);

    }

}
