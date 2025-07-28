<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMail;
use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function visitorContact(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $visitor_subject = $request->subject;
        $visitor_message = $request->message;

        $emailValidation = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,10}$/";

        if(empty($name)){
            $success['message'] = 'Please fill name field, thank you!';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }
        
        if(empty($email)){
            
            $success['message'] = 'Please fill email field, thank you!';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        if(!preg_match($emailValidation,$email)){
	    
            $success['message'] = "Your mail ".$email." is not valid mail. Please wirite a valid mail, thank you!";
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
            
        }
        
        if(empty($visitor_subject)){
            
            $success['message'] = 'Please fill subject field, thank you!';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        if(empty($visitor_message)){
            
            $success['message'] = 'Please write your query in message field, thank you!';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

      

        $contactmail = ContactMail::where('id', 1)->value('name') ?? 'info@tevini.co.uk';

        try {
            
            Mail::mailer('gmail')->to($contactmail)
                ->cc('info@tevini.co.uk')
                ->send(
                    new ContactUsMail(
                        $name,
                        $email,
                        $visitor_subject,
                        $visitor_message
                    )
                );

                
            $success['message'] = 'Message send successfully.';
            return response()->json(['success'=>true,'response'=> $success], 200);

        } catch (\Throwable $th) {
            $success['message'] = 'Problem with sending message !';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }


        }
}
