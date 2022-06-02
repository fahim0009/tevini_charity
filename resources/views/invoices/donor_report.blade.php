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
	 padding: 15px;
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
	 padding: 2px;
	 font-family: sans-serif;
	 border-right: 1px solid #fff;
}
 .wrapper .tableData table tr {
	 border-bottom: 1px solid #ebebeb;
}
 .wrapper .tableData table tr td {
	 padding: 2px;
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
                Donor report
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

            </div>
            <p class="donated">
                    <p>{{$user->name}}</p>
                    <p>{{$user->street}}</p>
                    <p>{{$user->town}}</p>
            </p>
        </div>
        <div class="tableData">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction Id</th>
                        <th>transaction type</th>
                        <th>Voucher Number</th>
                        <th>Charity Name</th>
                        <th>Status</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Balance</th>
                    </tr>
                </thead>

            <?php
                $tbalance = 0;
            ?>

              @foreach ($tamount as $data)
                    @if($data->commission != 0)
                        @php
                        $tbalance = $tbalance - $data->commission;
                        @endphp
                    @endif

                    @php
                    if($data->t_type == "In"){
                        if($data->commission != 0){

                        $tbalance = $tbalance + $data->amount + $data->commission;
                        }else {

                        $tbalance = $tbalance + $data->amount;
                        }

                    }
                    @endphp

                            @php
                            if($data->t_type == "Out"){
                            $tbalance = $tbalance - $data->amount;
                            }
                            @endphp
                @endforeach

            <tbody>
                {{-- <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Balance</td>
                    <td>£{{ number_format($tbalance, 2) }}</td>
                </tr> --}}
                @foreach ($report as $data)
                    @if($data->commission != 0)
                    <tr>
                        <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                        <td>{{$data->t_id}} </td>
                        <td>Commission</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>-£{{$data->commission}}</td>
                        <td>£{{ number_format($tbalance, 2) }}</td>
                        @php
                        $tbalance = $tbalance + $data->commission;
                        @endphp
                    </tr>
                    @endif
                    <tr>
                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                        <td>{{$data->t_id}} </td>
                        <td>{{$data->title}} </td>
                        <td>{{$data->cheque_no}}</td>
                        <td>@if($data->charity_id){{ $data->charity->name}}@endif</td>
                        <td>@if($data->pending == "0") Pending @endif</td>

                            @if($data->t_type == "In")
                                @if($data->commission != 0)
                                    <td>£ {{ number_format($data->amount + $data->commission, 2) }} </td>
                                    <td></td>
                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                @else
                                    <td>£{{number_format($data->amount, 2)}} </td>
                                    <td></td>
                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                @endif
                            @elseif($data->t_type == "Out")
                                <td></td>
                                <td>-£{{number_format($data->amount, 2) }}</td>
                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                    @if($data->pending != "0")
                                    @php  $tbalance = $tbalance + $data->amount;  @endphp
                                    @endif
                            @endif

                    </tr>
                @endforeach
                    <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Previous Balance</td>
                    <td>£{{ number_format($tbalance, 2) }}</td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
