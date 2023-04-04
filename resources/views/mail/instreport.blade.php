@component('mail::message')


<h2>Dear {{ $array['charity']->name}},</h2>
<p>We have processed your vouchers, Please find your remittance report attached. Your funds will be sent in due course. 
Tevini</p>




{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
