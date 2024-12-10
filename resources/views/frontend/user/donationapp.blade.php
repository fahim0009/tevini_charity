@extends('frontend.layouts.appview')

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
    /* Simple spinner */
    #loader {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
    }

    #loader::after {
        content: "";
        display: block;
        width: 50px;
        height: 50px;
        border: 5px solid #fff;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -25px 0 0 -25px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

@php
    if (isset($_GET["cid"])) {
        $cid = $_GET["cid"];
    } 
    if (isset($_GET["amount"])) {
        $amount = $_GET["amount"];
    } 

               // donor balance
               $gettrans = Usertransaction::where([
                    ['user_id','=', auth()->user()->id],
                    ['status','=', '1']
                ])->orwhere([
                    ['user_id','=', auth()->user()->id],
                    ['pending','=', '1']
                ])->orderBy('id','DESC')->get();

                $donorUpBalance = 0;

                foreach ($gettrans as $key => $tran) {
                    if ($tran->t_type == "In") {
                        $donorUpBalance = $donorUpBalance + $tran->amount;
                    }elseif ($tran->t_type == "Out") {
                        $donorUpBalance = $donorUpBalance - $tran->amount;
                    } else {
                        # code...
                    }
                }
                // donor balance end

@endphp

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

                @if(isset($success))
                    <div class="alert alert-success">
                        {{ $success }}
                    </div>
                @endif

                @if(session()->has('success'))
                <section class="px-4">
                    <div class="row my-3">
                        <div class="alert alert-success" id="successMessage">{{ session()->get('success') }}</div>
                    </div>
                </section>
                @endif
                @if(session()->has('error'))
                <section class="px-4">
                    <div class="row my-3">
                        <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
                    </div>
                </section>
                @endif

                @if (isset($errors))
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    <section class="px-2">
                        <div class="row">
                            <div class="alert alert-danger">{{ $error }}</div>
                        </div>
                    </section>
                    @endforeach
                @endif
                @endif
                

            </div>
        </section>

        <!-- Image loader -->
        <div id='loader' style='display:none ;'>
            {{-- <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." /> --}}
        </div>
        <!-- Image loader -->
    </div>
    <form action="{{ route('onlinedonation.store') }}" method="POST" enctype="multipart/form-data" id="DonationForm">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="row ">
            <div class="col-lg-6  px-3">
                <h4 class="txt-dash mt-5">Account Balance</h4>
                <h3 id="usertestID"></h3>
                <h2 class="amount">{{  $donorUpBalance ? number_format($donorUpBalance, 2) : "Loading..." }}
                    GBP</h2>
                    
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Beneficiary</label>
                            <!-- <input type="text" class="form-control" placeholder="Select a charity"> -->
                            <select id="charity_id" name="charity_id" required class="form-control">
                                <option value="">Select a charity</option>
                                <option value="">Please Select</option>
                                @foreach (App\Models\Charity::all() as $charity)
                                <option value="{{ $charity->id }}" {{ old('charity_id') == $charity->id ?  "selected": "" }} @if (isset($cid)) @if ($charity->id == $cid) selected @endif @endif>{{ $charity->name }} - ({{ $charity->acc_no }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control me-3" name="amount" id="amount" placeholder="0.00" value="@if(isset($amount)){{$amount}}@endif{{old('amount')}}"> <span
                                    class="txt-secondary fs-16">GBP</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group ">
                            <label for=""> &nbsp; </label>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="ano_donation" id="ano_donation" class="form-check" {{ old('ano_donation') == "on" ?  "checked": "" }}> <span class="txt-secondary fs-16">Make this an anonymous donation</span>
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
                                <input type="checkbox" name="standard" id="standard" class="form-check"  {{ old('standard') == "on" ?  "checked": "" }}> <span
                                    class="txt-secondary fs-16">Set up a standing order</span>
                            </div>

                            <div class="{{ old('standard') == "on" ?  "selected": "standardOptions" }} my-4">
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
                                            <input type="date" class="form-control" name="starting" id="starting">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">INTERVAL</label>
                                            <select class="form-control" id="interval" name="interval">
                                                <option value="1">Monthly</option>
                                                <option value="3">Every 3 month</option>
                                                <option value="6">Every 6 month</option>
                                                <option value="12">Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Total</label>
                                            <input type="text" class="form-control" id="totalamt" name="totalamt" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group ">
                            <div class="d-flex  ">
                                <input type="checkbox" name="confirm_donation" id="confirm_donation" required class="form-check" style="width: 56px;" {{ old('confirm_donation') == "on" ?  "checked": "" }}>
                                 <div
                                    class="txt-secondary fs-16">I confirm that this donation is for
                                    charitable purposes only, I will not benefit directly or indirectly by
                                    way of goods or services from the donation.</div>
                            </div>
                        </div>
                    </div>
                    
                </div>

            </div>
            <div class="col-lg-6 border-left-lg px-3">
                <div class="col-lg-12 mt-5">
                    <div class="form-group ">
                        <label for="">Notes to charity</label>
                        <textarea id="charitynote" name="charitynote" class="border-0 mt-2 w-100" rows="6">{{ old('charitynote') }}</textarea>
                    </div>
                </div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <label for="">My Notes</label>
                        <textarea name="mynote" id="mynote" class="border-0 mt-2 w-100" rows="6">{{ old('mynote') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mt-2">
                <div class="form-group ">
                    <input type="hidden" id="userid" name="userid" value="{{Auth::user()->id}}">
                    {{-- <input type="button" id="addBtn" value="Make Donation" class="btn-theme bg-primary"> --}}
                    <button class="btn-theme bg-primary" type="submit">Make a donation</button>
                </div>
            </div>
        </div>
    </form>
</div>



@endsection

@section('script')
<script>
    document.getElementById('DonationForm').addEventListener('submit', function() {
        document.getElementById('loader').style.display = 'block';
    });
</script>
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
                var amount = Number($("#amount").val());
                if (number_payments == "2") {
                    $("#number_payments").val(" ");
                    $("#totalamt").val(amount);
                    $("#number_payments").attr("disabled", true);
                  }else{
                    $("#number_payments").attr("disabled", false);
                  }
        });

        //calculation end
        $("#amount, #number_payments").keyup(function(){
            var number_payments = Number($("#number_payments").val());
            var amount = Number($("#amount").val());
            console.log(number_payments);
            if (number_payments == 0) {
                
            var totalamt = amount;
            $("#totalamt").val(totalamt.toFixed(2));

            } else {
                
            var totalamt = amount * number_payments;
            $("#totalamt").val(totalamt.toFixed(2));
            
            }
        });
        //calculation end  

 //header for csrf-token is must in laravel
 $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            //  make doantion start
        
            $("#addBtn").click(function(){
                
                if(!confirm('Are you sure?')) return;
                
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
                    var userid= $("#userid").val();

                    if($("#standard").is(":checked")) {
                        var url = "{{URL::to('/user/standing-donation')}}";
                    } else {
                        var url = "{{URL::to('/user/make-donation')}}";
                    }


                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {charity_id,amount,ano_donation,standard,payments_type,number_payments,starting,interval,c_donation,charitynote,mynote,userid},
                        success: function (d) {
                            console.log(d.data)
                            
                            
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
