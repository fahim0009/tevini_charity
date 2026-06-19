<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Voucher Notification</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4A90E2; color: white; padding: 15px 20px; border-radius: 4px 4px 0 0; }
        .body { border: 1px solid #ddd; padding: 20px; border-radius: 0 0 4px 4px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 13px; }
        .pending  { background: #FFF3CD; color: #856404; }
        .approved { background: #D4EDDA; color: #155724; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        td { padding: 8px 12px; border: 1px solid #eee; }
        td:first-child { font-weight: bold; width: 40%; background: #f9f9f9; }
        .footer { margin-top: 20px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0;">Voucher Notification</h2>
    </div>
    <div class="body">
        <p>Dear {{ $user->name }},</p>
        <p>
            Your voucher has been received and is currently
            <span class="status-badge {{ $isPending ? 'pending' : 'approved' }}">
                {{ $isPending ? 'Pending Review' : 'Processed' }}
            </span>.
        </p>

        @if($isPending)
            <p>Your voucher is under review. This may be due to one of the following reasons:</p>
            <ul>
                <li>The voucher amount is ৳500 or above and requires additional approval.</li>
                <li>Your account limit is insufficient to cover this amount.</li>
                <li>The voucher is marked as waiting or expired.</li>
            </ul>
            <p>You will be notified once it has been reviewed.</p>
        @else
            <p>Your voucher has been successfully processed. Details are below:</p>
        @endif

        <table>
            <tr><td>Voucher / Cheque No</td><td>{{ $voucher->cheque_no }}</td></tr>
            <tr><td>Amount</td><td>৳{{ number_format($voucher->amount, 2) }}</td></tr>
            <tr><td>Batch No</td><td>{{ $voucher->batch_no }}</td></tr>
            <tr><td>Date</td><td>{{ now()->format('d M Y') }}</td></tr>
            <tr><td>Status</td><td>{{ $isPending ? 'Pending' : 'Approved' }}</td></tr>
        </table>

        <p style="margin-top:20px;">If you have any questions, please contact our support team.</p>
        <p>Thank you.</p>
    </div>
    <div class="footer">
        This is an automated email. Please do not reply directly to this message.
    </div>
</div>
</body>
</html>