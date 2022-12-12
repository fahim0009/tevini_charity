@component('mail::message')


<h6>Dear {{ $array['charity']->name}},</h6>

<p>The pending vouchers listed in the attached file have now been cleared and your charity will receive the funds in due course.

Tevini</p>


{{-- mail content end  --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
