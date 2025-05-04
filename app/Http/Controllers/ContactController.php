<?php

namespace App\Http\Controllers;

use App\Models\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactUsMail;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact');
    }

    public function admincontact()
    {
        return view('contact.index');
    }
    
    public function contactMail()
    {
        $contactmail = ContactMail::all();
        return view('contact.contactmail',compact('contactmail'));
    }

    public function ContactmailEdit($id)
    {
        $mail = ContactMail::where('id','=' , $id)->first();
        return view('contact.editcontactmail', compact('mail'));
    }

    public function mailUpdate(Request $request, $id)
    {
        $user = ContactMail::findOrFail($id);
        $user->name = $request->name;
        if($user->save()){

            $message ="Contact mail Update Successfully";

        return redirect()->route('admin.contactmail')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }
    public function userContact()
    {
        return view('frontend.user.contact');
    }

    public function visitorContact_old(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $visitor_subject = $request->subject;
        $visitor_message = $request->message;

        $emailValidation = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,10}$/";

        if(empty($name)){
            $message ="<div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Please fill name field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(empty($email)){
            $message ="<div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Please fill email field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(!preg_match($emailValidation,$email)){
	    
            $message ="<div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Your mail ".$email." is not valid mail. Please wirite a valid mail, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            
        }
        
        if(empty($visitor_subject)){
            $message ="<div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Please fill subject field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($visitor_message)){
            $message ="<div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Please write your query in message field, thank you!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
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
                    $message ="<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Thanks for your message! We will get back to you soon :)
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                    return response()->json(['status'=> 303,'message'=>$message]);
                    exit();

                } else {

                    $message ="<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    Problem with sending message !
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                    return response()->json(['status'=> 303,'message'=>$message]);
                    exit();

                }
    }


    public function visitorContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            $message = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>".
                        $validator->errors()->first().
                        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";

            return response()->json(['status' => 303, 'message' => $message]);
        }

        $contactmail = ContactMail::where('id', 1)->value('name') ?? 'info@tevini.co.uk';

        try {


            Mail::mailer('gmail')->to($contactmail)->send(
                new ContactUsMail(
                    $request->name,
                    $request->email,
                    $request->subject,
                    $request->message
                )
            );

            $success = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Thanks for your message! We will get back to you soon :)
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";

            return response()->json(['status' => 200, 'message' => $success]);

        } catch (\Exception $e) {
            $error = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Problem with sending message! ({$e->getMessage()})
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";

            return response()->json(['status' => 303, 'message' => $error]);
        }
    }


}
