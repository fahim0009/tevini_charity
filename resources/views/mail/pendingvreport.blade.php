@component('mail::message')


<h6>Dear {{ $array['charity']->name}},</h6>
<p>We have processed your vouchers, Please find your remittance report attached.
Tevini</p>




{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent