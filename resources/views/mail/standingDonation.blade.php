
<h3>Dear {{$array['name']}},</h3> 
     
<p>This is to confirm that a payment of <b>£{{$array['amount']}}</b>  has been made to <b>{{$array['charity']}}</b>  as per standing order.</p>


<p>
  <b>Date: </b> {{date('d-m-Y')}}, <br>
  <b>Charity: </b> {{$array['charity']}}, <br>
  <b>Amount: </b> £{{$array['amount']}}, <br>

</p>
<br>

Thanks,<br>
{{ config('app.name') }}
