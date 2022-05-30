@component('mail::message')

<h3>Dear Mr {{$array['name']}},</h3>

<p>Thank you for your kind donation.</p>
<p>Please check the attached.</p>




Thank You,<br>
{{ config('app.name') }}
@endcomponent
