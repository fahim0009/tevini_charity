@extends('frontend.layouts.user')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
<style>
    .chkCircle{
    height: 25px;
    width: 25px;
    vertical-align: middle;
    }
    
</style>

<div class="dashboard-content">

    {{-- <section class=" px-4">
        <div class="alert mt-3 alert-warning alert-dismissible fade show" role="alert">
            <div class="d-inline">
                <span class="iconify" data-icon="akar-icons:info-fill"></span>
            </div>
            <small>Please note our office will be closed from Monday 27th December until Tuesday 4th
                January.
                Payments and vouchers will still be processed. i Emails, web contact forms and phone
                messages
                will still be monitored and replied to but may take slightly longer than usual.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </section> --}}
    <section class="profile purchase-status px-4">
        <div class="title-section">
            <span class="iconify" data-icon="clarity:heart-solid"></span>
            <div class="mx-2">Make a Donation/Standing Order</div>
        </div>

        <section class="px-4">
            <div class="row my-3">
                <div class="ermsg"></div>
            </div>
        </section>
        
        <!-- Image loader -->
<div id='loading' style='display:none ;'>
    <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
  </div>
  <!-- Image loader -->


        <div class="row mt-3">
            <div class="col-md-12">
                <div class="col-md-12 text-muted bg-white ">
                    <form action="{{ route('donation.store') }}" method="POST" enctype="multipart/form-data" class="gdp-form px-5">
                            @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="exampleDataList" class="form-label">Beneficiary</label>
                                <select class="form-control @error('charity_id') is-invalid @enderror" id="charity_id" name="charity_id" required>
                                    <option value="">Please Select</option>
                                    @foreach (App\Models\Charity::all() as $charity)                                        
                                    <option value="{{ $charity->id }}">{{ $charity->name }} - ({{ $charity->acc_no }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 ">
                                <input type="text" placeholder="Amount" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <select class="form-control @error('currency') is-invalid @enderror" name="currency" id="currency">
                                    <option value="GDP">GBP</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-group">
                                    <input type="checkbox" class="chkCircle" name="ano_donation" id="ano_donation">
                                    <label for="ano_donation"> ANONYMOUS DONATION</label>
                                </div>
                            </div>
                            <div class="col-md-12 my-3 text-dark">
                                <i>Please note that it is not possible to make a anonymous standing order.
                                </i>
                            </div>


                            <div class="col-md-12 d-flex align-items-center">
                                <div class="form-group w-100" class="standardOrder">
                                    <input type="checkbox" class="chkCircle" name="standard" id="standard">

                                    <label for="standard">
                                        SETUP A STANDING ORDER
                                    </label>

                                    <div class="standardOptions my-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="">PAYMENTS</label>
                                                    <select class="form-control" name="payments_type" id="payments_type">
                                                        <option value="1">Fixed number of payments</option>
                                                        <option value="2">Continuous payments</option>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="">NUMBER OF PAYMENTS</label>
                                                    <input type="text" class="form-control" name="number_payments" id="number_payments">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">STARTING</label>
                                                    <select class="form-control" name="starting" id="starting">
                                                        <option value="1 Jan 2022">1 Jan 2022</option>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">INTERVAL</label>
                                                    <select class="form-control" id="interval" name="interval">
                                                        <option value="Monthly">Monthly</option>
                                                        <option value="Every 3 month">Every 3 month</option>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12 my-3">
                                <div class="form-group d-flex align-items-center">
                                    <input type="checkbox" class="chkCircle" name="confirm_donation" id="confirm_donation" required>
                                    <label for="confirm_donation" class="mx-2">
                                        <small>
                                            I confirm that this donation is for charitable purposes only, I
                                            will not benefit directly or indirectly by way of goods or
                                            services from the donation.
                                        </small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 my-2">
                                <textarea class="form-control @error('charitynote') is-invalid @enderror" name="charitynote" placeholder="NOTES TO CHARITY" id="charitynote" cols="30" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 my-2">
                                <textarea class="form-control @error('mynote') is-invalid @enderror" name="mynote" placeholder="MY NOTES" id="mynote"
                                    cols="30" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 my-2">
                                <input type="button" id="addBtn" value="Make Donation" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
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
                    $("#number_payments").attr("disabled", true);
                  }else{
                    $("#number_payments").attr("disabled", false);
                  } 
        });

 //header for csrf-token is must in laravel
 $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            //  make doantion start
            var url = "{{URL::to('/user/make-donation')}}";
            $("#addBtn").click(function(){
                 $("#loading").show();
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

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {charity_id:charity_id,amount:amount,ano_donation:ano_donation,standard:standard,payments_type:payments_type,number_payments:number_payments,starting:starting,interval:interval,c_donation:c_donation,charitynote:charitynote,mynote:mynote},
                        success: function (d) {
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
  
@endsection
