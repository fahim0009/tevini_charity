@component('mail::message')

<h3>Dear Mr {{$array['charity']->name}},</h3> 
     
<p>This message is to confirm that you have got a donation via the Tevini website. It will be deal with in due course.</p>


Thanks,<br>
{{ config('app.name') }}
@endcomponent