


<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <!--[if !mso]--><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <!--<![endif]-->

    <title>Tevini</title>

    <style type="text/css">
        body {
            width: 100%;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }

        p,
        h1,
        h2,
        h3,
        h4 {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        span.preheader {
            display: none;
            font-size: 1px;
        }

        html {
            width: 100%;
        }

        table {
            font-size: 14px;
            border: 0;
        }
        /* ----------- responsivity ----------- */

        @media only screen and (max-width: 640px) {
            /*------ top header ------ */
            .main-header {
                font-size: 20px !important;
            }
            .main-section-header {
                font-size: 28px !important;
            }
            .show {
                display: block !important;
            }
            .hide {
                display: none !important;
            }
            .align-center {
                text-align: center !important;
            }
            .no-bg {
                background: none !important;
            }
            /*----- main image -------*/
            .main-image img {
                width: 440px !important;
                height: auto !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 440px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 440px !important;
            }
            .container580 {
                width: 400px !important;
            }
            .main-button {
                width: 220px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 320px !important;
                height: auto !important;
            }
            .team-img img {
                width: 100% !important;
                height: auto !important;
            }
        }

        @media only screen and (max-width: 479px) {
            /*------ top header ------ */
            .main-header {
                font-size: 18px !important;
            }
            .main-section-header {
                font-size: 26px !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 280px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 280px !important;
            }
            .container590 {
                width: 280px !important;
            }
            .container580 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
        }
    </style>
</head>


<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    
    <!-- header -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">

        <tr>
            <td align="center">
                <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                    <tr>
                        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="center">

                            <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                                <tr>
                                    <td align="center" height="70" style="height:70px;">
                                        <a href="" style="display: block; border-style: none !important; border: 0 !important;"><img width="100" border="0" style="display: block; width: 100px;" src="https://www.tevini.co.uk/assets/front/images/logo.svg" alt="" /></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <!-- end header -->

    <!-- big image section -->

    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">

        <tr>
            <td align="center">
                <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                    

                    <tr>
                        <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                    </tr>


                    

                    <tr>
                        <td align="left">
                            <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590">
                                <tr>
                                    <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                        <!-- section text ======-->


                                        <h3>Dear {{$array['name']}},</h3>

                                        <p>Thank you for your voucher book order. <br>
                                            Your order is now being prepared.</p>
                                        
                                            <p> <b>Order details:</b></p>
                                           <p>Client number : {{$array['client_no']}}</p>
                                           <p>Delivery Option : {{ $array['delivery_option']}}</p>
                                        
                                           <p>Request Date : {{date('m-d-Y')}}</p>
                                        
                                           <div style="display:none">
                                               Voucher books :
                                               <table style="border: 1px solid black; width:400px; " class="table">
                                                   <thead>
                                                       <tr style="border: 1px solid black">
                                                           <th>Voucher</th>
                                                           <th>Qty</th>
                                                           <th>Amount</th>
                                                           <th>Total </th>
                                                       </tr>
                                                   </thead>
                                                   <tbody>
                                                   @foreach (\App\Models\OrderHistory::where('order_id', $array['order_id'])->get() as $order)
                                                       <tr style="border: 1px solid black; text-align:center">
                                                           <td>£{{ $order->voucher->amount }} {{$order->voucher->type}} @if($order->voucher->note)(
                                                               @if ($order->voucher->single_amount > 0) £{{$order->voucher->single_amount}} of @endif
                                                               {{$order->voucher->note}}
                                                               )@endif</td>
                                                           <td>{{$order->number_voucher}}</td>
                                                           @if($order->voucher->type !="Prepaid")
                                                           <td></td>
                                                           <td></td>
                                                           @else
                                                           <td>£{{$order->amount / $order->number_voucher}}</td>
                                                           <td>£{{$order->amount}}</td>
                                                           @endif
                                                       </tr>
                                                   @endforeach
                                                   </tbody>
                                               </table>
                                           </div>

                                           <p style="line-height: 24px">
                                            <br>
                                            Kind Regards, <br>
                                            P. Schlesinger <br>
                                            <br><br>
                                            Tevini Ltd<br>
                                            5A Holmdale Terrace<br>
                                            London<br>
                                            N15 6PP<br>
                                            M. 07490956227<br>
                                            E. Tevinivouchers@gmail.com<br>
                                            W. www.tevini.co.uk<br>
                                            </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>





                </table>

            </td>
        </tr>

        <tr>
            <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
        </tr>

    </table>

    <!-- end section -->


</body>

</html>



