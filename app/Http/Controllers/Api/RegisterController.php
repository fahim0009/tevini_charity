<?php
   
namespace App\Http\Controllers\Api;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Charity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Validator;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'houseno' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'town' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:255'],
            'password' => [
                              'required',
                              Password::min(8)
                                  ->letters()
                                  ->mixedCase()
                                  ->numbers()
                                  ->symbols()
            ],
            
            'password_confirmation' => 'required|same:password'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['passwordchk'] = "1";
        $input['notification'] = "1";
        $input['status'] = "1";
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user();
            $data = User::where('id', Auth::user()->id)->first(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
            $success['data'] =  $data;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }


        $chksts = User::where('email', $request->email)->first();
        if ($chksts) {
            if ($chksts->status == 1) {
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 

                    $user = Auth::user(); 
                    $data = User::where('id', Auth::user()->id)->first(); 
                    $success['token'] =  $user->createToken('MyApp')->accessToken; 
                    $success['name'] =  $user->name;
                    $success['data'] =  $data;
                    $success['message'] =  'User login successfully.';
                    return response()->json($success,200);
                } 
                else{ 
                    return $this->sendError('Wrong Password!!.', ['error'=>'Wrong Password!!']);
                }
            }else{
                return $this->sendError('Your Account is deactive..', ['error'=>'Your Account is deactive.']);
            }
        }else {
            return $this->sendError('Credential Error. You are not authenticate user..', ['error'=>'Credential Error. You are not authenticate user.']);
        }

    }

    public function charity_login(Request $request)
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


                    $user = auth('charity')->user(); 
                    $data = Charity::where('id', auth('charity')->user()->id)->first(); 
                    $success['token'] =  $user->createToken('MyApp')->accessToken; 
                    $success['name'] =  $user->name;
                    $success['data'] =  $data;
                    $success['message'] =  'User login successfully.';
                    return response()->json($success,200);

                }else {
                    
                    return $this->sendError('Whoops! invalid email/account no and password.!!.', ['error'=>'Whoops! invalid email/account no and password.!!']);
                }

            }else{

                
                return $this->sendError('Your Account is deactive..', ['error'=>'Your Account is deactive.']);

            }
        }else {
            return $this->sendError('Credential Error. You are not authenticate user..', ['error'=>'Credential Error. You are not authenticate user.']);
        }
        
    
        

    }


    public function userDetails()
    {
        $data = User::where('id', auth()->user()->id)->first();
        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

    
}