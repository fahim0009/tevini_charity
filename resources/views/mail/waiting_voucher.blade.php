@component('mail::message')


<h2>Dear {{ $array['donor']->name}},</h2>

<p>The attached voucher is waiting for your confirmation, please click on the link to sign in to your Tevini account and confirm or decline the voucher in the 'pending confirmation' section.</p>

<p>If we do not hear from you within 7 working days of this email the voucher will automatically clear.</p>


@component('mail::button', ['url' => url('https://www.tevini.co.uk/user/process-voucher')])
Click here
@endcomponent



Tevini


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
