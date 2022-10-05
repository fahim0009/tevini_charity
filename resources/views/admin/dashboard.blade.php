
@extends('layouts.admin')

@section('content')


<div class="dashboard-content py-2 px-4">
    <div class="rows bg-white shadow-sm my-3">
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".25s" class="wow fadeIn box text-center theme-1 p-3 ">
                    <span class="iconify bg-violet" data-icon="mdi:white-balance-incandescent"></span>
                    <div class="inner theme-txt-violet">
                        <h1 class="my-0 ">£{{$donation}}</h1>
                        <h5 class="my-2 ">Total Donation In</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".30s" class="wow fadeIn box text-center theme-2 p-3 ">
                    <span class="iconify bg-pink" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-pink">
                        <h1 class="my-0 ">£{{$transaction}}</h1>
                        <h5 class="my-2 ">Total Charity Out</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".35s" class="wow fadeIn box text-center theme-yellow p-3 ">
                    <span class="iconify bg-yellow" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-yellow">
                        <h1 class="my-0 ">£{{$voucherout}}</h1>
                        <h5 class="my-2 ">Total Voucher In</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rows bg-white shadow-sm my-3">
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".25s" class="wow fadeIn box text-center theme-1 p-3 ">
                    <span class="iconify bg-violet" data-icon="mdi:white-balance-incandescent"></span>
                    <div class="inner theme-txt-violet">
                        <h1 class="my-0 ">£{{ $commission }}</h1>
                        <h5 class="my-2 ">Total Commission</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".30s" class="wow fadeIn box text-center theme-2 p-3 ">
                    <span class="iconify bg-pink" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-pink">
                        <h1 class="my-0 ">{{$processvoucher}}</h1>
                        <h5 class="my-2 ">Total Voucher process</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rows bg-white shadow-sm my-3">
        <div class="cols" id="contentContainer">
            <div class="card">
                <h3 class="text-center">Notification</h3>
                @foreach (\App\Models\User::where('notification','=', 1)->get() as $user)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>New donor!</strong> To view this Donor.<a href="{{ route('donor') }}"> Click here</a>
                        <a id="donorBtn" donor_id="{{$user->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                    </div>
                @endforeach

                @foreach (\App\Models\Order::where('notification','=', 1)->get() as $order)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>New order!</strong> To process this order.<a href="{{ route('singleorder',$order->id) }}"> Click here</a>
                    <a id="orderBtn" order_id="{{$order->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                  </div>
                @endforeach

                @foreach (\App\Models\Donation::where('notification','=', 1)->get() as $donation)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>New donation!</strong> To process this donation.<a href="{{ route('donationlist') }}"> Click here</a>
                    <a id="donationBtn" donation_id="{{$donation->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                  </div>
                @endforeach

                @foreach (\App\Models\StripeTopup::where('notification','=', 1)->get() as $topup)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>New Stripe Topup!</strong> To view this.<a href="{{ route('stripetopup') }}"> Click here</a>
                    <a id="topupBtn" topup_id="{{$topup->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                  </div>
                @endforeach

            </div>
        </div>
    </div>


</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {

    //header for csrf-token is must in laravel
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //


                //  donor notification
                var url = "{{URL::to('/admin/donornoti')}}";
                $("#contentContainer").on('click','#donorBtn', function(){

                    var donorid= $(this).attr('donor_id');

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {donorid},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });


                // order notification
                var url2 = "{{URL::to('/admin/ordernoti')}}";
                $("#contentContainer").on('click','#orderBtn', function(){

                    var orderid= $(this).attr('order_id');

                    $.ajax({
                        url: url2,
                        method: "POST",
                        data: {orderid},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });


                // donation notification
                var url3 = "{{URL::to('/admin/donationnoti')}}";
                $("#contentContainer").on('click','#donationBtn', function(){

                    var donationid= $(this).attr('donation_id');

                    $.ajax({
                        url: url3,
                        method: "POST",
                        data: {donationid},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });


            // topup notification
            var url4 = "{{URL::to('/admin/topupnoti')}}";
                $("#contentContainer").on('click','#topupBtn', function(){

                    var topupid= $(this).attr('topup_id');

                    $.ajax({
                        url: url4,
                        method: "POST",
                        data: {topupid},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });




    });
</script>

@endsection
