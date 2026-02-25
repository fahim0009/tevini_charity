<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\Validator;
use App\Models\Charity;
use App\Models\Transaction;
use App\Models\Usertransaction;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function profile()
    {
        $profile_data= Auth::user();
        return view('admin.profile')->with('profile_data', $profile_data);
    }

    public function adminProfileUpdate(Request $request)
    {
        $userdata= Auth::user();
        $userdata->name= $request->name;
        $userdata->email= $request->email;
        $userdata->phone= $request->phone;
        $userdata->city= $request->city;
        $userdata->country= $request->country;

        if ($userdata->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Profile Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }

    }

    public function changeAdminPassword(Request $request)
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



        public function adminImageUpload(Request $request, $id)
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

    public function adminindex()
    {
        $roles = Role::all();
        $accounts = User::where('is_type','=', 'admin')->get();

        return view('admin.register.index')->with('accounts',$accounts)->with('roles',$roles);
    }

    public function adminstore(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Password\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password === $request->cpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        try{
            $account = new User();
            $account->name = $request->name;
            $account->email = $request->email;
            $account->is_type = "admin";
            $account->phone = $request->phone;
            $account->password = Hash::make($request->input('password'));

            $account->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Admin Account Created Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);

        }
    }

    public function adminedit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = User::where($where)->get()->first();
        return response()->json($info);
    }

    public function adminupdate(Request $request, $id)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->cpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $where = [
            'id'=>$request->registerid
        ];
        
        //$userData = User::find($id);
        $userData = User::where($where)->get()->first();

        if(isset($request->password)){
        $userData->name = request('name');
        $userData->email = request('email');
        $userData->is_type =   "admin";
        $userData->phone = request('phone');
        $userData->password = Hash::make($request->input('password'));

        }else{

        $userData->name = request('name');
        $userData->email = request('email');
        $userData->is_type =   "admin";
        $userData->phone = request('phone');
        
        }

        if ($userData->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Admin Account Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    public function admindestroy($id)
    {

        if(User::destroy($id)){
            return response()->json(['success'=>true,'message'=>'User has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }


    public function agentindex()
    {
        $roles = Role::all();
        $accounts = User::where('is_type','=', 'agent')->get();

        return view('admin.register.agent')->with('accounts',$accounts)->with('roles',$roles);
    }

    public function agentstore(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Password\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password === $request->cpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        try{
            $account = new User();
            $account->name = $request->name;
            $account->email = $request->email;
            $account->is_type = "agent";
            $account->phone = $request->phone;
            $account->password = Hash::make($request->input('password'));

            $request->validate([
                'c_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->c_image->extension();
            $request->c_image->move(public_path('certificate'), $imageName);
            $account->c_image= $imageName;

            $account->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Agent Account Created Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);

        }
    }

    public function agentedit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = User::where($where)->get()->first();
        return response()->json($info);
    }

    public function agentupdate(Request $request, $id)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->cpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $where = [
            'id'=>$request->registerid
        ];
        
        //$userData = User::find($id);
        // $userData = User::where($where)->get()->first();
        $userData = User::find($id);
        //new code start
        if($request->c_image != 'null'){
            $image_path = public_path('certificate').'/'.$userData->c_image;
            unlink(public_path('certificate').'/'.$userData->c_image);
            
            $userData->name= $request->name;
            $userData->email= $request->email;
            $userData->is_type=  "agent";
            $userData->phone= $request->phone;
            if($request->password != null && ($request->password == $request->cpassword)){
                $userData->password = Hash::make($request->password);
            }
            // $userData->password= Hash::make($request->input('password'));
            $request->validate([
                'c_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->c_image->extension();
            $request->c_image->move(public_path('certificate'), $imageName);
            $userData->c_image= $imageName;
        }else{
            $userData->name= $request->name;
            $userData->email= $request->email;
            $userData->is_type=  "agent";
            $userData->phone= $request->phone;
            if($request->password != null && ($request->password == $request->cpassword)){
                $userData->password = Hash::make($request->password);
            }
            // $userData->password= Hash::make($request->input('password'));
        }

        //new code end



        // if(isset($request->password)){
        // $userData->name = request('name');
        // $userData->email = request('email');
        // $userData->is_type =   "agent";
        // $userData->phone = request('phone');
        // $userData->password = Hash::make($request->input('password'));
        // }else{
        // $userData->name = request('name');
        // $userData->email = request('email');
        // $userData->is_type =   "agent";
        // $userData->phone = request('phone');
        // }

        if ($userData->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Agent Account Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    // public function agentCertificateUpdate(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);
    //     $imageName = time().'.'.$request->image->extension();
    //     $request->image->move(public_path('certificate'), $imageName);
    //     /* Store $imageName name in DATABASE from HERE */
    //     $photo = User::find(Auth::User()->id);
    //     $photo->c_image= $imageName;
    //     $photo->save();
    //     return back()
    //         ->with('success','You have successfully upload image.')
    //         ->with('image',$imageName);
    // }

    public function agentdestroy($id)
    {

        if(User::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Agent has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }


    public function userindex()
    {
        $roles = Role::all();
        $accounts = User::where('is_type','=', 'user')->get();

        return view('admin.register.user')->with('accounts',$accounts)->with('roles',$roles);
    }

    public function userstore(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Password\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password === $request->cpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        try{
            $account = new User();
            $account->name = $request->name;
            $account->email = $request->email;
            $account->is_type = "user";
            $account->phone = $request->phone;
            $account->password = Hash::make($request->input('password'));

            $account->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>User Account Created Successfully.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message]);
        }catch (\Exception $e){
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);

        }
    }

    public function useredit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = User::where($where)->get()->first();
        return response()->json($info);
    }

    public function userupdate(Request $request, $id)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->cpassword)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $where = [
            'id'=>$request->registerid
        ];
        
        //$userData = User::find($id);
        $userData = User::where($where)->get()->first();

        if(isset($request->password)){
        $userData->name = request('name');
        $userData->email = request('email');
        $userData->is_type =   "user";
        $userData->phone = request('phone');
        $userData->password = Hash::make($request->input('password'));

        }else{

        $userData->name = request('name');
        $userData->email = request('email');
        $userData->is_type =   "user";
        $userData->phone = request('phone');
        
        }

        if ($userData->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>User Account Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    public function userdestroy($id)
    {

        if(User::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Agent has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function getUserDeleteRequest()
    {
        $users = User::whereHas('accDelRequest')->orderby('id','DESC')->get();
        return view('donor.deldonor', compact('users'));
    }


    public function auditBalances2()
    {
        $startDate = \Carbon\Carbon::parse('2026-02-09');
        $today = \Carbon\Carbon::parse('2026-02-22');
        $report = [];

        // 1. Get charities that were active in this period
        $charities = Charity::whereHas('usertransaction', function($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        })->get();

        // 2. Loop through each day from start until now
        for ($date = clone $startDate; $date->lte($today); $date->addDay()) {
            $dayStart = (clone $date)->startOfDay();
            $dayEnd = (clone $date)->endOfDay();

            foreach ($charities as $charity) {
                // Money that came IN that day (Status 1)
                $dailyIn = \App\Models\Usertransaction::where('charity_id', $charity->id)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('amount');

                // Money that went OUT that day (The payout record)
                $dailyOut = \App\Models\Transaction::where('charity_id', $charity->id)
                    ->where('t_type', 'Out')
                    ->where('status', 1)
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('amount');

                // If there was any activity on this day, add to report
                if ($dailyIn > 0 || $dailyOut > 0) {
                    // Difference for this specific day
                    $diff = $dailyIn - $dailyOut; 

                    $report[] = [
                        'date' => $date->toDateString(),
                        'charity_id' => $charity->id,
                        'name' => $charity->name,
                        'in' => $dailyIn,
                        'out' => $dailyOut,
                        'diff' => $diff,
                        // Typically 'In' should equal 'Out' if your cron runs daily
                        'is_clean' => abs($diff) < 0.01 
                    ];
                }
            }
        }

        // Sort report by date descending (newest first)
        $report = collect($report)->sortByDesc('date')->values()->all();

        return view('admin.audit_balances', compact('report'));
    }






    public function auditBalances(Request $request)
    {
        if ($request->ajax()) {

            // 1. Setup Date Ranges
        $startDate = $request->get('fromDate') ?? '2026-02-09';
        $endDate = $request->get('toDate') ?? '2026-02-22';

        $cutoffTime = '16:31:00';
        $cutoffTime2 = '16:30:00';

        // Business Date Logic String
        $businessDateRaw = "DATE(CASE 
            WHEN TIME(usertransactions.created_at) >= '$cutoffTime' 
            THEN DATE_ADD(usertransactions.created_at, INTERVAL 1 DAY) 
            ELSE usertransactions.created_at END)";

        // 2. Paid Subquery (ensure same logic)
        $paidSubquery = DB::table('transactions')
            ->select(
                DB::raw("DATE(CASE WHEN TIME(created_at) >= '$cutoffTime' THEN DATE_ADD(created_at, INTERVAL 1 DAY) ELSE created_at END) as pay_date"),
                'charity_id',
                DB::raw('SUM(amount) as total_paid'),
                DB::raw('MAX(bank_payment_status) as current_status')
            )
            ->where('status', 1)
            ->where('t_type', 'Out')
            ->groupBy('pay_date', 'charity_id');

        // 3. Main Query
        $query = Usertransaction::query()
            ->whereNotNull('usertransactions.charity_id')
            // FIX: Use whereRaw for the hardcoded audit range
            ->whereRaw("$businessDateRaw BETWEEN ? AND ?", [$startDate, $endDate])
            ->select([
                DB::raw("$businessDateRaw as date_group"),
                'usertransactions.charity_id',
                DB::raw("SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as online_sum"),
                DB::raw("SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as standing_sum"),
                DB::raw("SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as voucher_sum"),
                DB::raw("SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as campaign_sum"),
                DB::raw("IFNULL(MAX(paid_data.total_paid), 0) as paid_sum"),
                DB::raw("IFNULL(MAX(paid_data.current_status), 0) as payment_status")
            ])
            ->leftJoinSub($paidSubquery, 'paid_data', function ($join) use ($cutoffTime2) {
                $join->on(DB::raw("DATE(CASE WHEN TIME(usertransactions.created_at) >= '$cutoffTime2' THEN DATE_ADD(usertransactions.created_at, INTERVAL 1 DAY) ELSE usertransactions.created_at END)"), '=', 'paid_data.pay_date')
                     ->on('usertransactions.charity_id', '=', 'paid_data.charity_id');
            })
            ->groupBy('date_group', 'usertransactions.charity_id')
            ->orderByRaw('date_group DESC')
            ->with('charity');



            return DataTables::of($query)
                ->addColumn('date_group', fn($row) => '<span data-raw="'.$row->date_group.'">'.\Carbon\Carbon::parse($row->date_group)->format('d/m/Y').'</span>')
                ->addColumn('charity_name', function ($row) {
                    $charity = $row->charity;
                    $name = $charity->name ?? 'N/A';
                    $balance = $charity->balance ?? '0';
                    $style = ($charity && $charity->auto_payment == 0) ? 'color: #dc3545;' : 'color: #28a745;';
                    return '<span style="'.$style.' font-weight: bold;">'.$name.' ('.$balance.')</span>';
                })
                ->addColumn('balance', function ($row) {
                    $total = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum;
                    return '£' . number_format($total - $row->paid_sum, 2);
                })
                ->addColumn('action', function ($row) {
                    $total = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum;
                    $isChecked = ($row->payment_status == 1) ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input status-switch" type="checkbox" role="switch" '.$isChecked.' 
                                data-charity-id="'.$row->charity_id.'" data-date="'.$row->date_group.'" data-total="'.$total.'">
                            </div>';
                })
                
                ->editColumn('paid_sum', function($row) {
                    if ($row->paid_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    
                    return '<a href="javascript:void(0)" class="view-details text-success text-decoration-none fw-bold" 
                            data-type="paid" 
                            data-charity="'.$row->charity_id.'" 
                            data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">
                            £' . number_format($row->paid_sum, 2) . '
                            </a>';
                })

                ->editColumn('online_sum', function($row) {
                    if ($row->online_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="online" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->online_sum, 2) . '</a>';
                })
                ->editColumn('standing_sum', function($row) {
                    if ($row->standing_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="standing" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->standing_sum, 2) . '</a>';
                })
                ->editColumn('voucher_sum', function($row) {
                    if ($row->voucher_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="voucher" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->voucher_sum, 2) . '</a>';
                })
                ->editColumn('campaign_sum', function($row) {
                    if ($row->campaign_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="campaign" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->campaign_sum, 2) . '</a>';
                })



                ->rawColumns(['date_group', 'online_sum', 'standing_sum', 'voucher_sum', 'campaign_sum', 'paid_sum', 'charity_name', 'action'])
                ->make(true);
        }
        return view('admin.audit_balances');
    }


    public function getDayDetails(Request $request)
    {
        $cutoffHour = 16;
        $cutoffMinute = 31;

        // The date selected from the UI (Business Date)
        $businessDate = Carbon::createFromFormat('Y-m-d', $request->date);

        /**
         * START TIME: Previous Day at 16:31:00
         * Logic: (Selected Date - 1 Day) @ 16:31:00
         */
        $startDateTime = $businessDate->copy()
            ->subDay()
            ->setTime($cutoffHour, $cutoffMinute, 0);

        /**
         * END TIME: Today at 16:31:59
         * Logic: Selected Date @ 16:31:59
         */
        $endDateTime = $businessDate->copy()
            ->setTime($cutoffHour, $cutoffMinute, 59);

        /*
        |--------------------------------------------------------------------------
        | Query Implementation
        |--------------------------------------------------------------------------
        */
        if ($request->type == 'paid') {

            $data = Transaction::with('charity')
                ->where('charity_id', $request->charity_id)
                ->where('t_type', 'Out')
                // ->where('status', 1)
                // This will capture everything from 16:31:00 yesterday 
                // up to 16:31:59 today (inclusive)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->get();

            return response()->json($data->map(function($item) {
                return [
                    'donor'  => $item->charity->name ?? 'N/A',
                    'amount' => '£' . number_format($item->amount, 2),
                    'ref'    => $item->t_id,
                    'status' => $item->status,
                    'date'   => $item->created_at->format('d/m/Y H:i:s')
                ];
            }));
        }

        /*
        |--------------------------------------------------------------------------
        | CASE 2: Usertransactions
        |--------------------------------------------------------------------------
        */

        $query = Usertransaction::with('user')
            // ->where('status', 1)
            ->where('charity_id', $request->charity_id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($request->type == 'online')
            $query->whereNotNull('donation_id');

        if ($request->type == 'standing')
            $query->whereNotNull('standing_donationdetails_id');

        if ($request->type == 'voucher')
            $query->whereNotNull('cheque_no');

        if ($request->type == 'campaign')
            $query->whereNotNull('campaign_id');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'donor'  => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'amount' => '£' . number_format($item->amount, 2),
                'ref'    => $item->cheque_no ?? $item->t_id,
                'status' => $item->status,
                'date'   => $item->created_at->format('d/m/Y H:i')
            ];
        }));
    }



}
