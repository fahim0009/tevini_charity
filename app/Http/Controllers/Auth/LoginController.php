<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);


        
        if(is_numeric($request->get('email'))){
            
            if(auth()->attempt(array('accountno' => $input['email'], 'password' => $input['password'])))
            {

                if (auth()->user()->is_admin == 1) {
                    return redirect()->route('admin.dashboard');
                }if (auth()->user()->is_admin == 2) {
                    return redirect()->route('merchant.dashboard');
                }
                if (auth()->user()->is_admin == 0) {
                    return redirect()->route('home');
                }

            }else{
                return redirect()->route('login')
                    ->with('error','Incorrect email or password.');
            }
        }elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                
            if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
            {

                if (auth()->user()->is_admin == 1) {
                    return redirect()->route('admin.dashboard');
                }if (auth()->user()->is_admin == 2) {
                    return redirect()->route('merchant.dashboard');
                }
                if (auth()->user()->is_admin == 0) {
                    return redirect()->route('home');
                }

            }else{
                return redirect()->route('login')
                    ->with('error','Incorrect email or password.');
            }  

        } else {
            return redirect()->route('login')
                    ->with('error','Incorrect email or password.');
        }
        



        
    }
}
