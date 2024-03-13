@component('mail::message')

<h2>Dear {{$array['name']}},</h2>

<p>Thank you for your kind donation.</p>
<p>Please check the attached.</p>




Thank You,<br>
{{ config('app.name') }}
@endcomponent
