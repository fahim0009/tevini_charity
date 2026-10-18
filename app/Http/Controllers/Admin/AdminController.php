<?php
    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use App\Models\Role;
    use Illuminate\support\Facades\Auth;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Admin\Validator;
    use App\Models\AuditTran;
use App\Models\Authorisation;
use App\Models\Charity;
    use App\Models\Transaction;
    use App\Models\Usertransaction;
    use Illuminate\Support\Facades\DB;
    use Yajra\DataTables\Facades\DataTables;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Log;

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




    public function adjustTransaction(Request $request)
    {
        Log::info('Adjustment Started', [
            'transaction_id' => $request->transaction_id,
            'table' => $request->table_name,
            'type' => $request->type,
            'tran_type' => $request->tran_type,
            'amount' => $request->amount
        ]);

        $request->validate([
            'transaction_id' => 'required',
            'charity_id'     => 'required',
            'amount'         => 'required|numeric|min:0.01',
            'type'           => 'required|in:increment,decrement',
            'table_name'     => 'required|in:Charity,Transaction'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Fetch Records
                if ($request->tran_type === 'paid') {
                    $transaction = Transaction::findOrFail($request->transaction_id);
                } else {
                    $transaction = Usertransaction::findOrFail($request->transaction_id);
                }
                
                $charity = Charity::findOrFail($request->charity_id);
                Log::info('Records Found', [
                    'current_tran_amount' => $transaction->amount,
                    'current_charity_balance' => $charity->balance
                ]);

                // 2. Create Audit Log
                $audit = AuditTran::create([
                    'user_id'    => $transaction->user_id,
                    'charity_id' => $transaction->charity_id,
                    'date'       => $transaction->created_at->format('Y-m-d'),
                    'amount'     => $transaction->amount,
                    'tran_data'  => json_encode([
                        'adjustment_target' => $request->table_name,
                        'transaction' => $transaction->toArray(),
                        'charity_snapshot' => ['balance' => $charity->balance]
                    ]),
                    'status'     => $transaction->status,
                    'updated_by' => auth()->user()->name,
                    'created_by' => "Adj: $request->table_name ($request->type, $request->transaction_id)"
                ]);
                Log::info('Audit Record Created', ['audit_id' => $audit->id]);

                // 3. Logic for Charity Table
                if ($request->table_name === 'Charity') {
                    Log::info('Processing Charity Balance Adjustment');
                    if ($request->type === 'increment') {
                        $charity->balance += $request->amount;
                    } else {
                        $charity->balance -= $request->amount;
                    }
                    $charity->save();
                    Log::info('Charity Saved', ['new_balance' => $charity->balance]);
                }

                // 4. Logic for Transaction Table
                if ($request->table_name === 'Transaction') {
                    Log::info('Processing Transaction Amount Adjustment');
                    if ($request->type === 'increment') {
                        $transaction->amount += $request->amount;
                    } else {
                        $transaction->amount -= $request->amount;
                    }
                    $transaction->save();
                    Log::info('Transaction Saved', [
                        'new_amount' => $transaction->amount,
                        'new_status' => $transaction->status
                    ]);
                }

                Log::info('Adjustment Transaction Committed Successfully');

                return response()->json([
                    'success' => true,
                    'message' => "Successfully updated $request->table_name. Charity Balance: £" . number_format($charity->balance, 2)
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Adjustment Failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Adjustment failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function changeRealDonor(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $wrongUserId = $request->user_id;
                $realUserId  = $request->real_donor_id;
                $authId      = $request->authorization_id;
                $usertranid  = $request->usertranid;

                $wrongUser = User::findOrFail($wrongUserId);
                $realUser  = User::findOrFail($realUserId);

                $transaction = Usertransaction::where('t_id', 'LIKE', '%' . $usertranid)->first();

                if (!$transaction) {
                    throw new \Exception("Transaction not found");
                }

                $amount = $transaction->amount;

                /*
                -------------------------
                FIX BALANCES
                -------------------------
                */

                $wrongUser->increment('balance', $amount);
                $realUser->decrement('balance', $amount);

                /*
                -------------------------
                UPDATE TRANSACTIONS
                -------------------------
                */

                $transaction->update([
                    'user_id' => $realUserId
                ]);

                Transaction::where('t_id', $transaction->t_id)
                    ->update([
                        'user_id' => $realUserId
                    ]);

                /*
                -------------------------
                UPDATE AUTHORISATION
                -------------------------
                */

                Authorisation::where('Utid', $authId)
                    ->update([
                        'user_id' => $realUserId
                    ]);

            });

            return back()->with('success', 'Real donor updated successfully.');

        } catch (\Exception $e) {

            Log::error("Change Donor Error", [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }




}
