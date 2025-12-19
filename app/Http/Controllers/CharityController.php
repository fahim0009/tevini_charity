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
use Illuminate\Support\Str;
use App\Mail\VerifyEmailMail;

use PDF;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Date;

use App\Mail\CharitypayReport;
use App\Mail\CharitylinkRequest;
use App\Mail\DonationReport;
use App\Mail\InstantReport;
use App\Mail\UrgentRequest;
use App\Models\CharityLink;
use App\Models\UserDetail;
use Yajra\DataTables\DataTables;

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


    public function getData(Request $request)
{
    if ($request->ajax()) {

        $data = Charity::orderBy('id','DESC');

        return DataTables::of($data)

        ->addColumn('pending', function($row){
            $pending = Usertransaction::where([
                ['t_type', 'Out'],
                ['charity_id', $row->id],
                ['pending', 0]
            ])->sum('amount');

            return '£' . number_format($pending, 2);
        })

        ->addColumn('balance', function($row){
            return '£' . number_format($row->balance, 2);
        })

        ->addColumn('status', function($row){
            $checked = $row->status == 1 ? 'checked' : '';
            return '
                <div class="form-check form-switch text-center">
                    <input class="form-check-input campaignstatus"
                      type="checkbox" '.$checked.' data-id="'.$row->id.'">
                </div>
            ';
        })

        ->addColumn('bank', function($row){
            if (!$row->bank_statement) return '';

            return '
                <a href="#" class="bg-dark text-white py-1 px-3 rounded mb-1 openBankModal" 
                data-file="'.$row->bank_statement.'">
                    <i class="fa fa-file" style="color:#4D617E;font-size:16px;"></i> Bank 
                </a>
            ';
        })


        ->addColumn('action', function($row){


            return '
                <div class="d-flex flex-column text-center">
                    <a class="bg-success text-white py-1 px-3 rounded mb-1"
                        href="'.route('charity.pay',$row->id).'" target="_blank">Pay</a>

                    <a class="bg-dark text-white py-1 px-3 rounded mb-1"
                        href="'.route('charity.topup',$row->id).'" target="_blank">Top up</a>

                </div>
                

                    <a href="'.route('charityemail',$row->id).'">
                        <i class="fa fa-envelope-o" style="color:#4D617E;font-size:16px;"></i>
                    </a>

                    <a href="'.route('charity.tranview',$row->id).'">
                        <i class="fa fa-eye" style="color:#09a311;font-size:16px;"></i>
                    </a>

                    <a href="'.route('charity.edit', encrypt($row->id)).'">
                        <i class="fa fa-edit" style="color:#2196f3;font-size:16px;"></i>
                    </a>

                    <a rid="'.$row->id.'" class="deleteBtn">
                        <i class="fa fa-trash-o" style="color:red;font-size:16px;"></i>
                    </a>
            ';
        })

        ->rawColumns(['bank', 'status', 'action'])
        ->make(true);
    }
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

        if(isset($request->bank_statement)){
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->bank_statement->extension();
            $request->bank_statement->move(public_path('images'), $imageName);
            $user->bank_statement= $imageName;
        }


        if($request->balance == "") {
            $user->balance = 0;
          }else{
            $user->balance = $request->balance;
          }
        if ($password) {
            $user->password = Hash::make($password);
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

        if(isset($request->bank_statement)){
            // Delete previous bank statement if it exists
            if($user->bank_statement && file_exists(public_path('images/' . $user->bank_statement))){
                @unlink(public_path('images/' . $user->bank_statement));
            }
            
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->bank_statement->extension();
            $request->bank_statement->move(public_path('images'), $imageName);
            $user->bank_statement = $imageName;
        }



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


    public function topup($id, $amount = null)
    {
        $topup = Charity::where('id','=', $id)->first();
        return view('charity.topup',compact('topup', 'amount'));
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

            if ($request->sendemail) {
                
                Mail::to($email)
                ->cc($contactmail)
                ->send(new CharitypayReport($array));
            }
    
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
        $charity = auth('charity')->user();
        if (!$charity) {
            return redirect()->route('charity_loginshow')->with('error', 'Please log in first.');
        }

        return view('frontend.charity.dashboard');
    }

    public function profileShow()
    {
        
        $charity = auth('charity')->user();
        if (!$charity) {
            return redirect()->route('charity_loginshow')->with('error', 'Please log in first.');
        }
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
        $charity = auth('charity')->user();

        if (!$charity) {
            return redirect()->route('charity_loginshow')->with('error', 'Please log in first.');
        }

        $charityId = $charity->id;

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $intransactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type', '=', 'Out'],
                ['charity_id', '=', $charityId],
                ['status', '=', '1']
            ])->with('provoucher')->orderBy('id','DESC')->get();

            $outtransactions = Transaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type', '=', 'Out'],
                ['charity_id', '=', $charityId],
                ['status', '=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type', '=', 'Out'],
                ['charity_id', '=', $charityId],
                ['pending', '=', '0']
            ])->orderBy('id','DESC')->get();

        } else {
            $intransactions = Usertransaction::where([
                ['t_type', '=', 'Out'],
                ['charity_id', '=', $charityId],
                ['status', '=', '1']
            ])->with('provoucher')->orderBy('id','DESC')->get();

            $outtransactions = Transaction::where([
                ['t_type', '=', 'Out'],
                ['charity_id', '=', $charityId],
                ['status', '=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['t_type', '=', 'Out'],
                ['charity_id', '=', $charityId],
                ['pending', '=', '0']
            ])->orderBy('id','DESC')->get();
        }

        return view('frontend.charity.transaction', compact(
            'intransactions', 'outtransactions', 'pending_transactions'
        ));
    }


    public function charityLink()
    {
        
        $charity = auth('charity')->user();
        if (!$charity) {
            return redirect()->route('charity_loginshow')->with('error', 'Please log in first.');
        }

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
        
        $charity = auth('charity')->user();
        if (!$charity) {
            return redirect()->route('charity_loginshow')->with('error', 'Please log in first.');
        }
        
        $charities = Charity::all();
        $donors = User::where([
            ['is_type', '=', 'user'],
            ['status', '=', '1']
        ])->get();

        return view('frontend.charity.processvoucher')
        ->with('charities',$charities)
        ->with('donors',$donors);
    }

    public function pvoucherStore_old(Request $request)
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
                $pvsr->waiting = "No";
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

    public function pvoucherStore(Request $request)
    {
        $charity_id = auth('charity')->user()->id ?? null;

        // Safely retrieve array inputs (avoid null errors)
        $donor_ids  = $request->donorIds ?? [];
        $donor_accs = $request->donorAccs ?? [];
        $chqs       = $request->chqNos ?? [];
        $amounts    = $request->amts ?? [];
        $notes      = $request->notes ?? [];
        $waitings   = $request->waitings ?? [];

        // Validation: Charity must be selected
        if (empty($charity_id)) {
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-danger'><b>Please select a charity first.</b></div>"
            ]);
        }

        // Validation: Must have at least one cheque number
        if (empty($chqs) || !is_array($chqs)) {
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-danger'><b>No vouchers found. Please scan vouchers first.</b></div>"
            ]);
        }

        // Check duplicate cheque numbers in current submission
        foreach (array_count_values($chqs) as $key => $val) {
            if ($val > 1) {
                return response()->json([
                    'status' => 303,
                    'message' => "<div class='alert alert-danger'><b>Voucher {$key} is entered more than once.</b></div>"
                ]);
            }
        }

        // Check already processed cheques in DB
        $existing_cheques = Provoucher::pluck('cheque_no')->toArray();
        foreach ($chqs as $chq) {
            if (in_array($chq, $existing_cheques)) {
                return response()->json([
                    'status' => 303,
                    'message' => "<div class='alert alert-danger'><b>Voucher number {$chq} is already processed.</b></div>"
                ]);
            }
        }

        // Check for empty required fields
        foreach ($donor_ids as $key => $donor_id) {
            if (empty($donor_id) || empty($donor_accs[$key]) || empty($chqs[$key]) || empty($amounts[$key])) {
                return response()->json([
                    'status' => 303,
                    'message' => "<div class='alert alert-danger'><b>Please fill all required fields.</b></div>"
                ]);
            }
        }

        // Create new batch
        $batch = new Batchprov();
        $batch->charity_id = $charity_id;
        $batch->status = 0;
        $batch->save();

        // Loop donors
        foreach ($donor_ids as $key => $donor_id) {
            $user = User::find($donor_id);
            $limitChk = $user->getAvailableLimit() ?? 0;

            // Create user transaction
            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $donor_id;
            $utransaction->user_id = $donor_id;
            $utransaction->charity_id = $charity_id;
            $utransaction->t_type = "Out";
            $utransaction->amount = $amounts[$key];
            $utransaction->cheque_no = $chqs[$key];
            $utransaction->title = "Voucher";

            if ($limitChk < $amounts[$key]) {
                $utransaction->pending = 0; // pending transaction
                $utransaction->status = 0;  // pending status
            } else {
                $utransaction->pending = 1; // complete
                $utransaction->status = 1;  // complete
            }
            $utransaction->save();

            // Save into provoucher
            $pvsr = new Provoucher();
            $pvsr->charity_id = $charity_id;
            $pvsr->user_id = $donor_id;
            $pvsr->batch_id = $batch->id;
            $pvsr->donor_acc = $donor_accs[$key];
            $pvsr->cheque_no = $chqs[$key];
            $pvsr->amount = $amounts[$key];
            $pvsr->note = $notes[$key];
            $pvsr->waiting = "No";
            $pvsr->status = ($limitChk < $amounts[$key]) ? 0 : 1;
            $pvsr->tran_id = $utransaction->id;
            $pvsr->save();

            // Update charity and user balances if complete
            if ($limitChk >= $amounts[$key]) {
                $charity = Charity::find($charity_id);
                $charity->increment('balance', $amounts[$key]);

                $user->decrement('balance', $amounts[$key]);

                // Update card balance if user has CreditProfileId
                if (!empty($user->CreditProfileId)) {
                    Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                            'CreditProfileId' => $user->CreditProfileId,
                            'CreditProfileName' => $user->name,
                            'AvailableBalance' => 0 - $amounts[$key],
                            'comment' => 'Pending Voucher Balance update',
                        ]);
                }
            }
        }

        // Final response
        return response()->json([
            'status' => 300,
            'message' => "<div class='alert alert-success'><b>Voucher processed successfully.</b></div>",
            'charity_id' => $charity_id,
            'batch_id' => $batch->id
        ]);
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

    // donor email send
    public function charityemail($id)
    {
        
        $charity = Charity::where('id','=', $id)->first();
        return view('charity.email',compact('charity'));
    }


    public function charitymailsend(Request $request)
    {


        $validatedData = $request->validate([
            'subject' => 'required',
            'emailto' => 'required|email',
            'body' => 'required',
        ]);


            $charity = Charity::where('id',$request->userid)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['cc'] = $contactmail;
            $array['name'] = $charity->name;
            $array['email'] = $charity->email;
            $array['phone'] = $charity->phone;
            $email = $request->emailto;
            $array['subject'] = $request->subject;
            $array['body'] = $request->body;
            $array['from'] = 'info@tevini.co.uk';

            Mail::send('mail.donorMail', compact('array'), function($message)use($array,$email) {
                $message->from($array['from'], 'Tevini.co.uk');
                $message->to($email)->cc($array['cc'])->subject($array['subject']);
               });

            return redirect()->back()->with('success', 'Mail send successfully.');
    }


        
    public function emailAccountStore(Request $request)
    {


        
        UserDetail::where('user_id', auth('charity')->user()->id)
            ->whereNull('email_verified_at')
            ->where('email', '=', $request->newemail)
            ->delete();


        $request->validate([
            'newemail' => 'required|email|unique:user_details,email',
        ], [
            'newemail.required' => 'Please enter a new email address.',
            'newemail.email'    => 'Please provide a valid email address.',
            'newemail.unique'   => 'This email is already in use. Please choose another.',
        ]);




        $charity_id = auth('charity')->user()->id;
        $data = new UserDetail();
        $data->charity_id = $charity_id;
        $data->date = Date::now()->format('Y-m-d');
        $data->email = $request->newemail;
        $data->verification_token = Str::random(64);
        if ($data->save()) {

        Mail::to($data->email)->send(new VerifyEmailMail($data));

        return redirect()->route('charity.profile')->with([
            'status' => 200,
            'message' => 'Verification link sent to your email. Please verify to activate.',
        ]);

        } else {
            return back()->with(['status' => 303, 'message' => 'Server Error!!']);
        }
    }


    public function emailAccountUpdate(Request $request)
    {

        $request->validate([
            'upemail' => 'required|email|max:255|unique:user_details,email,' . $request->userDetailId,
        ]);

        $data = UserDetail::findOrFail($request->userDetailId);
        $data->email = $request->upemail;
        $data->save();

        $message = "Email updated successfully.";
        return redirect()->route('charity.profile')->with(['status' => 200, 'message' => $message]);
    }

    public function emaildestroy($id)
    {
        $data = UserDetail::findOrFail($id);
        $data->delete();
        $message = "Email deleted successfully.";
        return redirect()->route('charity.profile')->with(['status' => 200, 'message' => $message]);
    }


}
