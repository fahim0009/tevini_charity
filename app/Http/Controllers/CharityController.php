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
use Illuminate\Support\Facades\Log;

use App\Mail\CharitypayReport;
use App\Mail\CharitylinkRequest;
use App\Mail\DonationReport;
use App\Mail\InstantReport;
use App\Mail\UrgentRequest;
use App\Models\CharityLink;
use App\Models\UserDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

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

            ->addColumn('auto_payment', function($row){
                $checked = $row->auto_payment == 1 ? 'checked' : '';
                return '
                    <div class="form-check form-switch text-center">
                        <input class="form-check-input auto_payment_status"
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

                        <a href="javascript:void(0)" onclick="editCharity(' . $row->id . ')">
                            <i class="fa fa-edit" style="color:#2196f3;font-size:16px;"></i>
                        </a>

                        <a rid="'.$row->id.'" class="deleteBtn">
                            <i class="fa fa-trash-o" style="color:red;font-size:16px;"></i>
                        </a>
                ';
            })

            ->rawColumns(['bank', 'status', 'action','auto_payment'])
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:charities,email',
            'acc' => 'required|string|unique:charities,acc_no',
            'number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'town' => 'nullable|string|max:100',
            'post_code' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'account_sortcode' => 'nullable|string|max:10',
            'bank_statement' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'balance' => 'nullable|numeric|min:0',
        ], [
            'email.unique' => 'This email is already registered.',
            'acc.unique' => 'This charity registration number already exists.',
            'bank_statement.mimes' => 'Bank statement must be a PDF, JPG, or PNG file.',
            'bank_statement.max' => 'Bank statement must not exceed 5MB.',
            'balance.numeric' => 'Balance must be a valid number.',
            'balance.min' => 'Balance cannot be negative.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please fix the errors below.'
            ], 422);
        }

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

        if (isset($request->bank_statement)) {
            $rand = mt_rand(100000, 999999);
            $imageName = time() . $rand . '.' . $request->bank_statement->extension();
            $request->bank_statement->move(public_path('images'), $imageName);
            $user->bank_statement = $imageName;
        }

        $user->balance = $request->balance != "" ? $request->balance : 0;
        $user->password = Hash::make('111111');

        if ($user->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Charity Created Successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Server Error!!'
        ], 500);
    }

    public function edit($id)
    {
        $charity = Charity::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $charity
        ], 200);
    }

    public function update(Request $request, $id)
    {
        Log::info('Updating charity with ID: ' . $id, [
            'acc_no' => $request->acc_no
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:charities,email,' . $id,
            'number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'acc' => 'required|string|unique:charities,acc_no,' . $id,
            'town' => 'nullable|string|max:100',
            'post_code' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'account_sortcode' => 'nullable|string|max:10',
            'bank_statement' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'password' => 'nullable|min:8',
            'password_confirmation' => 'nullable|min:8|same:password',
        ], [
            'email.unique' => 'This email is already registered.',
            'acc_no.unique' => 'This charity registration number already exists.',
            'bank_statement.mimes' => 'Bank statement must be a PDF, JPG, or PNG file.',
            'bank_statement.max' => 'Bank statement must not exceed 5MB.',
            'password.min' => 'Password must be at least 8 characters.',
            'password_confirmation.same' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please fix the errors below.'
            ], 422);
        }

        $user = Charity::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->address = $request->address;
        $user->town = $request->town;
        $user->post_code = $request->post_code;
        $user->acc_no = $request->acc;
        $user->account_name = $request->account_name;
        $user->account_number = $request->account_number;
        $user->account_sortcode = $request->account_sortcode;

        if (isset($request->bank_statement)) {
            if ($user->bank_statement && file_exists(public_path('images/' . $user->bank_statement))) {
                @unlink(public_path('images/' . $user->bank_statement));
            }

            $rand = mt_rand(100000, 999999);
            $imageName = time() . $rand . '.' . $request->bank_statement->extension();
            $request->bank_statement->move(public_path('images'), $imageName);
            $user->bank_statement = $imageName;
        }

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        if ($user->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Charity Updated Successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Server Error!!'
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */

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


    public function autoPaymentCharity(Request $request)
    {
        if($request->status==1){
            $active = Charity::find($request->id);
            $active->auto_payment = $request->status;
            $active->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Automatic Payment Active Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $deactive = Charity::find($request->id);
            $deactive->auto_payment = $request->status;
            $deactive->save();
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Automatic Payment Deactive Successfully.</b></div>";
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

    public function pay($id, $amount = null)
    {
        $charity = Charity::where('id','=', $id)->first();
        return view('charity.pay',compact('charity','amount'));
    }

    public function payStore(Request $request)
    {

        $charity_bal = Charity::find($request->topupid);
        $charity_bal->decrement('balance',$request->balance);
        $t_id = time() . "-" . $request->topupid;
        $createdAt = Carbon::parse($request->created_at);

        $charity = Charity::where('id','=',$request->topupid)->first();


            $transaction = new Transaction();
            $transaction->t_id = $t_id;
            $transaction->charity_id = $request->topupid;
            $transaction->t_type = "Out";
            $transaction->name = $request->source;
            $transaction->amount = $request->balance;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->created_at = $createdAt;
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


    public function updateProfile(Request $request)
    {
        $charityId = auth('charity')->user()->id;

        $rules = [
            'name'                => 'required|string|max:191',
            'email'               => 'required|email|unique:charities,email,' . $charityId . ',id', // Fixed: added ',id'
            'phone'               => 'nullable|string|max:20',
            'acc_no'              => 'required|numeric|unique:charities,acc_no,' . $charityId . ',id', // Fixed: added ',id'
            'website'             => 'required|url|max:191',
            'houseno'             => 'required|string|max:191',
            'address_second_line' => 'nullable|string|max:191',
            'address_third_line'  => 'nullable|string|max:191',
            'town'                => 'required|string|max:100',
            'postcode'            => 'required|string|max:20',
            'account_name'        => 'nullable|string|max:191',
            'account_number'      => 'nullable|string|max:20',
            'account_sortcode'    => 'nullable|string|max:10',
            'profile_image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Only validate password if the user actually typed something
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Custom Error Messages
        $customMessages = [
            'name.required'               => 'Please enter your full name.',
            'email.required'              => 'The email address is required.',
            'email.email'                 => 'Please enter a valid email address.',
            'email.unique'                => 'This email is already registered with another account.',
            'acc_no.required'             => 'The Charity Number is required.',
            'acc_no.numeric'              => 'The Charity Number must be a valid number.',
            'acc_no.unique'               => 'This Charity Number is already taken by another account.',
            'website.required'            => 'Please enter your website URL.',
            'website.url'                 => 'Please enter a valid URL (e.g., https://example.com).',
            'houseno.required'            => 'Address Line 1 is required.',
            'town.required'               => 'Town is required.',
            'postcode.required'           => 'Postcode is required.',
            'password.min'                => 'Password must be at least 8 characters.',
            'password.confirmed'          => 'The new password and confirm password do not match.',
            'profile_image.image'         => 'The file must be a valid image.',
            'profile_image.mimes'         => 'Only JPG, JPEG, and PNG images are allowed.',
            'profile_image.max'           => 'The image size must be under 2MB.',
        ];

        // Pass both rules and custom messages to validate()
        $validated = $request->validate($rules, $customMessages);

        $charity = Charity::findOrFail($charityId);

        // Handle Profile Image Upload
        if ($request->hasFile('profile_image')) {
            if ($charity->profile_image && file_exists(public_path($charity->profile_image))) {
                unlink(public_path($charity->profile_image));
            }
            
            $imageName = time() . '_' . mt_rand(100000, 999999) . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('images'), $imageName);
            $charity->profile_image = 'images/' . $imageName;
        }

        // Handle Profile Image Removal
        if ($request->boolean('remove_profile_image')) {
            if ($charity->profile_image && file_exists(public_path($charity->profile_image))) {
                unlink(public_path($charity->profile_image));
            }
            $charity->profile_image = null;
        }

        // Assign Validated Fields
        $charity->name                = $validated['name'];
        $charity->email               = $validated['email'];
        $charity->number              = $validated['phone'];
        $charity->acc_no              = $validated['acc_no'];
        $charity->website             = $validated['website'];
        $charity->address             = $validated['houseno'];
        $charity->address_second_line = $validated['address_second_line'];
        $charity->address_third_line  = $validated['address_third_line'];
        $charity->town                = $validated['town'];
        $charity->post_code           = $validated['postcode'];
        $charity->account_name        = $validated['account_name'];
        $charity->account_number      = $validated['account_number'];
        $charity->account_sortcode    = $validated['account_sortcode'];

        // Hash and update password if provided
        if ($request->filled('password')) {
            $charity->password = Hash::make($validated['password']);
        }

        $charity->save();

        return redirect()->route('charity.profile')->with([
            'status'  => 200,
            'message' => 'Profile updated successfully.',
        ]);
    }
        
    public function charityTransaction(Request $request)
    {
        $charity = auth('charity')->user();

        if (!$charity) {
            return redirect()->route('charity_loginshow')->with('error', 'Please log in first.');
        }

        // Build base date constraints
        $dateFilter = function ($query) use ($request) {
            if (!empty($request->input('fromDate')) && !empty($request->input('toDate'))) {
                $fromDate = Carbon::parse($request->input('fromDate'))->startOfDay();
                $toDate = Carbon::parse($request->input('toDate'))->endOfDay();
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
        };

        // Transaction In - Fetch all records
        $intransactions = Usertransaction::where([
                            't_type' => 'Out',
                            'charity_id' => $charity->id,
                            'status' => '1'
                        ])
                        ->where($dateFilter)
                        ->with([
                            'provoucher',
                            'user',
                            'charity',
                            'standingdonationDetail.StandingDonation'
                        ])
                        ->orderBy('id', 'DESC')
                        ->get(); // Changed here

        // Transaction Out - Fetch all records
        $outtransactions = Transaction::where([
                            't_type' => 'Out',
                            'charity_id' => $charity->id,
                            'status' => '1'
                        ])
                        ->where($dateFilter)
                        ->with('charity')
                        ->orderBy('id', 'DESC')
                        ->get(); // Changed here

        // Pending Transactions - Fetch all records
        $pending_transactions = Usertransaction::where([
                            't_type' => 'Out',
                            'charity_id' => $charity->id,
                            'pending' => '0'
                        ])
                        ->where($dateFilter)
                        ->with('user')
                        ->orderBy('id', 'DESC')
                        ->get(); // Changed here

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
