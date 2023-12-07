@component('mail::message')

<h2>Dear,</h2>

<p>You have received a urgent request from Mr {{$array['name']}}</p>

Thank You,<br>
{{ config('app.name') }}
@endcomponent