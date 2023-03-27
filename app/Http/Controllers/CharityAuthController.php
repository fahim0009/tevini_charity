<?php

namespace App\Http\Controllers;

use App\Models\Charity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class CharityAuthController extends Controller
{
    use AuthenticatesUsers;
    
    protected $redirectTo = '/home';


    public function __construct()
    {
        $this->middleware('guest:charity')->except('logout');
    }

    public function login2(Request $request)
    {
        // Validate form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        // $check = Admin::where($where)->get()->first('user_type');
        $check = Charity::where('email', $request->email)->first();


        if($check){

            if(Auth::guard('charities')->attempt(['email' => $request->email, 'password' => $request->password]))
            {
                // return redirect()->intended(route('admin.dashboard'));
                dd('view');
            }

        }else{
            
            return redirect()->intended(route('charity_loginshow'));
        }
        // if unsuccessful
        $errors = 'Provided credentials is not corect';
        return redirect()->back()->withErrors($errors)->withInput($request->only('email','remember'));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(auth()->guard('charity')->attempt(['email' => $request->input('email'),  'password' => $request->input('password')])){

            return redirect()->route('charityDashboard');

        }else {
            return back()->with('error','Whoops! invalid email and password.');
        }
    }
}
