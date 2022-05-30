@extends('frontend.layouts.user')

@section('content')

<div id="main-container" class="" style="display: block; opacity: 1;" url="https://clients.achisomoch.org/settings.php"><main class="main-reports content-desktop">
    <div class="header-fixed visible-xs">

            <header class="header">

                <div class="container ">

                    <div class="row">

                        <div class="header-mobile-transactions">

                            <div class="col-xs-2">

                                <a href="dashboard.php" class="go-back">

                                    <i class="fa fa-angle-left" aria-hidden="true"></i>

                                </a>

                            </div><!-- /col -->

                            <div class="col-xs-8">

                                <h2 class="title">Reports</h2>

                            </div><!-- /col -->

                            <div class="col-xs-2">

                                <a href="#" class="nav-mobile nav-icon4 visible-xs ">

                                    <span></span>
                                    <span></span>
                                    <span></span>

                                </a>

                            </div><!-- /col -->

                        </div><!-- /header-mobile-transactions -->
                                                <!-- AACDESIGN3 -->
                        <div class="col-xs-12 header-mobile-transactions visible-xs">
                            <ul class="nav-vouchers transaction_page_mobile">
                                <li class="nav-vouchers-li">
									<a href="reports.php" class="nav-vouchers-lkn active">CUSTOM</a>
                                </li>

                                <li class="nav-vouchers-li">
			                        <a href="#" class="nav-vouchers-lkn">OFFICE</a>
                                </li>
                            </ul>
                        </div>

                        <div class="clear"></div>

                    </div><!-- /row  -->

                </div><!-- /container  -->

            </header>



    </div><!-- /header-fixed -->
    <div class="container-reports-options">
        <div class="container-fluid">


            			<div class="row hidden-xs">
	            <div class="col-md-12">

	                <div class="voucher-books-header">
	                    <h2 class="title-section-desktop">Reports</h2>
	                    <div class="navigator-voucher-books">
	                        <a href="#" class="selected">CUSTOM</a>
	                        <a href="#">OFFICE</a>
	                    </div>
	                </div>


	            </div>
	        </div>

            <div class="row">
                <div class="col-md-12">
                    <ul class="report-options">
                        						                                                <li>
                            <div class="icon-report pull-left">
                                <a href="report-payments.php" class="ajaxlink"><img src="images/donations-report-icon.png" alt=""></a>
                            </div>
                            <div class="report-container">
                                <h1 class="report-title"><a href="report-payments.php" class="report-title ajaxlink">Donations Report</a></h1>
                                <p>It will show all monies received into your account by AAC, whether payments were due Gift aid or not. You can download this report as a PDF.<a href="http://achisomoch.org/donations-report/" target="_blank" title="">Learn more</a></p>
                            </div>
                        </li>
                        <li>
                            <div class="icon-report pull-left">
                                <a href="report-account-history.php" class="ajaxlink"><img src="images/account-transactions-report-icon.png" alt=""></a>
                            </div>
                            <div class="report-container">
                                <h1 class="report-title"><a href="report-account-history.php" class="report-title ajaxlink">Account Transactions - Compact Report</a></h1>
                                <p>View your account’s transactions in a single compact report.</p>
                            </div>
                        </li>
                        <li class="hide-mobile">
                            <div class="icon-report pull-left">
                                <a href="report-statement.php" class="ajaxlink"><img src="images/account-transactions-report-icon.png" alt=""></a>
                            </div>
                            <div class="report-container">
                                <h1 class="report-title"><a href="report-statement.php" class="report-title ajaxlink">Statement Report</a></h1>
                                <p>You can view your statement online. You can download this report as a PDF.<a href="https://achisomoch.org/statement-report/" target="_blank" title="">Learn more</a></p>
                            </div>
                        </li>
                                                <li>
                            <div class="icon-report pull-left">
                                <a href="report-voucher-books.php" class="ajaxlink"><img class="voucher-book-icon" src="images/voucher-book-report-icon.png" alt=""></a>
                            </div>
                            <div class="report-container">
                                <h1 class="report-title"><a href="report-voucher-books.php" class="report-title ajaxlink">Voucher Books Report</a></h1>
                                <p>This reports lists all of your voucher books, and how they have been used.<a href="http://achisomoch.org/ordered-voucher-book-report/" target="_blank" title="">Learn more</a></p>
                            </div>
                        </li>
                        <li>
                            <div class="icon-report pull-left">
                                <a href="report-voucher-book.php" class="ajaxlink"><img src="images/vouchers-book-report-icon.png" alt=""></a>
                            </div>
                            <div class="report-container">
                                <h1 class="report-title"><a href="report-voucher-book.php" class="report-title ajaxlink">Vouchers in a Book Report</a></h1>
                                <p>View the status of each voucher in a voucher book.</p>
                            </div>
                        </li>
                        <!--
                        <li >
                            <div class="icon-report pull-left">
                                <a href="report-other.php" class="ajaxlink"><img src="images/account-transactions-report-icon.png" alt=""></a>
                            </div>
                            <div class="report-container">
                                <h1 class="report-title"><a href="report-other.php" class="report-title ajaxlink">Office or Custom Reports</a></h1>
                                <p>You can view your statements online.<a href="https://achisomoch.org/statement-report/" target="_blank" title="">Learn more</a></p>
                            </div>
                        </li>
-->

                    </ul>
                </div><!-- / col -->
            </div><!-- / row -->

        </div><!-- /container -->
    </div><!-- /container-reports-options -->
</main>

<script>
if(typeof gtag==='function') {
    gtag('set', 'page_path', '/reports.php');
    gtag('set', 'page_title', 'reports');
    gtag('event', 'page_view');
}
</script>
</div>


<div id="main-container-back" class="" url="https://clients.achisomoch.org/vouchers.php" style="opacity: 1; display: none;">
    <script type="text/javascript">
        /**
        jQuery(document).ready(function () {

            jQuery('.lkn-order-vouchers, .right-bt').on('click', function () {

                if (jQuery('.bootstrap-switch').hasClass('bootstrap-switch-on') == true) {
                    //alert('Now you are ready to submit.');
                    jQuery('#editor').submit();
                    document.getElementById('editor').submit();
                } else {
                    jQuery("#modal-general-caption p").html('Please select a delivery method');
                    jQuery("#modal-general-caption").modal('show');
                }

            });
        });
        **/

        jQuery('#delivery_type_post').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state == true) {
                jQuery("#delivery_type_office").bootstrapSwitch('state', false);
                jQuery("#delivery_type_special").bootstrapSwitch('state', false);
                jQuery("#VoucherBookDelivery").val('');
                jQuery("#VoucherBookDelivery").val(jQuery(this).data('value'));
            }
        });
        jQuery('#delivery_type_office').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state == true) {
                jQuery("#delivery_type_post").bootstrapSwitch('state', false);
                jQuery("#delivery_type_special").bootstrapSwitch('state', false);
                jQuery("#VoucherBookDelivery").val('');
                jQuery("#VoucherBookDelivery").val(jQuery(this).data('value'));
            }
        });
        jQuery('#delivery_type_special').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state == true) {
                jQuery("#delivery_type_post").bootstrapSwitch('state', false);
                jQuery("#delivery_type_office").bootstrapSwitch('state', false);
                jQuery("#VoucherBookDelivery").val('');
                jQuery("#VoucherBookDelivery").val(jQuery(this).data('value'));
            }
        });

        function updateVoucherBooks() {

            var total = 0;

            jQuery('.VoucherBooks').each(function () {
    //console.log($(this).val());

                var intRegex = /^\d+$/;

                if (intRegex.test($(this).val())) {
                    total += parseInt($(this).val());
                }
            });
            if (total > 2) {
                jQuery('#post').addClass('disable');
                jQuery('.row-input').removeClass('active');
            } else {
                jQuery('#post').removeClass('disable');
            }
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.right-bt').click(function(e){
                $('#voucherbookWarningAccepted').val(1);
                $('.lkn-order-vouchers').click();
            });
            $('.lkn-order-vouchers').click(function(e){
                e.preventDefault();

                $('.av').val($('.page_av').val());


                var formData = $('#voucher-editor').serialize();

                $('#voucher-editor input').removeClass('has-error-box');

                $(this).addClass('disabled');


                $.ajax({
                    url: 'remote.php?m=order-voucher-books',
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    success: function(data)
                    {
                        if(data.errorType=='login'){
                            performUnloadCheck=false;
                            window.onbeforeunload=null;
                            window.location.href='login.php?logout=1&m='+encodeURIComponent(data.errorMessage);
                            return;
                        }

                        if(data.error && data.errorType=='confirm') {
                            jQuery("#modal-charity-voucher-warning p.line1").html(data.errorMessage.replace("\n\n",'<br><br>'));
                            jQuery("#modal-charity-voucher-warning").modal('show');

                            $('.lkn-order-vouchers').removeClass('disabled');
                        } else if(data.error && data.errorType=='alert') {
                            jQuery("#modal-charity-voucher-error p.line1").html(data.errorMessage.replace("\n\n",'<br><br>'));
                            jQuery("#modal-charity-voucher-error").modal('show');

                            $('.lkn-order-vouchers').removeClass('disabled');

                        } else if(!data.error) {

                            jQuery("#modal-success p.line1").html('Your voucher book order has been placed.');
                            jQuery("#modal-success p.line2").html('');
                            jQuery("#modal-success").modal('show');
                            $('#modal-success').on('hidden.bs.modal', function (e) {
                                  loadpage('vouchers.php');
                            })

                            /**
                            jQuery("#modal-general-caption p").html(
                                '<h2>Thank You.</h2>'+
                                '<p>Your voucher book order has been placed.</p>'
                            );
                            jQuery("#modal-general-caption").modal('show');
                            **/

                            loadpage('vouchers.php?done=true');
                            // AACDESIGN3
                            //loadpage('dashboard.php');

                            //$('body').addClass('has-notification');
                            //$('.notification-box font').html('Your Voucher Book order <strong> has been placed.</strong>');
                            //$('.notification-box').show();

                            //$('.password-box').val('');

                        } else {

                            $('.lkn-order-vouchers').removeClass('disabled');

                            jQuery("#modal-general-caption p").html(data.errorMessage);
                            $('#box-'+data.errorField).addClass('has-error-box');
                            $('#box-'+data.errorField).focus();

                            var pos = null;
                            if(data.errorField) pos = $('#box-'+data.errorField).offset();
                            if(pos) {
                                var top = pos.top - 220;
                                var left = pos.left - 20;
                                window.scrollTo((left < 0 ? 0 : left), (top < 0 ? 0 : top));
                            }
                            jQuery("#modal-general-caption").modal('show');
                        }

                    }
                });

            });

        });
    </script>
        <main class="order-voucher content-desktop">

            <div class="header-fixed visible-xs">

                <header class="header">

                    <div class="container ">

                        <div class="row">

                            <div class="header-mobile-transactions">

                                <div class="col-xs-2">

                                    <a href="dashboard.php" class="go-back">

                                        <i class="fa fa-angle-left" aria-hidden="true"></i>

                                    </a>

                                </div><!-- /col -->

                                <div class="col-xs-8">

                                    <h2 class="title">Order Voucher Books</h2>

                                </div><!-- /col -->

                                <div class="col-xs-2">

                                    <a href="#" class="nav-mobile nav-icon4 visible-xs">

                                        <span></span>
                                        <span></span>
                                        <span></span>

                                    </a>

                                </div><!-- /col -->

                            </div><!-- /header-mobile-transactions -->
                            <!-- AACDESIGN3 -->
                            <div class="col-xs-12 header-mobile-transactions visible-xs">
                                <ul class="nav-vouchers transaction_page_mobile">
                                    <li class="nav-vouchers-li">
                                        <a href="vouchers.php" class="nav-vouchers-lkn active">ORDER VOUCHER BOOKS</a>
                                    </li>

                                    <li class="nav-vouchers-li">
                                        <a href="vouchers-previous.php" class="nav-vouchers-lkn">PREVIOUS ORDERS</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="clear"></div>

                        </div><!-- /row  -->

                    </div><!-- /container  -->

                </header>

            </div><!-- /header-fixed -->

            <div class="container top-center-content hidden-xs">

                <div class="row">

                    <div class="col-xs-12">

                        <div class="box-account-header visible-xs">

                            <div class="box-account">

                                <h2 class="title">ACCOUNT</h2>

                                <h3 class="account-number">A7895</h3>

                            </div><!-- /box-account -->

                            <div class="box-balance">

                                <h2 class="title">BALANCE</h2>

                                <h3 class="balance-number">ֲ ?£ 3,344.99</h3>

                            </div><!-- /box-balance -->

                        </div><!-- /box-account-header -->

                        <h3 class="time-update visible-xs">AS OF <strong>1 SEP 2016, 2:15PM</strong></h3>

                    </div><!-- / col 12 -->

                </div><!-- / row -->

            </div><!-- / top center content -->
            <div class="box-slide-text">
                <i class="fa fa-angle-down visible-xs" aria-hidden="true"></i>
                <div class="container-fluid new-tab-links">
                                            <!--<a href="javascript:void(0);" class="lkn-daily">-->
                                <p class="text lkn-daily"><span style="color:red">Please be aware that the post is slow at the moment and if you want your voucher books within a week, we recommend that you collect your order from our office, or select the special delivery option.</span></p>
                                <!--<i class="fa fa-angle-down" aria-hidden="true"></i>
                                <i class="fa fa-angle-up" aria-hidden="true"></i>-->
                            <!--</a>-->
                </div><!-- container -->

            </div><!-- /box-daily-updates -->

            <div class="row hidden-xs">
                <div class="col-md-12">

                    <div class="voucher-books-header">
                        <h2 class="title-section-desktop">Order Voucher Books</h2>
                        <div class="navigator-voucher-books">
                            <a href="vouchers.php" class="selected">ORDER VOUCHER BOOKS</a>
                            <a href="vouchers-previous.php">PREVIOUS ORDERS</a>
                        </div>
                    </div>


                </div>
            </div>

            <form name="voucher-editor" id="voucher-editor" method="post" action="/vouchers.php">



                <div class="container-vochers">

                    <div class="container-fluid">

                        <div id="default-number" class="row">
                            <div>
                                <div class="col-md-6 col-xs-12">

                                                                  <div class="form-group input-default">

                                        <div class="icon-input icon-small"></div>


                                            <div class="group-title">
                                                <div class="icon icon-input icon-small"></div>
                                                <p>Prepaid Voucher Books</p>
                                            </div>

                                                                                    <div class="info-input">
                                                <div class="title-label">PREPAID 50P VOUCHER</div>
                                                <div class="subtitle-label">100 VOUCHERS <span></span></div>
                                            </div><!-- /info-input -->

                                            <div class="input-group ">
                                                <a href="#" class="less-input lkn-input"></a>
                                                <input type="number" name="fields[VoucherBooks][50p]" data-value="50" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber1" value="0">
                                                <a href="#" class="more-input lkn-input"></a>
                                            </div><!-- /input-group -->

                                            <div class="voucher-prices">
                                                <span class="total">£123</span>
                                                <span class="discount">£123</span>
                                            </div>


                                    </div><!-- /form-group -->


                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-small"></div>

                                        <div class="info-input">
                                            <div class="title-label">PREPAID £1 VOUCHER </div>
                                            <div class="subtitle-label">50 VOUCHERS </div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£1]" data-value="50" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber2" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->


                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-small"></div>

                                        <div class="info-input">
                                            <div class="title-label">PREPAID £2 VOUCHER </div>
                                            <div class="subtitle-label">25 VOUCHERS </div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£2]" data-value="50" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber3" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-small"></div>

                                        <div class="info-input">
                                            <div class="title-label">PREPAID £3 VOUCHER </div>
                                            <div class="subtitle-label">50 VOUCHERS </div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£3PPV]" data-value="150" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber4" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-small"></div>

                                        <div class="info-input">
                                            <div class="title-label">PREPAID £5 VOUCHER </div>
                                            <div class="subtitle-label">30 VOUCHERS </div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£5PPV]" data-value="150" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber5" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->
                                        <!--end 5-->

                                    <!--end prepaid-->


                                    <div class="group-title">
                                        <div class="icon icon-voucher"></div>
                                        <p>Pre-printed Voucher Books</p>
                                    </div>

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">BLANK</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][Blank]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber6" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £3</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£3]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber7" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->


                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £5</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£5]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber8" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                </div><!-- /col -->



                                <div class="col-md-6 col-xs-12">

                                                                   <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £10</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£10]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber9" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £18</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£18]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber10" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->


                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £20</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£20]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber11" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->


                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £25</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£25]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber12" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                   <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £36</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£36]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber13" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->


                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £50</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£50]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber14" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £100</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£100]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber15" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->

                                                                    <div class="form-group input-default">

                                        <div class="icon-input icon-large"></div>

                                        <div class="info-input">
                                            <div class="title-label">PRE-PRINTED £180</div>
                                            <div class="subtitle-label">VOUCHER BOOK <span></span></div>
                                        </div><!-- /info-input -->

                                        <div class="input-group ">
                                            <a href="#" class="less-input lkn-input"></a>
                                            <input type="number" name="fields[VoucherBooks][£180]" data-value="0" data-discount="0" class="input-number VoucherBooks zero-default defaultNumber16" value="0">
                                            <a href="#" class="more-input lkn-input"></a>
                                        </div><!-- /input-group -->

                                        <div class="voucher-prices">
                                            <span class="total">£123</span>
                                            <span class="discount">£123</span>
                                        </div>

                                    </div><!-- /form-group -->



                                </div><!-- /col-->


                            </div>
                        </div><!-- /row -->
                    </div><!-- /container- -->
                    <div class="container-fluid container-border-desktop hidden-xs">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="border-bottom "></div>
                            </div><!-- /col -->
                        </div><!-- /row -->
                    </div><!-- /container -->
                </div><!-- /container-vochers -->
                <div class="container-delivery checkbox-box">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-delivery">
                                    <div class="form-group">
                                        <input type="hidden" id="VoucherBookDelivery" name="fields[VoucherBookDelivery]" value="">
                                        <h2 class="title-delivery">DELIVERY </h2>
                                        <div class="container-urgent" id="VoucherBookDeliveryPostBox">
                                            <a href="javascript:void(0)" id="VoucherBookDeliveryPost" data-id="VoucherBookDelivery" data-value="Post" class="ckeckbox radio-button ">
                                                <span class="circle"></span>
                                            </a>
                                            <div class="label-delivery">
                                                <div class="label-urgent">POST <!--:UP TO THREE BOOKS-->
                                                    <div class="subtitle-label" style="text-transform:uppercase">I take responsibility for any pre-paid books lost in the post.</div>
                                                </div>
                                            </div>
                                        </div>
    <!--start collection-->
    <!--
                                        <div class="container-urgent">
                                            <a href="javascript:void(0)" data-id="VoucherBookDelivery" data-value="Pick up from office" class="ckeckbox radio-button ">
                                                <span class="circle"></span>
                                            </a>
                                            <div class="label-delivery">
                                                <div class="label-urgent">COLLECTION FROM THE LONDON JEWISH FAMILY CENTRE
                                                    <div class="subtitle-label">SUN 12:30PM - 4:30PM & MON - THURS 9:30AM -4:30PM</div>
                                                    <div class="subtitle-label">ADDRESS: 113B GOLDERS GREEN RD, LONDON, NW11 8HR</div>
                                                </div>
                                            </div>
                                        </div>
    -->
                                        <div class="container-urgent">
                                            <a href="javascript:void(0)" data-id="VoucherBookDelivery" data-value="Pick up from office" class="ckeckbox radio-button ">
                                                <span class="circle"></span>
                                            </a>
                                            <div class="label-delivery">
                                                <div class="label-urgent">COLLECTION
                                                    <div class="subtitle-label">
                                                        Voucherbooks when confirmed can be collected from our office Mon - Thu: 10:00am - 5:00pm Fri: 10:00am - 12:00pm.<br><br>Please note our office will be closed from Friday 24th December until Tuesday 4th January.                                                    <!--Due to the government restrictions and guidelines as a result of the lockdown, from 01 November 2020, we are no longer able to offer voucher book collection as an option. Voucher book orders can be either sent by post or special delivery-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                       <!--end collection-->
                                        <div class="container-urgent">
                                            <a href="javascript:void(0)" data-id="VoucherBookDelivery" data-value="Special Delivery" class="ckeckbox radio-button ">
                                                <span class="circle"></span>
                                            </a>
                                            <div class="label-delivery">
                                                <div class="label-urgent">SPECIAL DELIVERY
                                                    <div class="subtitle-label">AT A COST OF ֲ£5 TO BE DEDUCTED FROM MY ACCOUNT</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /form-group -->
                                </div>
                            </div><!-- /col -->
                            <div class="col-md-6">
                                <input type="hidden" id="VoucherBookUrgent" name="fields[VoucherBookUrgent]" value="">
                                <div class="container-urgent urgent-text">
                                    <a href="javascript:void(0)" data-id="VoucherBookUrgent" data-value="Yes" class="ckeckbox checkbox-button ">
                                        <span class="circle"></span>
                                    </a>
                                    <div class="label-delivery">
                                        <div class="label-urgent">
                                            THIS IS URGENT
                                        </div>
                                    </div>
                                </div><!-- /container-urgent -->
                                <!-- AACDESIGN4 -->
                                <div class="container-notes">
                                    <label class="title-notes">NOTES TO OFFICE</label>
                                    <textarea id="OfficeComments" name="fields[OfficeComments]" cols="30" rows="10" class="textarea-notes" placeholder="Add any notes you'd wish to pass on to the office."></textarea>
                                </div><!-- /container-urgent -->
                            </div><!-- /col -->
                        </div><!-- /row -->
                    </div><!-- /container -->
                    <div class="container-fluid container-border-desktop hidden-xs">
                        <div class="col-md-12">
                            <div class="border-bottom "></div>
                        </div><!-- /col -->
                    </div><!-- /container -->

                </div><!-- /container-delivery -->

            <!-- AACDESIGN4 -->
            <div class="col-md-12">
                <!-- AACDESIGN3 -->
                <a href="#" class="lkn-order-vouchers transition disabled">Order Vouchers</a>
                <!-- AACDESIGN4 -->
            </div><!-- /col -->

            <!-- <a href="#" class="lkn-order-vouchers visible-xs disabled">Order Vouchers</a> -->

                <input type="hidden" name="submit1" value="save">
                <input type="hidden" name="voucherbookWarningAccepted" id="voucherbookWarningAccepted" value="0">
                <input type="hidden" name="av" value="1b0a041fb51ad6a78aaa8296c68d75ec38f5ff80">
                <input type="hidden" name="user-check" class="user-check" value="5452">


                <!--<input type="hidden" name="fields[VoucherBookDelivery]" id="VoucherBookDelivery" value="">-->
            </form>

        </main>

        <div class="modal-gral modal fade" id="modal-charity-voucher-warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-body">

                    <div class="row-confirmation-content">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>

                        <p class="line1">You have requested a new  £10 voucher book. Our records indicate that you already have 5 of these books, some of which are hardly used.</p>

                    </div><!-- /row-confirmation-content -->

                    <div class="btns-options">
                        <a href="report-voucher-books.php" id="cancel" class="left-bt ajaxlink" data-dismiss="modal">View details</a>
                        <a href="javascript:void(0);" id="submit" class="right-bt">Order anyway</a>
                    </div><!-- / btns options -->

                </div><!-- /modal-body -->

            </div><!-- /modal-content -->

        </div><!-- /modal-dialog -->

    </div><!-- /modal -->    <div class="modal-gral modal fade" id="modal-charity-voucher-error" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-body">

                    <div class="row-confirmation-content">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>

                        <p class="line1">You have requested a new  £10 voucher book. Our records indicate that you already have 5 of these books, some of which are hardly used.</p>

                    </div><!-- /row-confirmation-content -->

    <!--			<a href="" id="lnk_vch_donate_again" class="lkn-bottom-modal external-lkn" data-dismiss="modal" >OK</a>-->
                <a href="javascript:void(0);" id="cancel" class="lkn-bottom-modal" data-dismiss="modal">OK</a>


                </div><!-- /modal-body -->

            </div><!-- /modal-content -->

        </div><!-- /modal-dialog -->

    </div><!-- /modal -->

    <script>
        $('input[type="number"]').keypress(function(e) {
            var a = [];
            var k = e.which;

            for (i = 48; i < 58; i++)
                a.push(i);

            if (!(a.indexOf(k)>=0))
                e.preventDefault();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.zero-default').keyup(function(){
                if($(this).val() > 0){
                    $(this).val().replace(0,'');
                }

                if($(this).val()!=0){
                    var text = $(this).val();
                    if(text.slice(0,1)==0)
                    {
                        $(this).val(text.slice(1,text.length));
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on("click", function() {
                if ($('.defaultNumber1').val() === ""){
                    $(".defaultNumber1").val("0");
                }
                else if ($('.defaultNumber2').val() === ""){
                    $(".defaultNumber2").val("0");
                }
                else if ($('.defaultNumber3').val() === ""){
                    $(".defaultNumber3").val("0");
                }
                else if ($('input.defaultNumber4').val() === ""){
                    $("input.defaultNumber4").val("0");
                }
                else if ($('input.defaultNumber5').val() === ""){
                    $("input.defaultNumber5").val("0");
                }
                else if ($('input.defaultNumber6').val() === ""){
                    $("input.defaultNumber6").val("0");
                }
                else if ($('input.defaultNumber7').val() === ""){
                    $("input.defaultNumber7").val("0");
                }
                else if ($('input.defaultNumber8').val() === ""){
                    $("input.defaultNumber8").val("0");
                }
                else if ($('input.defaultNumber9').val() === ""){
                    $("input.defaultNumber9").val("0");
                }
                else if ($('input.defaultNumber10').val() === ""){
                    $("input.defaultNumber10").val("0");
                }
                else if ($('input.defaultNumber11').val() === ""){
                    $("input.defaultNumber11").val("0");
                }
                else if ($('input.defaultNumber12').val() === ""){
                    $("input.defaultNumber12").val("0");
                }
                else if ($('input.defaultNumber13').val() === ""){
                    $("input.defaultNumber13").val("0");
                }
                else if ($('input.defaultNumber14').val() === ""){
                    $("input.defaultNumber14").val("0");
                }
                else if ($('input.defaultNumber15').val() === ""){
                    $("input.defaultNumber15").val("0");
                }
                else if ($('input.defaultNumber16').val() === ""){
                    $("input.defaultNumber16").val("0");
                }

            });
        });
    </script>

    <script>
    if(typeof gtag==='function') {
        gtag('set', 'page_path', '/vouchers.php');
        gtag('set', 'page_title', 'vouchers');
        gtag('event', 'page_view');
    }
    </script>
    </div>


@endsection
