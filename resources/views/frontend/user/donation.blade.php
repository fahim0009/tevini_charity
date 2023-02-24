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

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Make a donation/Standing order
            </div>
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
    </div>
    <form action="{{ route('donation.store') }}" method="POST" enctype="multipart/form-data">
        <div class="row ">
            <div class="col-lg-6  px-3">
                <h4 class="txt-dash mt-5">Account Balance</h4>
                <h2 class="amount">{{auth()->user()->balance}} GBP</h2>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Beneficiary</label>
                            <!-- <input type="text" class="form-control" placeholder="Select a charity"> -->
                            <select id="charity_id" name="charity_id" required class="form-control">
                                <option value="">Select a charity</option>
                                <option value="">Please Select</option>
                                @foreach (App\Models\Charity::all() as $charity)
                                <option value="{{ $charity->id }}">{{ $charity->name }} - ({{ $charity->acc_no }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control me-3" name="amount" id="amount" placeholder="0.00"> <span
                                    class="txt-secondary fs-16">GBP</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group ">
                            <label for=""> &nbsp; </label>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="ano_donation" id="ano_donation" class="form-check"> <span
                                    class="txt-secondary fs-16">Make this an anonymous donation</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group ">
                            <span class="txt-secondary fs-16">Please note that it is not possible to make a
                                anonymous standing order.
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-2">
                        <div class="form-group ">

                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="standard" id="standard" class="form-check"> <span
                                    class="txt-secondary fs-16">Set up a standing order</span>
                            </div>

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
                    <div class="col-lg-12">
                        <div class="form-group ">
                            <div class="d-flex  ">
                                <input type="checkbox" name="confirm_donation" id="confirm_donation" required class="form-check" style="width: 56px;">
                                 <div
                                    class="txt-secondary fs-16">I confirm that this donation is for
                                    charitable purposes only, I will not benefit directly or indirectly by
                                    way of goods or services from the donation.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-4">
                        <div class="form-group ">
                            <input type="button" id="addBtn" value="Make Donation" class="btn-theme bg-primary">
                            {{-- <button class="btn-theme bg-primary" id="addBtn">Make a donation</button> --}}
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 border-left-lg px-3">
                <div class="col-lg-12 mt-5">
                    <div class="form-group ">
                        <label for="">Notes to charity</label>
                        <textarea id="charitynote" name="charitynote" class="border-0 mt-2 w-100" rows="8"></textarea>
                    </div>
                </div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <label for="">My Notes</label>
                        <textarea name="mynote" id="mynote" class="border-0 mt-2 w-100" rows="8"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>



@endsection

@section('script')
<script>
     $(document).ready(function () {

        $(".standardOptions").hide();
        $("#standard").click(function() {
            if($(this).is(":checked")) {
                $(".standardOptions").show(300);
            } else {
                $(".standardOptions").hide(200);
            }
        });

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
                                $(".rightbar").animate({ scrollTop: 0 }, "fast");
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                $(".rightbar").animate({ scrollTop: 0 }, "fast");
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
