@component('mail::message')

<h2>Dear {{ $array['name'] }},</h2>

<p>This email is to confirm that we have received your <strong>OneGiv Card Order</strong>.</p>

<p>Your card will be shipped shortly to your registered address.</p>

@component('mail::table')
| Order Details | |
|:--|:--|
| **Order Number** | #{{ $array['order_number'] }} |
| **Card Holder** | {{ $array['card_holder'] }} |
| **Card Type** | {{ $array['card_type'] }} |
@if($array['amount'] > 0)
| **Card Amount** | £{{ $array['amount'] }} |
@endif
| **Order Date** | {{ $array['order_date'] }} |
@endcomponent

<br>

<p>If you have any questions regarding your order, please do not hesitate to contact us.</p>

<br>
Kind Regards,<br>
P. Schlesinger<br>
<br>
Tevini Ltd<br>
5A Holmdale Terrace<br>
London<br>
N15 6PP<br>
M. 02038161694<br>
E. info@tevini.co.uk<br>
W. www.tevini.co.uk<br>

@endcomponent