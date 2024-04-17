@component('mail::message')

<h2>Dear {{$array['name']}},</h2>

<p>You are receiving this email because you requested a TDF transfer, this should show up in your TDF account in the next 2 business working days.</p>

Regards <br>
{{ config('app.name') }}
@endcomponent