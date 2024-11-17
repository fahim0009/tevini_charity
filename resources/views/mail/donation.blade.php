@component('mail::message')

<h3>Dear {{$array['name']}},</h3> 
     
<p>This message is to confirm that we have received your request via the Tevini website. It will be processed shortly.</p>


@component('mail::table')
|          |           |
|:------:  |:---------:|
|Client number|{{$array['client_no']}}|
|Request Date |{{date('d-m-Y')}}|
|  Charity |{{$array['charity_name']}}|
|  Amount  |£{{$array['amount']}}|
@endcomponent

<p>If you have not requested this, please contact us immediately. </p>

<br>
Kind Regards, <br>
P. Schlesinger <br>
<br><br>
Tevini Ltd<br>
5A Holmdale Terrace<br>
London<br>
N15 6PP<br>
M. 07490956227<br>
E. Tevinivouchers@gmail.com<br>
W. www.tevini.co.uk<br>
@endcomponent