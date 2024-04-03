@extends('frontend.layouts.master')

@section('content')




<div class="container">
    <br>
    <h3> Data declaration right</h3>

    

    <p>If a user contacts us via ( tevinivouchers@gmail.com ) for bug report or help & feedback, their email will be collected by us for the sole purpose of communication
        And resolving the issue. However. If the user wishes to exercise their data deletion right. They can request us to delete their email and associated data from our records by contacting us through the same email address.
        We will promptly respond to the user’s request take necessary actions to delete their from our records.</p>


        <h3> Instruction for requesting data deletion</h3>
        <p>
            To request data deletion users may send an email to (mail address) from the email address that is valid or registered with the app data.the subject 
            Line of the email should read “Data Deletion Request”  and the email should include all necessary details.</p>


            <p>Read Our  <a href="{{route('privacy')}}">Privacy Policy</a> to Understand data use 
                We take user’s privacy seriously and are committed to protecting their personal information. To	better understand how your data is used by us or our app. We encourage you to read our  <a href="{{route('privacy')}}">Privacy Policy</a> . Our policy provides a detailed explanation of the types data .we collect, how we use it.We take to ensure the security and confidence of your information.</p>

</div>

@endsection


