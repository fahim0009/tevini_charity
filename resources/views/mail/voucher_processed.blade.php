<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Verification Request</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            line-height: 1.7;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #003057;
            color: #ffffff;
            padding: 24px 30px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .body {
            padding: 30px;
        }
        .body p {
            margin: 0 0 16px 0;
            font-size: 14px;
        }
        .voucher-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        .voucher-details td {
            padding: 10px 14px;
            border: 1px solid #e8e8e8;
        }
        .voucher-details td:first-child {
            font-weight: 600;
            width: 40%;
            background-color: #f9fafb;
            color: #555;
        }
        .voucher-details td:last-child {
            color: #222;
        }
        .note-box {
            background-color: #FFF8E1;
            border-left: 4px solid #FFA000;
            padding: 14px 18px;
            margin: 24px 0;
            border-radius: 0 4px 4px 0;
            font-size: 13px;
            color: #6d4c00;
            line-height: 1.6;
        }
        .button-row {
            margin: 28px 0 10px 0;
            text-align: center;
        }
        .button-row table {
            width: 100%;
            border-collapse: collapse;
        }
        .button-row td {
            padding: 0 8px;
            vertical-align: top;
        }
        .btn {
            border: 0;
            width: auto;
            margin: 5px;
            border-radius: 7px;
            padding: 6px 30px;
            font-size: 19px;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-content: center;

        }
        .btn-accept {
            background-color: #003057;
            color: #ffffff;
            border: none;
        }
        .btn-accept:hover {
            background-color: #014074;
        }
        .btn-decline {
            background-color: #ffffff;
            color: #dc3545;
            border: 2px solid #dc3545;
        }
        .btn-decline:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .btn-label {
            display: block;
            font-size: 11px;
            font-weight: 400;
            margin-top: 6px;
            color: #999;
            letter-spacing: 0;
        }
        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 28px 0 20px 0;
        }
        .footer {
            padding: 20px 30px 28px 30px;
            font-size: 12px;
            color: #999;
            line-height: 1.7;
        }
        @media only screen and (max-width: 600px) {
            .container { margin: 0; border-radius: 0; }
            .body { padding: 20px; }
            .header { padding: 20px; }
            .footer { padding: 16px 20px 24px 20px; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h2>Voucher Verification Request</h2>
    </div>

    <div class="body">

        <p>Dear Valued Client,</p>

        <p>I hope you are doing well.</p>

        <p>We have recently received the attached voucher. Kindly review the voucher details below and let us know whether you wish to accept or decline the voucher.</p>

        <table class="voucher-details">
            <tr>
                <td>Voucher / Cheque No</td>
                <td>{{ $voucher->cheque_no }}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>£{{ number_format($voucher->amount, 2) }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>{{ $voucher->date ?? now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Charity Name</td>
                <td>{{ $charityName ?? 'N/A' }}</td>
            </tr>
        </table>

        <p style="text-align: center; font-size: 14px; font-weight: 600; color: #444; margin-bottom: 6px;">
            Do you recognise this voucher?
        </p>

        <div class="button-row">
            <table>
                <tr>
                    <td>
                        <a href="{{ $acceptUrl ?? '#' }}" class="btn btn-accept">Accept</a>
                        <span class="btn-label">I have written this voucher</span>
                    </td>
                    <td>
                        <a href="{{ $declineUrl ?? '#' }}" class="btn btn-decline">Decline</a>
                        <span class="btn-label">I do not recognise this voucher</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="note-box">
            <strong>Please note:</strong> If you ignore this email, the voucher will automatically be paid.
        </div>

        <p>If you have any questions or require further clarification regarding the voucher, please do not hesitate to contact us.</p>

    </div>

    <hr class="divider">

    <div class="footer">
        Kind Regards,<br>
        P. Schlesinger<br>
        <br>
        <strong>Tevini Ltd</strong><br>
        5A Holmdale Terrace<br>
        London, N15 6PP<br>
        M. 02038161694<br>
        E. info@tevini.co.uk<br>
        W. www.tevini.co.uk
    </div>

</div>
</body>
</html>