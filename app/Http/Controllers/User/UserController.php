<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Models\Usertransaction;
use App\Models\User;
use App\Models\Role;
use App\Models\TdfTransaction;
use Illuminate\support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use AmrShawky\LaravelCurrency\Facade\Currency;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Date;
use App\Mail\TDFTransfer;
use App\Models\AccDelRequest;
use App\Models\ContactMail;
use App\Models\UserDetail;

class UserController extends Controller
{
    public function profile()
    {
        $profile_data= Auth::user();


        // previous year data start
        $period = CarbonPeriod::create(
            now()->month(4)->subMonths(12)->startOfMonth()->format('Y-m-d'),
            '1 month',
            now()->month(3)->endOfMonth()->format('Y-m-d')
        );
        $finYear = [];
        $totalamount = 0;
        foreach ($period as $p) {
            $finYear[] = $p->format('m-Y');
            $currentmonthgift2 = Usertransaction::where('user_id','=', Auth::user()->id)
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', $p->format('m'))
                            ->whereYear('created_at', $p->format('Y'))
                            ->get();
            foreach ($currentmonthgift2 as $data){
                $totalamount = $data->amount + $totalamount + $data->commission;
            }
        }
        // previous year data end

        // current year data start
        $currentyr = Usertransaction::where('user_id','=', Auth::user()->id)
                        ->whereBetween('created_at',
                            [Carbon::now()->subMonth(4), Carbon::now()]
                        )
                        ->get();

                        // dd($items );

        $currentyramount = 0;
        foreach ($currentyr as $data2){
            $currentyramount = $data2->amount + $currentyramount + $data2->commission;
        }
        // current year data end



        $currentmonthgift = Usertransaction::where('user_id','=', Auth::user()->id)
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->sum('amount');

        $lastMonth = Carbon::now()->startOfMonth()->subMonth(1)->format('m');
        $premonthgift = Usertransaction::where('user_id','=', Auth::user()->id)
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', $lastMonth )
                            ->whereYear('created_at', date('Y'))
                            ->sum('amount');





        $date = \Carbon\Carbon::now();
        $currentMonthName = $date->format('F'); // July
        $lastMonthName = $date->startOfMonth()->subMonth(1)->format('F'); // June

        return view('frontend.user.profile', compact('profile_data','currentyramount','totalamount','lastMonthName','currentMonthName'));
    }

    public function profileinAdmin($id)
    {   $donor_id = $id;
        $profile_data= User::where('id','=',$id)->first();



        // previous year data start
        $period = CarbonPeriod::create(
            now()->month(4)->subMonths(12)->startOfMonth()->format('Y-m-d'),
            '1 month',
            now()->month(3)->endOfMonth()->format('Y-m-d')
        );
        $finYear = [];
        $totalamount = 0;
        foreach ($period as $p) {
            $finYear[] = $p->format('m-Y');
            $currentmonthgift2 = Usertransaction::where('user_id','=', $id)->where('gift','=','1')
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', $p->format('m'))
                            ->whereYear('created_at', $p->format('Y'))
                            ->get();
            foreach ($currentmonthgift2 as $data){
                $totalamount = $data->amount + $totalamount + $data->commission;
            }
        }
        // previous year data end


        $currentyr = Usertransaction::where('user_id','=', $id)->where('gift','=','1')
                        ->whereBetween('created_at',
                            [Carbon::now()->subMonth(4), Carbon::now()]
                        )
                        ->get();

                        // dd($items );

        $currentyramount = 0;
        foreach ($currentyr as $data2){
            $currentyramount = $data2->amount + $currentyramount + $data2->commission;
        }


        // current year data end

        $date = \Carbon\Carbon::now();
        $currentMonthName = $date->format('F'); // July
        $lastMonthName = $date->startOfMonth()->subMonth(1)->format('F'); // June

        // user balance calculation start
        $gettrans = Usertransaction::where([
            ['user_id','=', $donor_id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', $donor_id],
            ['pending','=', '1']
        ])->orderBy('id','DESC')->get();

        $donorUpBalance = 0;

        foreach ($gettrans as $key => $tran) {
            if ($tran->t_type == "In") {
                $donorUpBalance = $donorUpBalance + $tran->amount;
            }elseif ($tran->t_type == "Out") {
                $donorUpBalance = $donorUpBalance - $tran->amount;
            } else {
                # code...
            }
        }

        $userTransactionBalance = UserTransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where([
                ['user_id','=', $donor_id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=', $donor_id],
                ['pending','=', '1']
            ])
            ->first();

        // user balance calculation end

        return view('donor.profile',compact('profile_data','donor_id','currentyramount','totalamount','lastMonthName','currentMonthName','donorUpBalance','userTransactionBalance'));
    }

    public function updateprofile(Request $request)
    {

        $chkemail = User::where('email','=', $request->email)->whereNotIn('id', [Auth::id()])->count();

        if( $chkemail > 0){
            $message ="This email has already exists.";
            return redirect()->route('user.profile')->with(['status'=> 303,'error'=> $message]);

        }

        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $message ="Password doesn't match!!";
                return redirect()->route('user.profile')->with(['status'=> 303,'error'=> $message]);
            }
        }




        $userdata= Auth::user();
        $userdata->profile_type = $request->profile_type;
        $userdata->prefix_name = $request->prefix_name;
        $userdata->name = $request->name;
        $userdata->surname = $request->surname;
        $userdata->email = $request->email;
        $userdata->phone = $request->phone;
        $userdata->houseno = $request->houseno;
        $userdata->street = $request->street;
        $userdata->address_third_line = $request->address_third_line;
        $userdata->town = $request->town;
        $userdata->postcode = $request->postcode;

            if ($request->image) {

                // unlink("images/".$userdata->photo);
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $rand = mt_rand(100000, 999999);
                $imageName = time(). $rand .'.'.$request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $userdata->photo= $imageName;
            }

        if ($request->password) {
            $userdata->password= Hash::make($request->password);
        }

        if ($userdata->save()) {
            $message ="Profile Update Successfully";

            return redirect()->route('user.profile')->with(['status'=> 303,'message'=> $message]);
        }
        else{
            return back()->with(['status'=> 303,'message'=>'Server Error!!']);
        }

    }

        public function changeUserPassword(Request $request)
        {

            if(empty($request->opassword)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Old Password\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            if(empty($request->password)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"New Password\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            if(empty($request->password === $request->confirmpassword)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>New password doesn't match.</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            $hashedPassword = Auth::user()->password;

            if (\Hash::check($request->opassword , $hashedPassword )) {

                if (!\Hash::check($request->password , $hashedPassword)) {
                    $where = [
                        'id'=>auth()->user()->id
                    ];
                    $passwordchange = User::where($where)->get()->first();
                    $passwordchange->password =Hash::make($request->password);

                    if ($passwordchange->save()) {
                        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password Change Successfully.</b></div>";
                        return response()->json(['status'=> 300,'message'=>$message]);
                    }else{
                        return response()->json(['status'=> 303,'message'=>'Server Error!!']);
                    }

                }else{
                    $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>New password can not be the old password.</b></div>";
                    return response()->json(['status'=> 303,'message'=>$message]);
                }

           }else{
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Old password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            }

        }



        public function userImageUpload(Request $request, $id)
        {
            $where = [
                'id'=>auth()->user()->id
            ];
            $user = User::where($where)->get()->first();

            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $user->photo= $imageName;


            if ($user->save()) {
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>User Image Upload Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }
            else{
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);
            }
        }

    public function transferToTDF(Request $request)
    {

        if(empty($request->tdfaccount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Account Field Required.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if($request->tdfamount < 18){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Minimum transfer is £18.00.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        if(empty($request->tdfamount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Amount Field Required.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        // donor balance
        $userTransactionBalance = Usertransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where([
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=',  auth()->user()->id],
                ['pending','=', '1']
            ])
            ->first();
        // donor balance end
        
        $donorBlanacewithLimit = $userTransactionBalance->balance + auth()->user()->overdrawn_amount;

        if($request->tdfamount  > $donorBlanacewithLimit){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You don't have enough balance to transfer.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new TdfTransaction();
        $data->issue_date = date('Y-m-d');
        $data->user_id = auth()->user()->id;
        $data->tdfaccount = $request->tdfaccount;
        $data->tdf_amount = $request->tdfamount;
        $data->current_dollar_amount = $request->tdfamount;
        if ($data->save()) {

            $user = User::find(auth()->user()->id);
            $user->balance = $user->balance - $request->tdfamount;
            $user->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->tdfamount;
                $comment = "Transfer to TDF";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".auth()->user()->id;
            $udtransaction->user_id = auth()->user()->id;
            $udtransaction->t_type = "Out";
            $udtransaction->amount =  $request->tdfamount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Transfer to TDF";
            $udtransaction->status =  1;
            $udtransaction->save();

            $transaction = new Transaction();
            $transaction->t_id = time()."-".auth()->user()->id;
            $transaction->user_id = auth()->user()->id;
            $transaction->t_type = "Out";
            $transaction->amount =  $request->tdfamount;
            $transaction->note ="Transfer to TDF";
            $transaction->status =  1;
            $transaction->save();

            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['name'] = auth()->user()->name;
            $array['subject'] = 'Urgent request';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $email = auth()->user()->email;

            Mail::to($email)
                    ->cc($contactmail)
                    ->send(new TDFTransfer($array));


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Successfully transferred to TDF.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        } else {
            return response()->json(['status'=> 303,'message'=>'Server error!!']);
        }
        
    }


    public function checkCurrencyAmount(Request $request)
    {


        $convertedObj = Currency::convert()
                        ->from('GBP')
                        ->to('USD')
                        ->amount(50)
                        ->get();
        $converted = "";

        if (isset($convertedObj) ) {
            return response()->json(['status'=> 300,'data'=>$converted,'message'=>$convertedObj]);
        } else {
            return response()->json(['status'=> 303,'data'=>$converted,'message'=>$convertedObj,'currency_from'=>$request->currency_from]);
        }
        

    }

    public function gettransferToTDF()
    {
        $data = TdfTransaction::where('user_id', Auth::user()->id)->orderBy('id','DESC')->get();
        return view('frontend.user.transfertotdf')->with('data',$data);

    }

    public function profileDeleteRequest(Request $request)
    {
        $data = new AccDelRequest();
        $data->user_id = Auth::id();
        $data->date = Date::now()->format('Y-m-d');
        $data->note = $request->note;
        if ($data->save()) {
            $message = "Account deletion request submitted successfully.";
            return redirect()->route('user.profile')->with(['status' => 303, 'message' => $message]);
        } else {
            return back()->with(['status' => 303, 'message' => 'Server Error!!']);
        }
    }

    
    public function emailAccountStore(Request $request)
    {
        $data = new UserDetail();
        $data->user_id = Auth::id();
        $data->date = Date::now()->format('Y-m-d');
        $data->email = $request->newemail;
        if ($data->save()) {
            $message = "New email added successfully.";
            return redirect()->route('user.profile')->with(['status' => 200, 'message' => $message]);
        } else {
            return back()->with(['status' => 303, 'message' => 'Server Error!!']);
        }
    }


    public function emailAccountUpdate(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'upemail' => 'required|email|max:255|unique:user_details,email,' . $request->userDetailId,
        ]);

        $data = UserDetail::findOrFail($request->userDetailId);
        $data->email = $request->upemail;
        $data->save();

        $message = "Email updated successfully.";
        return redirect()->route('user.profile')->with(['status' => 200, 'message' => $message]);
    }

    public function destroy($id)
    {
        $data = UserDetail::findOrFail($id);
        $data->delete();
        $message = "Email deleted successfully.";
        return redirect()->route('user.profile')->with(['status' => 200, 'message' => $message]);
    }




    public function verification($id)
    {
        $user = auth()->user() ?? User::find($id);

        if (! $user) {
            return redirect()->route('login')->withErrors('User not found.');
        }

        // Verify email if not already verified
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Generate unique account number if not already set
        if (empty($user->accountno)) {
            $user->accountno = $this->generateUniqueAccountNo();
            $user->save();
        }

        // Redirect accordingly
        return auth()->check()
            ? redirect()->route('user.dashboard')
            : redirect()->route('login')->with('status', 'Email verified successfully. Please login to continue.');
    }

    private function generateUniqueAccountNo()
    {
        do {
            $accountno = mt_rand(100000, 999999);
        } while (User::where('accountno', $accountno)->exists());

        return $accountno;
    }
}
