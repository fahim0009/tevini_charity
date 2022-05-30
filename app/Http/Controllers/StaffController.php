<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Role;
use Hash;
use Auth;
use DB;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $staffs = DB::table('staff')
                ->join('users', 'users.id', '=', 'staff.user_id')
                ->join('roles', 'roles.id', '=', 'staff.role_id')
                ->select('users.*','staff.*','roles.id as rid','roles.name as rname','roles.permissions')
                ->get();
        $roles = Role::all();
        return view('staff.index')->with('staffs',$staffs)->with('roles',$roles);
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

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_type = "staff";
        $user->password = Hash::make($request->password);
        if($user->save()){
            $staff = new Staff;
            $staff->user_id = $user->id;
            $staff->role_id = $request->role;
            if($staff->save()){
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Staff Created Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }
        }

        return response()->json(['status'=> 303,'message'=>'Server Error!!']);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info = DB::table('staff')
                ->join('users', 'users.id', '=', 'staff.user_id')
                ->join('roles', 'roles.id', '=', 'staff.role_id')
                ->select('users.name','users.email','staff.*','roles.id as rid','roles.name as rname','roles.permissions')
                ->where('staff.id', '=', $id)
                ->get()->first();

        return response()->json($info);;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        

        $staff = Staff::findOrFail($id);
        $user = $staff->user;
        $user->name = $request->name;
        $user->email = $request->email;
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }
        if($user->save()){
            $staff->role_id = $request->role;
            if($staff->save()){
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Staff Updated Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if( User::destroy(Staff::findOrFail($id)->user->id)){
            return response()->json(['success'=>true,'message'=>'Staff has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }
}
