@component('mail::message')

<h3>Dear {{$array['name']}},</h3>

<p>Please find the attached statement.</p>
<p>if your balance is in debit please make a payment to top up your account.</p>




Thanks,<br>
{{ config('app.name') }}
@endcomponent
