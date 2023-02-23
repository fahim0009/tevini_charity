   <div class="container">

   <h3>Dear Mr {{$array['name']}},</h3>

   <p>This message is to confirm that you have made the request below via the Tevini  website. It will be dealt with in due course.</p>

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
                            <td>£{{ $order->voucher->amount }} {{$order->voucher->type}} @if($order->voucher->note)(£{{$order->voucher->single_amount}} of{{$order->voucher->note}})@endif</td>
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
