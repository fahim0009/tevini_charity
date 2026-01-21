@php
use Illuminate\Support\Carbon;
use app\Models\Provoucher;
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Report</title>
   <style>
       @page { margin: 10px; }
    body { margin: 10px; }
 * {
	 margin: 0;
	 padding: 0;
}
 .wrapper {
	 max-width: 960px;
	 margin: 0 auto;
	 box-shadow: rgba(0, 0, 0, 0.04) -1px 2px 20px 15px;
	 padding: 25px;
}
 .wrapper .heading .title {
	 text-align: right;
	 font-weight: 600;
	 font-size: 2.1rem;
	 text-transform: uppercase;
	 font-family: monospace;
	 letter-spacing: 1px;
	 color: #436784;
}
 .wrapper .heading .subHead {
	 margin: 20px 0;
	 display: flex;
	 justify-content: space-between;
}
 .wrapper .heading .subHead .left {
	 color: #436784;
	 text-transform: capitalize;
	 line-height: 1.3;
	 font-size: 16px;
	 font-family: sans-serif;
}
 .wrapper .heading .subHead .right {
	 text-align: right;
	 color: #436784;
	 text-transform: capitalize;
	 line-height: 1.3;
	 font-size: 16px;
	 font-family: sans-serif;
}
 .wrapper .heading .donated {
	 color: #436784;
	 font-family: sans-serif;
}
 .wrapper .tableData {
	 margin-top: 100px;
	 min-width: 400px;
	 overflow-x: auto;
}
 .wrapper .tableData table {
	 width: 100%;
	 text-align: center;
	 border-collapse: collapse;
}
 .wrapper .tableData table tr th {
	 background-color: #436784;
	 color: azure;
	 padding: 6px;
	 font-family: sans-serif;
	 border-right: 1px solid #fff;
}
 .wrapper .tableData table tr {
	 border-bottom: 1px solid #ebebeb;
}
 .wrapper .tableData table tr td {
	 padding: 6px;
	 color: #625f5f;
	 text-transform: capitalize;
}
 /* .wrapper .tableData table tr:nth-child(even) {
	 background: #436784 14;
} */


   </style>
</head>

<body>

    <div class="wrapper">

        <div class="heading">
            <div class="title">
                Remittance Report
            </div>
            <div class="subHead">
                <div class="left">
                    Tevini Limited <br>
                    Registered charity no. 282079 <br>
                    5A Holmdale Terracer<br>
                    N156PP
                </div>



                <div class="right">
                    Date: <b>@php echo date('d-m-Y'); @endphp </b> <br>
                    Receipt <b>#@php echo(rand(100,999));  @endphp</b>
                </div>

                @php
                $total_bal = 0;
                $total_pending = 0;
                @endphp

                @foreach ($remittance as $balcal)
                <?php $total_bal += $balcal->amount;?>
                    @if($balcal->status == 0)
                    <?php $total_pending += $balcal->amount;?>
                    @endif
                @endforeach

                <div class="left">
                    <h3>Summary of Funds:</h3>
                </div>

                <div class="left">
                    Amount: £{{ number_format($total_bal, 2)}}  <br>
                    Pending: £{{ number_format($total_pending, 2)}} <br>
                </div>
                <br>
                @php
                $previous_pending_net = $previous_pending - $total_pending;
                @endphp
                <div class="left">
                    Total wallet amount: £{{ number_format($charity->balance, 2)}}  <br>
                    Total Previous pending: £{{ number_format($previous_pending_net, 2)}} <br>
                </div>


            </div>
            <p class="donated">

                    <p>{{$charity->name}}</p>
                    <p>{{$charity->address}}</p>

            </p>
        </div>

        <div class="tableData">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Voucher number</th>
                        <th>Donor Name</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Expired </th>
                    </tr>
                </thead>

            <?php
                $total = $total;
            ?>


            <tbody>
            @php
                $total = $total;
                $tbalance = 0;
            @endphp

            @foreach ($remittance as $data)
            @if ($data->status == 1)
                    <tr>
                        <td><span style="display:none;">{{ $data->id }}</span>{{ $data->created_at->format('d/m/Y')}} </td>
                        <td>Vouchers </td>
                        <td>{{$data->cheque_no}}</td>
                        <td>{{$data->user->name}}</td>
                        <td> £{{ number_format($data->amount, 2) }}</td>
                        @if($data->status == 1)
                        <td> £{{ number_format($total+$tbalance, 2) }} </td>
                        <?php $tbalance = $tbalance - $data->amount;?>
                        @else
                        <td> £{{ number_format('0', 2) }} </td>
                        @endif
                        <td>
                        <!--Acc: No: {{$data->donor_acc}}; <br>-->
                            <!--Voucher No:*****-->
                        {{$data->note}}
                        </td>
                        <td>
                            @if($data->status == 1)
                            Complete
                            @elseif($data->status == 0 && $data->waiting == "Yes")
                            Pending confirmation
                            @elseif($data->status == 0 && $data->waiting == "No")
                            Pending
                            @elseif($data->status == 3)
                            Cancel
                            @endif
                        </td>
                        
                        <td>
                            @if($data->expired == 1)
                            Complete
                            @elseif($data->status == 0 && $data->expired == "Yes")
                            Expired
                            @elseif($data->status == 0 && $data->expired == "No")
                            Pending
                            @elseif($data->status == 3)
                            Cancel
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach

            @foreach ($remittance as $data)
            @if ($data->status != 1)
            <tr>
            <td><span style="display:none;">{{ $data->id }}</span>{{ $data->created_at->format('d/m/Y')}} </td>
            <td>Vouchers </td>
            <td>{{$data->cheque_no}}</td>
            <td>{{$data->user->name}}</td>
            <td> £{{ number_format($data->amount, 2) }}</td>
            @if($data->status == 1)
            <td> £{{ number_format($total+$tbalance, 2) }} </td>
            <?php $tbalance = $tbalance - $data->amount;?>
            @else
            <td> £{{ number_format('0', 2) }} </td>
            @endif
            <td>
            <!--Acc: No: {{$data->donor_acc}}; <br>-->
                <!--Voucher No:*****-->
               {{$data->note}}
            </td>
            <td>
                @if($data->status == 1)
                Complete
                @elseif($data->status == 0 && $data->waiting == "Yes")
                Pending confirmation
                @elseif($data->status == 0 && $data->waiting == "No")
                Pending
                @elseif($data->status == 3)
                Cancel
                @endif
            </td>
            <td>
                @if($data->expired == 1)
                Complete
                @elseif($data->status == 0 && $data->expired == "Yes")
                Expired
                @elseif($data->status == 0 && $data->expired == "No")
                Pending
                @elseif($data->status == 3)
                Cancel
                @endif
            </td>
         </tr>
        @endif
         @endforeach
            </tbody>
        </table>
        </div>

    </div>

</body>

</html>
