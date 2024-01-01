<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMail;

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

      

        $contactmail = ContactMail::where('id', 1)->first()->name;

	    $mail_to_send_to = $contactmail;
	    
        $from_email = "info@tevini.co.uk";
        $subject= "New message from Tevini";

        $message= "\r\n" . "Name: $name" . "\r\n". "Subject: $visitor_subject" . "\r\n"; //get recipient name in contact form
        $message = $message.$visitor_message . "\r\n" ;//add message from the contact form to existing message(name of the client)
        $headers = "From: $from_email" . "\r\n" . "Reply-To: $email"  ;
        $a = mail( $mail_to_send_to, $subject, $message, $headers );
        
                if ($a)
                {
                    $success['message'] = 'Data updated successfully.';
                    return response()->json(['success'=>true,'response'=> $success], 200);

                } else {

                    $success['message'] = 'Problem with sending message !';
                    return response()->json(['success'=>false,'response'=> $success], 202);

                }
            }
}
