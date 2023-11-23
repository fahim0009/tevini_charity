@component('mail::message')

<h3>Dear {{$array['name']}},</h3>

<p>We have made a payment of £{{$array['amount']}} transaction ID {{$array['t_id']}} with the following notes "{{$array['note']}}"</p>
<p>Please kindly send a receipt of funds to this email.</p>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
