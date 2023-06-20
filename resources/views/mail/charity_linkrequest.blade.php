@component('mail::message')

<h3>Dear {{$array['name']}},</h3>

<p>You have a payment request of £{{$array['amount']}} from Charity {{$array['charity_name']}}.</p>

<p>Your support will truly invaluable and greatly appreciated.</p>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
