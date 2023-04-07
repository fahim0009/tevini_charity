@component('mail::message')


<h2>Dear {{ $array['donor']->name}},</h2>

<p>The waiting vouchers listed in the attached file. Please check and confirm.</p>

@component('mail::button', ['url' => url('/user/process-voucher')])
Click here
@endcomponent

Tevini


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
