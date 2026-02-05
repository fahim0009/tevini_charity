<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #eee; max-width: 600px; margin: auto; }
        .header { background: #436784; color: white; padding: 10px; text-align: center; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tevini Ltd - Daily Statement</h2>
        </div>
        
        <p>Dear {{ $name }},</p>

        <p>Please find attached your <strong>Daily Transaction Statement</strong> for {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.</p>

        <p>The total amount consolidated for today is: <strong>£{{ $total }}</strong>.</p>

        <p>This amount has been deducted from your pending portal balance. It is currently marked as "Awaiting Payment" and will be transferred to your bank account shortly.</p>

        <p>If you have any questions regarding these transactions, please contact us.</p>

        <div class="footer">
            Kind Regards,<br>
            <strong>Tevini Ltd</strong><br>
            5A Holmdale Terrace, London, N15 6PP<br>
            W. <a href="http://www.tevini.co.uk">www.tevini.co.uk</a>
        </div>
    </div>
</body>
</html>