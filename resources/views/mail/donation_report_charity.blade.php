@component('mail::message')

<h3>Dear {{$array['charity']->name}},</h3>

<p>You Have received an online donation, please find the attached statement. We will aim to send the funds in the next 2 working days, if you have not received the funds in this time please do not hesitate to contact us.</p>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
