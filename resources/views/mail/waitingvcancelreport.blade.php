@component('mail::message')


<h2>Dear {{ $array['charity']->name}},</h2>

<p>The following voucher has been cancelled, for more information please contact us.</p>


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
