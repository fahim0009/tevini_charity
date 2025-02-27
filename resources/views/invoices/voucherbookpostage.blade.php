<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html">
    <title>Invoice</title>
    <style>
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>

</head>

<body>


    <section class="invoice">
        <div class="container-fluid p-0">
            <div class="invoice-body py-5 position-relative">
                <div style="max-width: 1170px; margin: 20px auto;">


                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td colspan="2" class="" style="border :0px solid #dee2e6;width:50%;">
                                    <div class="col-lg-2" style="flex: 2; text-align: left;">

                                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('assets/front/images/logo.png'))) }}" width="120px" style="display:inline-block;" />
                                        
                                        {{-- <img src="https://www.tevini.co.uk/assets/front/images/logo.svg" width="120px" style="display:inline-block;" /> --}}
                                        
                                        {{-- <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/front/images/logo.png'))) }}" width="120px" style="display:inline-block;" /> --}}


                                        
                                    </div>
                                </td>
                                <td colspan="1" class="" style="border :0px solid #dee2e6 ;width:30%;"></td>
                                <td colspan="3" class="" style="border :0px solid #dee2e6 ;">
                                    <div class="col-lg-6" style="flex: 2; text-align: right;">
                                        <h1 style="font-size: 30px; color:blue">DELIVERY NOTE</h1>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="" style="border :0px solid #dee2e6;width:25%;">
                                </td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;width:50%;"></td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                </td>
                            </tr>
                        </tbody>

                    </table>

                    <br><br>

                    <table style="width: 100%;font-family: Arial, Helvetica;font-size: 12px;">
                        <tbody>

                            <tr>
                                <td colspan="2" class="" style="border :0px solid #828283 ;width:40%;">
                                    <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                        @if($user->name)
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $user->name ?? "" }}</p>
                                        @endif
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $user->email }}</p>
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $user->phone }}</p>
                                        @if($user->houseno)
                                        <p style="font-size: 12px; margin: 5px; text-align: left; line-height: 10px;">
                                            {{ $user->houseno }} {{ $user->street }} {{ $user->address_third_line }} {{ $user->town }} {{ $user->postcode }} 
                                        </p>
                                        @endif
                                    </div>
                                </td>

                                <td colspan="2" class="" style="border :0px solid #dee2e6;width:30%;"></td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                    <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                        <p style="font-size: 12px; margin : 5px;text-align: right;line-height: 10px;">Order: {{ $order->order_id }}</p>
                                        <p style="font-size: 12px; margin : 5px;text-align: right;line-height: 10px;">Date: {{ \Carbon\Carbon::parse($order->purchase_date)->format('d/m/Y') }}</p>
                                    </div>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                    <br>

                    

                    <br><br>

                    <div class="row overflow" style="position:fixed; bottom:0; width:100%;font-family: Arial, Helvetica;font-size: 12px; ">
                        <hr>
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="width: 50%;"></th>
                                    <th style="width: 50%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 50%; text-align:left;" colspan="1"><b>TEVINI</b></td>
                                    <td style="width: 50%; text-align:right;" colspan="1"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="footer-bottom mt-3 mb-0">
                                            <span class="mx-4">  5A Holmdale Terrace,    London, N15 6PP </span> | <span class="mx-4">PHONE: +44 7490956227</span>
                                            | <span class="mx-4">EMAIL: tevinivouchers@gmail.com</span>
                                        </p>
                                    </td>
                                    <td style="width: 50%; text-align:right;">
                                        

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>