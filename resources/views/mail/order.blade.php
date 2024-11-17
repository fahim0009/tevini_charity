   <div class="container">

   <h3>Dear {{$array['name']}},</h3>

   <p>Thank you for your voucher book order. <br>
    Your order is now being prepared.</p>

    <p> <b>Order details:</b></p>
   <p>Client number : {{$array['client_no']}}</p>
   <p>Delivery Option : {{ $array['delivery_option']}}</p>

   <p>Request Date : {{date('m-d-Y')}}</p>

    Voucher books :
        <table style="border: 1px solid black; width:400px;">
                <thead>
                    <tr style="border: 1px solid black">
                        <th>Voucher</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Total </th>
                    </tr>
                </thead>
                <tbody>
             @foreach (\App\Models\OrderHistory::where('order_id', $array['order_id'])->get() as $order)
                        <tr style="border: 1px solid black; text-align:center">
                            <td>£{{ $order->voucher->amount }} {{$order->voucher->type}} @if($order->voucher->note)(
                                @if ($order->voucher->single_amount > 0) £{{$order->voucher->single_amount}} of @endif
                                {{$order->voucher->note}}
                                )@endif</td>
                            <td>{{$order->number_voucher}}</td>
                            @if($order->voucher->type !="Prepaid")
                            <td></td>
                            <td></td>
                            @else
                            <td>£{{$order->amount / $order->number_voucher}}</td>
                            <td>£{{$order->amount}}</td>
                            @endif
                        </tr>
             @endforeach

                    </tbody>
                        </table>

    </div>


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
