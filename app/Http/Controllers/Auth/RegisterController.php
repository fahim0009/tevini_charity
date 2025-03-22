<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'profile_type' => ['required'],
            'name' => ['required_if:profile_type,Personal', 'nullable', 'max:255'],
            'company_name' => ['required_if:profile_type,Company', 'nullable', 'max:255'],
            'phone' => ['required', 'regex:/^((44)|(45)|(46))[0-9]{10}/','min:12'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'email:rfc,dns'],
            'houseno' => ['required', 'string', 'max:255'],
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
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        
        $num = $data['phone'];
        $chkctr = substr($num, 0, 2);
        $lastdigit = substr($num,-10);

        if ($chkctr == "44") {
            $prefix = "+44";
            $phone = $prefix.$lastdigit;
        }else{
            $phone = "+".$num;
        }
        if ($data['profile_type'] == "Company") {
            $name = $data['company_name'];
            $surname = $data['company_last_name'];
        } else {
            $name = $data['name'];
            $surname = $data['surname'];
        }
        

        

        $user =  User::create([
            'profile_type' => $data['profile_type'],
            'prefix_name' => $data['prefix_name'],
            'name' => $name,
            'surname' => $surname,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $phone,
            'houseno' => $data['houseno'],
            'street' => $data['streetname'],
            'address_third_line' => $data['address_third_line'],
            'town' => $data['town'],
            'postcode' => $data['postcode'],
            'passwordchk' => 1,
            'notification' => 1,
            'status' => 1,

        ]);

        // Generate email verification link
        $verificationUrl = $this->verificationUrl($user);
        $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['prefix_name'] = $data['prefix_name'];
            $array['name'] = $name;
            $array['subject'] = 'Welcome to Tevini';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $email = $data['email'];
            $array['verification_url'] = $verificationUrl;

            Mail::send('mail.register', compact('array'), function($message)use($array,$email) {
             $message->from($array['from'], 'Tevini.co.uk');
             $message->to($email)->cc($array['cc'])->subject($array['subject']);
            });


        return $user;
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }

}
