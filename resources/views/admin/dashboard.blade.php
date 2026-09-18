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
                <!-- Added Flexbox to align title and Clear All button -->
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h3 class="text-center mb-0">Notifications</h3>
                    <button type="button" class="btn btn-danger btn-sm" id="clearAllNotiBtn">
                        Clear All Notifications
                    </button>
                </div>

                <div class="p-3">
                    @foreach (\App\Models\User::where('notification','=', 1)->get() as $user)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>New donor!</strong> To view this Donor.<a href="{{ route('donor') }}"> Click here</a>
                            <a class="donorBtn" donor_id="{{$user->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                        </div>
                    @endforeach

                    @foreach (\App\Models\Order::where('notification','=', 1)->get() as $order)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>New order!</strong> To process this order.<a href="{{ route('singleorder',$order->id) }}"> Click here</a>
                            <a class="orderBtn" order_id="{{$order->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                        </div>
                    @endforeach

                    @foreach (\App\Models\Donation::where('notification','=', 1)->get() as $donation)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>New donation!</strong> To process this donation.<a href="{{ route('donationlist') }}"> Click here</a>
                            <a class="donationBtn" donation_id="{{$donation->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                        </div>
                    @endforeach

                    @foreach (\App\Models\StripeTopup::where('notification','=', 1)->get() as $topup)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>New Stripe Topup!</strong> To view this.<a href="{{ route('stripetopup') }}"> Click here</a>
                            <a class="topupBtn" topup_id="{{$topup->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        // Setup CSRF token globally for all AJAX requests
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Reusable function to handle single notification dismissal
        function dismissNotification(url, data, $element) {
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function (response) {
                    if (response.status == 300) {
                        // Hide the specific alert smoothly without reloading the page
                        $element.closest('.alert').alert('close');
                        $(".ermsg").html(response.message);
                    } else {
                        console.error('Error processing request');
                    }
                },
                error: function (err) {
                    console.error('AJAX Error:', err);
                }
            });
        }

        // Handle Individual Donor Notification
        $("#contentContainer").on('click', '.donorBtn', function(e){
            e.preventDefault();
            dismissNotification("{{ route('donornoti') }}", { donorid: $(this).attr('donor_id') }, $(this));
        });

        // Handle Individual Order Notification
        $("#contentContainer").on('click', '.orderBtn', function(e){
            e.preventDefault();
            dismissNotification("{{ route('ordernoti') }}", { orderid: $(this).attr('order_id') }, $(this));
        });

        // Handle Individual Donation Notification
        $("#contentContainer").on('click', '.donationBtn', function(e){
            e.preventDefault();
            dismissNotification("{{ route('donationnoti') }}", { donationid: $(this).attr('donation_id') }, $(this));
        });

        // Handle Individual Topup Notification
        $("#contentContainer").on('click', '.topupBtn', function(e){
            e.preventDefault();
            dismissNotification("{{ route('topupnoti') }}", { topupid: $(this).attr('topup_id') }, $(this));
        });

        // Handle Clear All Notifications Button
        $("#clearAllNotiBtn").on('click', function(){
            var $btn = $(this);
            
            // Add loading state to button
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Clearing...');

            $.ajax({
                url: "{{ route('clearallnoti') }}",
                method: "POST",
                success: function (response) {
                    if (response.status == 300) {
                        $(".ermsg").html(response.message);
                        
                        // Close all alerts smoothly
                        $("#contentContainer .alert").each(function(){
                            $(this).alert('close');
                        });
                    }
                    // Restore button state
                    $btn.prop('disabled', false).html('Clear All Notifications');
                },
                error: function (err) {
                    console.error('AJAX Error:', err);
                    $btn.prop('disabled', false).html('Clear All Notifications');
                }
            });
        });
    });
</script>
@endsection