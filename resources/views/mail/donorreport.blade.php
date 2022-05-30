@component('mail::message')

<h3>Dear Mr {{$array['name']}},</h3>

<p>Please find attached statement.</p>



Thanks,<br>
{{ config('app.name') }}
@endcomponent
