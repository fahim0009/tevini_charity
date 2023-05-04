<?php

namespace App\Http\Controllers;

use App\Models\Charity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

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
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $input = $request->all();

        $chksts = Charity::where('email', $input['login'])->orwhere('acc_no',$input['login'])->first();
        if ($chksts) {
            if ($chksts->status == 1) {
                
                $fieldType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'acc_no';
    
                $loginData = [
                    $fieldType => $request->input('login'),
                    'password' => $request->input('password')
                ];
            
                if(auth()->guard('charity')->attempt($loginData)){
                    return redirect()->route('charityDashboard');
                }else {
                    return back()->with('error','Whoops! invalid email/ account no and password.');
                }

            }else{
                return view('frontend.charity.charity_login')
                ->with('message','Your ID is Deactive.');
            }
        }else {
            return view('frontend.charity.charity_login')
                ->with('message','Credential Error. You are not authenticate user.');
        }
        
    
        

    }

    public function charityRegistraion()
    {
        return view('frontend.charity.charity_register');
    }

    public function charityRegistraionStore(Request $request)
    {

        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:charities'],
            'phone' => 'required',
            'password' => ['required','min:6'],
            'confirm_password' => 'required|same:password',
        ]);
        
        $data = new Charity();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->town = $request->town;
        $data->number = $request->phone;
        $data->post_code = $request->postcode;
        $data->password = Hash::make($request->password);

        if(isset($request->bank_statement)){
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->bank_statement->extension();
            $request->bank_statement->move(public_path('images'), $imageName);
            $data->bank_statement= $imageName;
        }

        $data->save();
        // return redirect()->route('login');
        // return back()->with('message', "Charity Registration Successful. Please Login"); 
        return Redirect::back()->withErrors(['msg' => 'Charity Registration Successful.']);
    }


}
