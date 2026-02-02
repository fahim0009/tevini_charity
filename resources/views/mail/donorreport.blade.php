@component('mail::message')

<h3>Dear {{$array['name']}},</h3>

<p>Please find attached your Monthly Statement.</p>
<p>If your account shows a debit, we kindly request that you make a payment to top up your account.</p>
<p>Tevini Ltd <br>
Sort Code: 30-99-50 <br>
Account no: 80075460</p>

<p>Your account balance is: £{{$array['userbalance']}}</p>


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
