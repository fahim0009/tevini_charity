<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Charity Daily Statement</title>
    <style>
        @page { margin: 10px; }
        body { font-family: sans-serif; margin: 10px; color: #436784; }
        .wrapper { padding: 15px; }
        .center { text-align: center; }
        .logo { margin-bottom: 10px; }
        .title { font-weight: 600; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }
        .subHead { margin: 20px 0; display: table; width: 100%; }
        .split { display: table-cell; width: 50%; vertical-align: top; }
        .text-right { text-align: right; }
        .tableData { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; }
        table tr th { background-color: #436784; color: azure; padding: 5px; font-size: 12px; }
        table tr td { padding: 5px; border-bottom: 1px solid #ebebeb; color: #625f5f; font-size: 11px; text-align: center; }
        .summary-box { background: #f9f9f9; padding: 10px; border-left: 4px solid #436784; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="center">
            <div class="logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}" width="120px"/>
            </div>
            <div class="title">Remittance Report</div>
            <div>Tevini Limited | Registered charity no. 282079</div>
        </div>

        <div class="subHead">
            <div class="split">
                <div class="summary-box">
                    <strong>Charity Details:</strong><br>
                    {{ $charity->name }}<br>
                    Reg No: {{ $charity->reg_no ?? 'N/A' }}
                </div>
            </div>
            <div class="split text-right">
                <strong>Statement Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}<br>
                <strong>Receipt:</strong> #{{ rand(1000, 9999) }}
            </div>
        </div>

        <div class="tableData">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Donor Name</th>
                        <th>Type</th>
                        <th>Voucher #</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalAmount = 0;
                    @endphp

                    @foreach($details as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $row->user ? $row->user->name . ' ' . $row->user->surname : 'Anonymous' }}</td>
                        <td>
                            @if($row->donation_id) Online Donation
                            @elseif($row->standing_donationdetails_id) Standing Order
                            @elseif($row->cheque_no) Voucher 
                            @else Campaign @endif
                        </td>
                        <td>{{ $row->cheque_no ?? '-' }}</td>
                        <td>{{ $row->note ?? '-' }}
                            @if($row->donation_id) {{$row->donation->mynote ?? ''}} <br>  {{$row->donation->charitynote ?? ''}}
                            @elseif($row->standing_donationdetails_id) {{$row->standingDonationDetails->StandingDonation->mynote ?? ''}} <br>  {{$row->standingDonationDetails->StandingDonation->charitynote ?? ''}} 
                            @elseif($row->campaign_id) {{ $row->campaign->campaign_title ?? '' }} 
                            @elseif($row->cheque_no) Voucher ({{ $row->cheque_no }})
                            @else Campaign  @endif
                        </td>
                        <td>£{{ number_format($row->amount, 2) }}</td>
                    </tr>
                    @php
                        $totalAmount += $row->amount; 
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background: #eee;">
                        <td colspan="5" class="text-right">Total Payment:</td>
                        <td>£{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="margin-top: 30px; font-size: 10px; text-align: center;">
            This is a consolidated statement of your daily income. Funds are processed according to your payment schedule.
        </div>
    </div>
</body>
</html>