<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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

        return view('donor.profile',compact('profile_data','donor_id','currentyramount','totalamount','lastMonthName','currentMonthName'));
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
        $userdata->name= $request->name;
        $userdata->surname= $request->surname;
        $userdata->email= $request->email;
        $userdata->phone= $request->phone;
        $userdata->houseno= $request->houseno;
        $userdata->street= $request->street;
        $userdata->town= $request->town;
        $userdata->postcode= $request->postcode;

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

        if($request->tdfamount < 1){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Amount Field Required.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->tdfamount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Amount Field Required.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if($request->tdfamount  > (auth()->user()->balance + auth()->user()->overdrawn_amount)){
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
                        ->date(date('Y-m-d'))
                        ->amount(50)
                        ->round(2)
                        ->get();
        $converted = "";

        if (isset($convertedObj) ) {
            $message ="<b style='color: green'>Available</b>";
            return response()->json(['status'=> 300,'data'=>$converted,'message'=>$convertedObj]);
        } else {
            $message ="<b style='color: red'>This location is out of our service.</b>";
            return response()->json(['status'=> 303,'data'=>$converted,'message'=>$convertedObj,'currency_from'=>$request->currency_from]);
        }
        

    }

    public function gettransferToTDF()
    {
        $data = TdfTransaction::where('user_id', Auth::user()->id)->orderBy('id','DESC')->get();
        return view('frontend.user.transfertotdf')->with('data',$data);

    }
}
