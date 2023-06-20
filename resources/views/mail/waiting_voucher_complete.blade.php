@component('mail::message')


<h2>Dear {{ $array['name']}},</h2>

<p>The following voucher number-{{$array['voucher_number']}}, amount: £{{ $array['amount']}} has now been cleared, your charity will receive the funds in due course.

Tevini</p>


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
