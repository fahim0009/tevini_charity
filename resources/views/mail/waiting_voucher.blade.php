@component('mail::message')


<h2>Dear {{ $array['donor']->name}},</h2>

<p>The waiting vouchers listed in the attached file. Please check and confirm.


Tevini</p>


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
