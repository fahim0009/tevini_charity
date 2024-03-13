@component('mail::message')

<h3>Dear {{$array['name']}},</h3> 
     
<p>This message is to confirm that you have made the request below via the Tevini website. It will be dealt with in due course.</p>


@component('mail::table')
|          |           |
|:------:  |:---------:|
|Client number|{{$array['client_no']}}|
|Request Date |{{date('d-m-Y')}}|
|  Charity |{{$array['charity_name']}}|
|  Amount  |£{{$array['amount']}}|
@endcomponent

<p>Your donation is being processed. If this is still the case after two working days please contact us by email.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent