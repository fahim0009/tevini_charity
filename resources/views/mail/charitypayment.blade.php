@component('mail::message')

<h3>Dear {{$array['name']}},</h3>

<p>We have made a payment of £{{$array['amount']}} transaction ID {{$array['t_id']}} with the following notes "{{$array['note']}}"</p>
<p>For more details on this payment please visit your charity's online portal here <a href="https://www.tevini.co.uk/charity_login">Tevini Login</a>  for login details please contact us.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
