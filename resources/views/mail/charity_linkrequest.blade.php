<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #18988B, #18988B);
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: rgba(255,255,255,0.9);
            margin: 10px 0 0 0;
            font-size: 14px;
        }
        .body {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 0 0 8px 8px;
        }
        .greeting {
            font-size: 18px;
            color: #003057;
            margin-bottom: 20px;
        }
        .amount-box {
            background: #FDF3EE;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
            border: 2px dashed #18988B;
        }
        .amount-box .label {
            font-size: 14px;
            color: #003057;
            margin-bottom: 5px;
        }
        .amount-box .amount {
            font-size: 36px;
            font-weight: 700;
            color: #18988B;
        }
        .charity-name {
            font-size: 16px;
            color: #003057;
            margin-top: 5px;
        }
        .donate-btn {
            display: inline-block;
            background: linear-gradient(135deg, #18988B, #18988B);
            color: #fff !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            margin: 30px 0;
        }
        .link-text {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            font-size: 12px;
            color: #003057;
            word-break: break-all;
            border: 1px solid #eee;
        }
        .note-box {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            font-style: italic;
            color: #003057;
        }
        
        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background: #FDF3EE;
            border-radius: 10px;
        }
        .qr-section .qr-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .qr-section .qr-image {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .qr-section .qr-image img {
            display: block;
            width: 180px;
            height: 180px;
        }
        .qr-section .qr-hint {
            font-size: 13px;
            color: #888;
            margin-top: 12px;
        }
        
        /* Two Column Layout */
        .two-col {
            display: table;
            width: 100%;
            margin: 25px 0;
        }
        .two-col .col {
            display: table-cell;
            vertical-align: middle;
            padding: 10px;
        }
        .two-col .col-left {
            text-align: center;
            width: 45%;
        }
        .two-col .col-right {
            width: 55%;
        }
        .two-col .col-right .or-text {
            display: none;
        }
        
        @media only screen and (max-width: 500px) {
            .two-col .col {
                display: block;
                width: 100%;
                text-align: center;
            }
            .two-col .col-right .or-text {
                display: block;
                margin: 15px 0;
                color: #999;
            }
        }
        
        .footer {
            text-align: center;
            padding: 30px;
            color: #999;
            font-size: 13px;
        }
        .footer .signature {
            color: #555;
            font-size: 14px;
            text-align: left;
        }
        .footer .company-info {
            margin-top: 20px;
            line-height: 1.8;
        }
        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1> Donation Request </h1>
            <p>{{ $array['charity_name'] }}</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Dear {{ $array['name'] }},</p>

            <p>You have received a donation request from <strong>{{ $array['charity_name'] }}</strong>. Your support will be truly invaluable and greatly appreciated.</p>

            <!-- Amount Box -->
            <div class="amount-box">
                <div class="label">Donation Amount</div>
                <div class="amount">£{{ number_format($array['amount'], 2) }}</div>
                <div class="charity-name">for {{ $array['charity_name'] }}</div>
            </div>

            <!-- Two Column: QR Code + Link/Button -->
            <div class="two-col">
                <!-- Left: QR Code -->
                <div class="col col-left">
                    <div class="qr-section" style="margin: 0; padding: 15px;">
                        <div class="qr-title">Scan to Donate</div>
                        <div class="qr-image">
                            <img src="{{ $array['qr_code_url'] }}" alt="Scan QR Code to Donate" width="180" height="180">
                        </div>
                        <div class="qr-hint">Use your phone camera to scan</div>
                    </div>
                </div>
                
                <!-- Right: Button & Link -->
                <div class="col col-right">
                    <div class="or-text">— OR —</div>
                    <p style="font-size: 15px; color: #555; margin-bottom: 15px;">Click the button below to make your donation:</p>
                    
                    <div style="text-align: center;">
                        <a href="{{ $array['donation_link'] }}" class="donate-btn" style="margin: 0 0 20px 0; display: inline-block;">
                            Donate Now
                        </a>
                    </div>
                    
                    <p style="font-size: 12px; color: #999; margin-bottom: 8px; text-align: center;">If the button doesn't work, use this link:</p>
                    <div class="link-text">{{ $array['donation_link'] }}</div>
                </div>
            </div>

            @if(!empty($array['charity_note']))
            <!-- Note from Charity -->
            <div class="note-box">
                <strong>Message from {{ $array['charity_name'] }}:</strong><br>
                {{ $array['charity_note'] }}
            </div>
            @endif

            <hr>

            <!-- Signature -->
            <div class="signature">
                Kind Regards,<br>
                <strong>P. Schlesinger</strong><br>
                <strong>Tevini Ltd</strong><br>
                5A Holmdale Terrace<br>
                London, N15 6PP<br>
                📞 02038161694<br>
                ✉️ info@tevini.co.uk<br>
                🌐 www.tevini.co.uk
            </div>
        </div>

        
    </div>
</body>
</html>