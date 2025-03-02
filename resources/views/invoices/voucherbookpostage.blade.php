

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tevini</title>
    <script>
        // window.onload = function() {
        //     window.print();
        // }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            background-color: #f8f8f8;
            padding-top: 20px;
        }
        .label {
            display: flex;
            flex-direction: column;
            border: 2px solid black;
            border-radius: 10px;
            width: 4in;
            height: 3in;
            padding: 20px;
            background: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .amazon-logo {
            font-size: 24px;
            font-weight: bold;
        }
        .amazon-logo span {
            color: orange;
        }
        .ship-to {
            background: black;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            border-radius: 5px;
        }
        .section {
            margin-bottom: 15px;
            margin-top: 25px;
        }

        p {
            margin-top: 25px;
        }


        .bold {
            font-weight: bold;
            font-size: 16px;
        }

        @media print {
            body {
                display: flex;
                justify-content: center;
                align-items: flex-start;
                height: 100vh;
                padding-top: 20px;
            }
            .label {
                margin: auto;
                page-break-before: always;
            }
        }

    </style>
</head>
<body>
    <div class="label">
        <div class="header">
            <div class="amazon-logo">
                <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('assets/front/images/logo.png'))) }}" width="120px" style="display:inline-block;" />
            </div>
        </div>
        {{-- <div class="section">
            <p class="bold">From:</p>

            <span>5A Holmdale Terrace, London, N15 6PP </span> <br>
            <span>+44 7490956227</span> <br>
            <span>tevinivouchers@gmail.com</span>

        </div> --}}
        <div class="section">
            <p class="bold">Delivery To:</p>
            <p>
                @if ($user->profile_type == 'Personal')
                {{ $user->name ?? ""   }} <br>
                @endif
                @if ($user->profile_type == 'Company')
                {{ $user->surname ?? ""   }} <br>
                @endif
                @if ($user->profile_type == 'Company' || $user->profile_type == null)    {{ $user->surname ?? ""   }} <br>
                @endif
                {{ $user->houseno ?? ""   }} @if ($user->street) <br> @endif 
                {{ $user->street ?? ""   }} @if ($user->address_third_line) <br> @endif 
                {{ $user->address_third_line ?? ""   }} <br>
                {{ $user->town ?? ""   }} <br> 
                {{ $user->postcode ?? ""   }}  <br>
                United Kingdom
            </p>
            

            
        </div>
    </div>
</body>
</html>

