<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background-color: #dc3545; color: #fff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .details { background: #f8f9fa; border-radius: 6px; padding: 20px; margin: 20px 0; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 10px 0; border-bottom: 1px solid #e9ecef; }
        .details td:first-child { font-weight: bold; width: 40%; color: #666; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .amount { color: #dc3545; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Voucher Declined</h1>
        </div>
        <div class="content">
            <p>Dear {{ $array['name'] }},</p>
            
            <p>We regret to inform you that your voucher has been <strong>declined</strong>. No further action will be taken regarding this voucher.</p>
            
            <div class="details">
                <table>
                    <tr>
                        <td>Voucher ID:</td>
                        <td>#{{ $array['voucher']->id }}</td>
                    </tr>
                    <tr>
                        <td>Cheque No:</td>
                        <td>{{ $array['voucher']->cheque_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Amount:</td>
                        <td class="amount">£{{ number_format($array['voucher']->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Declined Date:</td>
                        <td>{{ date('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            
        </div>


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