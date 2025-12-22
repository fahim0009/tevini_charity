@extends('layouts.admin')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
<style>
    .chkCircle{
    height: 25px;
    width: 25px;
    vertical-align: middle;
    }

</style>
<style>
    .donation-dashboard { background-color: #f8f9fa; min-height: 100vh; }
    .card { border-radius: 16px; overflow: hidden; }
    .icon-box {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
    .form-control, .form-select {
        padding: 0.6rem 1rem; border: 1px solid #dee2e6; border-radius: 8px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    .standing-order-box {
        background-color: #f1f4f9; border: 1px solid #e2e8f0;
    }
    .border-dashed { border-style: dashed !important; }
    .btn-primary {
        background-color: #0d6efd; border: none; padding: 12px 40px;
        font-weight: 600; border-radius: 10px; transition: all 0.3s ease;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
    input[type="checkbox"].form-check-input { width: 1.2em; height: 1.2em; cursor: pointer; }
</style>

<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')


<section class="donation-dashboard py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-4 px-5">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-light text-primary me-3">
                                <span class="iconify" data-icon="clarity:heart-solid" data-width="24"></span>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">Make a Donation</h4>
                                <small class="text-muted">Support your favorite charities or set up a regular gift.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-5 pb-5">
                        <div class="ermsg"></div>

                        <form action="{{ route('donation.store') }}" method="POST" enctype="multipart/form-data" id="donationForm">
                            @csrf
                            <input type="hidden" value="{{$donor_id}}" id="donner_id">

                            <div class="row g-4 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Select Beneficiary</label>
                                    <select class="form-select @error('charity_id') is-invalid @enderror" id="charity_id" name="charity_id" required>
                                        <option value="" selected disabled>Choose a charity...</option>
                                        @foreach (App\Models\Charity::all() as $charity)
                                        <option value="{{ $charity->id }}">{{ $charity->name }} ({{ $charity->acc_no }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Donation Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">£</span>
                                        <input type="number" step="0.01" placeholder="0.00" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror">
                                        <select class="form-select border-start-0 bg-light" name="currency" id="currency" style="max-width: 100px;">
                                            <option value="GBP">GBP</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="ano_donation" id="ano_donation">
                                        <label class="form-check-label fw-medium" for="ano_donation">Anonymous Donation</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25">

                            <div class="standing-order-box rounded-3 p-4 mb-4" id="standingOrderWrapper">
                                <div class="form-check custom-checkbox mb-0">
                                    <input type="checkbox" class="form-check-input me-2" name="standard" id="standard">
                                    <label class="form-check-label fw-bold" for="standard">SETUP A STANDING ORDER</label>
                                </div>
                                <p class="text-muted small mt-1 ms-4">Regular giving helps charities plan for the future.</p>

                                <div id="standingOptions" style="display: none;" class="mt-4 pt-3 border-top">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">PAYMENT TYPE</label>
                                            <select class="form-select" name="payments_type" id="payments_type">
                                                <option value="1">Fixed number of payments</option>
                                                <option value="2">Continuous payments</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6" id="numPaymentsCol">
                                            <label class="form-label small fw-bold">NUMBER OF PAYMENTS</label>
                                            <input type="number" class="form-control" name="number_payments" id="number_payments" placeholder="e.g. 12">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">STARTING DATE</label>
                                            <input type="date" class="form-control" name="starting" id="starting">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">INTERVAL</label>
                                            <select class="form-select" id="interval" name="interval">
                                                <option value="1">Monthly</option>
                                                <option value="3">Every 3 months</option>
                                                <option value="6">Every 6 months</option>
                                                <option value="12">Yearly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <textarea class="form-control border-dashed" name="charitynote" placeholder="Notes to Charity..." rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <textarea class="form-control border-dashed" name="mynote" placeholder="Personal Notes..." rows="3"></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="alert alert-light border d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-3" name="confirm_donation" id="confirm_donation" required>
                                        <label for="confirm_donation" class="small text-dark mb-0">
                                            I confirm that this donation is for charitable purposes only. I will not benefit directly or indirectly from this donation.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-5">
                                <button type="button" id="addBtn" class="btn btn-primary btn-lg px-5 shadow-sm">
                                    Complete Donation
                                </button>
                                <div id="loading" class="mt-3" style="display:none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ms-2 small text-muted">Processing...</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


</div>
@endsection
@section('script')
<script>
     $(document).ready(function () {

        $("#payments_type").change(function () {
                var number_payments = $(this).val();
                if (number_payments == "2") {
                    $("#number_payments").val(" ");
                    $("#number_payments").attr("disabled", true);
                  }else{
                    $("#number_payments").attr("disabled", false);
                  }
        });

        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            //  make doantion start
            $("#addBtn").click(function(){
                 $("#loading").show();
                    var donner_id= $("#donner_id").val();
                    var charity_id= $("#charity_id").val();
                    var amount= $("#amount").val();
                    var ano_donation= $('#ano_donation').prop('checked');
                    var standard= $('#standard').prop('checked');
                    var payments_type= $("#payments_type").val();
                    var number_payments= $("#number_payments").val();
                    var starting= $("#starting").val();
                    var interval= $("#interval").val();
                    var c_donation= $('#confirm_donation').prop('checked');
                    var charitynote= $("#charitynote").val();
                    var mynote= $("#mynote").val();

                    if($("#standard").is(":checked")) {
                        var url = "{{URL::to('/admin/make-stnddonation')}}";
                    } else {
                        var url = "{{URL::to('/admin/make-donation')}}";
                    }


                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {donner_id,charity_id,amount,ano_donation,standard,payments_type,number_payments,starting,interval,c_donation,charitynote,mynote},
                        success: function (d) {

                        console.log(d);

                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        complete:function(data){
                            $("#loading").hide();
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });
            // make donation end


});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
    $('#charity_id').select2({
      width: '100%',
      placeholder: "Select an Option",
      allowClear: true
    });
  </script>


<script>
$(document).ready(function() {
    // Toggle Standing Order Options
    $('#standard').on('change', function() {
        if($(this).is(':checked')) {
            $('#standingOptions').slideDown(300);
            // Business Rule: Disable Anonymous if Standing Order is checked
            $('#ano_donation').prop('checked', false).prop('disabled', true);
        } else {
            $('#standingOptions').slideUp(300);
            $('#ano_donation').prop('disabled', false);
        }
    });

    // Toggle Number of Payments based on Type
    $('#payments_type').on('change', function() {
        if($(this).val() == "2") { // Continuous
            $('#numPaymentsCol').fadeOut();
        } else {
            $('#numPaymentsCol').fadeIn();
        }
    });


});
</script>

@endsection
