
<h3>Dear {{$array['charity']}},</h3> 
     
<p>This is to confirm that  has been received of <b>£{{$array['amount']}}</b>  from Mr./Mrs. <b>{{$array['name']}}</b>  as per standing order.</p>


<p>
  <b>Date: </b> {{date('d-m-Y')}}, <br>
  <b>Amount: </b> £{{$array['amount']}}, <br>
</p>
<br>

Thanks,<br>
{{ config('app.name') }}
