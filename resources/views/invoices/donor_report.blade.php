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
        text-align: center;
        font-weight: 600;
        font-size: 1.1rem;
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
    .wrapper .heading .subHead .center {
        text-align: center;
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
        margin-top: 50px;
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

    .text-right{
        text-align: right;
    }
 /* .wrapper .tableData table tr:nth-child(even) {
	 background: #436784 14;
} */

   </style>
</head>
<body>
    <div class="wrapper">



        <div class="heading">
            <div class="subHead">
                <div class="center">
                    <div class="logo">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}" width="120px" style="display:inline-block;"  alt="Tevini Limited"/>   
                    </div>
                    
                    <div class="title">
                        Donor report
                    </div>

                    <div class="sub-title">
                        Tevini Limited
                    </div>
                    Registered charity no. 282079 <br>
                    5A Holmdale Terrace,
                    N156PP
                </div>
            </div>


            <div class="subHead">
                <div class="left">

                    {{-- <b> Date:</b>	{{date('d-m-Y')}} <br> --}}
                    <b>Date:
                    {{ \Carbon\Carbon::parse($fromDate)->format('d/m/y') }} -
                    {{ \Carbon\Carbon::parse($toDate)->format('d/m/y') }}</b>

                </div>

                <div class="right">
                    Receipt <b>#@php echo(rand(100,999));  @endphp</b> <br> 

                </div>
            </div>

            <?php
                $tbalance = 0;
                $amountin = 0;
                $amountout = 0;
                $pending = 0;
            ?>
            {{-- money in / money out / pending cal  --}}
            @foreach ($report as $data)
                @php
                if($data->t_type == "In"){
                    if($data->commission != 0){
                    $amountin = $amountin + $data->amount + $data->commission;
                    }else {
                    $amountin = $amountin + $data->amount;
                    }

                }
                @endphp

                @php
                if($data->t_type == "Out"){
                    if($data->pending != "0"){
                        $amountout = $amountout + $data->amount;
                    }else {
                        $pending = $pending + $data->amount;

                    }
                }
                @endphp

            @endforeach

              {{-- total balance cal  --}}
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

            <div class="subHead">
                <div class="left">
                    <h3>Account Summery</h3>
                    <p>
                        Name: {{$user->name}}<br>
                        Address: {{$user->street}} {{$user->town}}<br>
                        Balance: {{ $tbalance < 0 ? '-' : '' }}£{{ number_format(abs($tbalance), 2) }}<br>
                        Money In: £{{ number_format($amountin, 2) }}<br>
                        Money Out: £{{ number_format($amountout, 2) }}<br>
                        Pending Amount: £{{ number_format($pending, 2) }}
                    </p>

                </div>

            </div>
        </div>



        <div class="tableData">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction type</th>
                        <th>Voucher Number</th>
                        <th>Charity Name</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th>Donate By</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Balance</th>
                    </tr>
                </thead>


            <tbody>
                  @foreach ($report as $data)
                    @if($data->commission != 0)
                    <tr style="font-size: 12px;">
                        <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                        <td>Commission</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">-£{{$data->commission}}</td>
                        <td class="text-right">{{ $tbalance < 0 ? '-' : '' }}£{{ number_format(abs($tbalance), 2) }}</td>
                        @php
                        $tbalance = $tbalance + $data->commission;
                        @endphp
                    </tr>
                    @endif


                    <tr style="font-size: 12px;">
                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                        <td style="width: 10%">{{ implode(' ', array_slice(explode(' ', $data->title), 0, 3)) }}</td>
                        
                        <td style="width: 10%">{{$data->cheque_no}} </td>
                        <td style="width: 10%">

                            @if($data->charity_id){{ $data->charity->name}}@endif
                            @if($data->crdAcptID)
                                {{ explode('~', $data->crdAcptLoc)[0] }}
                            @endif
                        
                        </td>
                        <td>@if($data->pending == "0") Pending @endif</td>
                        
                                            <td>
                                                {{$data->donation_id ? $data->donation->mynote : $data->note }} <br>
                                                {{$data->donation_id ? $data->donation->charitynote : ''}}
                                            </td>
                                            
                        <td>{{$data->donation_by ?? ""}} </td>

                            @if($data->t_type == "In")
                                @if($data->commission != 0)
                                    <td class="text-right">£ {{ number_format($data->amount + $data->commission, 2) }} </td>
                                    <td></td>
                                    <td class="text-right">{{ $tbalance < 0 ? '-' : '' }}£{{ number_format(abs($tbalance), 2) }}</td>
                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                @else
                                    <td class="text-right">£{{number_format($data->amount, 2)}} </td>
                                    <td></td>
                                    <td class="text-right">{{ $tbalance < 0 ? '-' : '' }}£{{ number_format(abs($tbalance), 2) }}</td>
                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                @endif
                            @elseif($data->t_type == "Out")
                                <td></td>
                                <td class="text-right">-£{{number_format($data->amount, 2) }}</td>
                                <td class="text-right">{{ $tbalance < 0 ? '-' : '' }}£{{ number_format(abs($tbalance), 2) }}</td>
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
                        <td></td>
                        <td>Previous Balance</td>
                        <td class="text-right">{{ $tbalance < 0 ? '-' : '' }}£{{ number_format(abs($tbalance), 2) }}</td>
                    </tr>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
