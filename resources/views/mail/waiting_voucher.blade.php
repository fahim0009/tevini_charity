@component('mail::message')


<h2>Dear {{ $array['donor']->name}},</h2>

<p>The waiting vouchers are listed in the attached file. Please check and confirm.</p>

<p>To confirm, please login to your dashboard and click on "Waiting Voucher" on the left side.</p>


@component('mail::button', ['url' => url('https://www.tevini.co.uk/user/process-voucher')])
Click here
@endcomponent



Tevini


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
