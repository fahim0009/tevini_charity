@component('mail::message')


<h2>Dear {{ $array['charity']->name}},</h2>

<p>The waiting vouchers listed in the attached file have now been cleared and your charity will receive the funds in due course.

Tevini</p>


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
