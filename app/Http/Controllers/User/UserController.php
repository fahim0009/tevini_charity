<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Usertransaction;
use App\Models\User;
use App\Models\Role;
use Illuminate\support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile()
    {
        $profile_data= Auth::user();
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

        return view('frontend.user.profile', compact('profile_data','currentmonthgift','premonthgift','lastMonthName','currentMonthName'));
    }

    public function profileinAdmin($id)
    {   $donor_id = $id;
        $profile_data= User::where('id','=',$id)->first();
        $currentmonthgift = Usertransaction::where('user_id','=', $id)
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->sum('amount');

        $lastMonth = Carbon::now()->startOfMonth()->subMonth(1)->format('m');
        $premonthgift = Usertransaction::where('user_id','=', $id)
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', $lastMonth )
                            ->whereYear('created_at', date('Y'))
                            ->sum('amount');

        $date = \Carbon\Carbon::now();
        $currentMonthName = $date->format('F'); // July
        $lastMonthName = $date->startOfMonth()->subMonth(1)->format('F'); // June

        return view('donor.profile',compact('profile_data','donor_id','currentmonthgift','premonthgift','lastMonthName','currentMonthName'));
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

                unlink("images/".$userdata->photo);
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
}
