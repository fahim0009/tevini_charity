<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMail;

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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'houseno' => ['required', 'string', 'max:255'],
            'streetname' => ['required', 'string', 'max:255'],
            'town' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:255'],
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
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'houseno' => $data['houseno'],
            'street' => $data['streetname'],
            'town' => $data['town'],
            'postcode' => $data['postcode'],
            'notification' => 1,
            'status' => 0,

        ]);


    //   $mail_to_send_to = $data['email'];

        // $from_email = "info@tevini.co.uk";
        // $subject= "Registration message from Tevini";

        // $message= "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";

        // $message = $message. "\r\n" ;//add message from the contact form to existing message(name of the client)
        // $headers = "From: $from_email" . "\r\n" . "Reply-To: $from_email"  ;
        // $a = mail( $mail_to_send_to, $subject, $message, $headers );

        $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['name'] = $data['name'];
            $array['subject'] = 'Welcome to Tevini';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $email = $data['email'];

            Mail::send('mail.register', compact('array'), function($message)use($array,$email) {
             $message->from($array['from'], 'Tevini.co.uk');
             $message->to($email)->cc($array['cc'])->subject($array['subject']);
            });

        return $user;
    }

}
