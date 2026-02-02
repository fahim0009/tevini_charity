@extends('layouts.admin')
@section('content')

<style>
    input.largerCheckbox {
      width: 25px;
      height: 25px;
    }

    .inner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .inner .left {
        flex-basis: 50px;
    }

    .inner .right {
        flex: 1;
    }
</style>
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')
  <section class="">

   <div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="et:wallet"></span> <div class="mx-2">
               Order Voucher Book
            </div>
        </div>
        <div class="ermsg">

        </div>
    </section>
<!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
   </div>
 <!-- Image loader -->
 <section class="px-4">
    <div class="row my-3">
        {{-- <div class="col-12 text-center"> --}}
            <div class="col-2"></div>
            <div class="col-8">
                <div class="orderVoucher">
                    @foreach (App\Models\Voucher::where('status','=','1')->get() as $voucher )
                        <div class="voucher">
                            <div class="items">
                                <span>£{{ number_format($voucher->single_amount, 2)  }} </span>
                                <input type="hidden" value="{{$voucher->id}}" name="v_ids[]">
                                <input type="hidden" value="{{$voucher->single_amount}}" name="single_amount[]">
                                @if ($voucher->type == 'Prepaid')
                                    <div class="badge rounded-pill bg-secondary">{{ $voucher->type }}</div>
                                    <span class="h6">(@if($voucher->note){{$voucher->note}}@endif)</span>
                                @else
                                    <div class="badge rounded-pill bg-info">{{ $voucher->type }}</div>
                                    <span class="h6">(@if($voucher->note){{$voucher->note}}@endif)</span>
                                @endif
                            </div>
                            <div class="items">
                                <div class="cart mx-auto">
                                    <button id="dec{{$voucher->id}}" onclick="dec({{$voucher->id}},{{ $voucher->amount }})">-</button>
                                    <input type="hidden" class="total" id="vamnt{{$voucher->id}}" value="">
                                    <input type="text" name="qty[]" id="cartValue{{$voucher->id}}" class="qty" value="0" v_amount="{{ $voucher->amount }}" v_type="{{ $voucher->type }}" v_id="{{$voucher->id}}">
                                    <button id="inc{{$voucher->id}}" onclick="inc({{$voucher->id}},{{ $voucher->amount }})">+</button>
                                </div>
                            </div>
                            {{-- <div class="items">
                                <div class="badge rounded-pill bg-info">{{ $voucher->stock }} Stock</div>
                            </div> --}}
                            <div class="items">
                                @if($voucher->type == "Prepaid")
                                    <div id="amt{{$voucher->id}}"><div class="items">£0</div></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                        <input type="hidden" value="{{$donor_id}}" id="donner_id">
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <h3>Delivery</h3>
                            </div>
                        </div>
                        <div class="row my-4">
                            <div class="col-lg-6">
                                <div class="inner">
                                    <div class="left text-center">
                                        <input type="checkbox" id="delivery" name="delivery" class="form-check largerCheckbox delivery_option">
                                    </div>
                                    
                                    <div class="right">
                                        <div class="title">
                                               Express delivery
                                        </div>
                                        <span class="bottom-data"> 1-2 working days</span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-lg-6">
                                <div class="inner">
                                    <div class="left text-center">
                                        <input type="checkbox" id="collection"  name="collection"  class="form-check largerCheckbox delivery_option">
                                    </div>
                                    <div class="right">
                                        <div class="title">
                                              Collection
                                        </div>
                                        <span class="bottom-data">100 fairholt Rd London N16 5HN <br> Mon - Thu:
                                            10:00 - 17:00 Fri: 10:00 - 13:00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="dmsg" id="dmsg">
        
                                    <div class='alert alert-danger'><b>Charge £3.50 on Prepaid voucher orders less than £200. </b></div>
        
                                </div>
                            </div>
                        </div>


                        
                        <div class="row my-4">
                            <div class="col-lg-6">
                                <div class="">
                                    <div class="">
                                        <div class="title">
                                               Delivery Charge
                                        </div>
                                        <input style="max-width:136px" type="text" id="d_charge" value="" class="rounded text-center form-control fw-bold border-0" placeholder="£0.00" readonly>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-lg-6">
                                <div class="">
                                    <div class="">
                                        <div class="title">
                                            Order total
                                        </div>
                                        <input style="max-width:136px" type="text" id="net_total" value="" class="rounded text-center form-control fw-bold border-0" placeholder="£0.00" readonly>
                                    </div>
                                </div>
                            </div>
                            
                        </div>


                    <div class="col-md-12 my-2">
                        <button class="text-white btn-theme ml-1 mb-4" id="addvoucher" type="button">Order Voucher</button>
                    </div>

                </div>
            </div>
            <div class="col-2"></div>
        {{-- </div> --}}
    </div>
  </section>
</div>

  </section>
</div>
@endsection

@section('script')
<script src='{{ asset('assets/user/js/app.js') }}'> </script>
<script>


    $(document).ready(function () {

        $("#dmsg").hide();
        $('[type="checkbox"]').change(function(){
            if(this.checked){
                $('[type="checkbox"]').not(this).prop('checked', false);
            }
        });



        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //
        var url = "{{URL::to('/admin/addvoucher')}}";

        $("#addvoucher").click(function(){

            var del = document.getElementById("delivery");
            var col = document.getElementById("collection");

            if (!del.checked && !col.checked) {
                alert('Please, choose a delivery option!!');
                return;
            }



            var delivery = $('#delivery').prop('checked');
            var collection = $('#collection').prop('checked');
            

            $("#loading").show();

            var voucherIds = $("input[name='v_ids[]']")
              .map(function(){return $(this).val();}).get();

            var qtys = $("input[name='qty[]']")
              .map(function(){return $(this).val();}).get();

            var single_amounts = $("input[name='single_amount[]']")
              .map(function(){return $(this).val();}).get();

            var did = $("#donner_id").val();
            var net_total = $("#net_total").val();
            
            
            if (del.checked) {
                if (net_total < 200) {
                var delivery_charge = 3.50;
                } else {
                var delivery_charge = 0;
                }
            } else {
                var delivery_charge = 0;
                
            }

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {voucherIds:voucherIds,qtys:qtys,did:did,delivery_charge:delivery_charge,delivery:delivery,collection:collection,single_amounts:single_amounts},

                    success: function (d) {
                        console.log(d);
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){location.reload()},2000)
                        }
                    },
                    complete:function(d){
                        $("#loading").hide();
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

        });

        net_amnt_total();
        // each row total price show
        $("body").delegate(".qty","keyup",function(event){
            event.preventDefault();
            var total = 0;

            var row = $(this).parent().parent();
            var voucherId = row.find('.qty').attr("v_id");
            var type = row.find('.qty').attr("v_type");
            var amount = row.find('.qty').attr("v_amount");
            var qty = row.find('.qty').val();
                if (type == "Prepaid") {
                var total = amount * qty;

                var net_total = total + 3.50;

                var delivery = $('#delivery').prop('checked');
                
                    if (total<200) {
                        if (delivery == 'true') {
                            $("#dmsg").show();
                            $('#net_total').val(net_total.toFixed(2));
                            $('[type="checkbox"]').prop('checked', false);
                        } else {
                            $("#dmsg").hide();
                            $('#net_total').val(total.toFixed(2));
                            $('[type="checkbox"]').prop('checked', false);
                        }


                    } else {
                        $("#dmsg").hide();
                        $('#net_total').val(total.toFixed(2));
                        $('[type="checkbox"]').prop('checked', false);
                    }
                
                } else { 
                var total = parseInt('00');
                }
                // var total = amount * qty;
            row.find('.total').val(total.toFixed(2));
            $('#amt'+voucherId).html('<div class="items">£'+total+'</div>');
            net_amnt_total();
            // <div class="items">£0</div>

        })

        // net total
        function net_amnt_total(){
            var total = 0;
            $('.total').each(function(){
                total += ($(this).val()-0);
            })
            
            var net_total = total + 3.50;
            var delivery = $('#delivery').prop('checked');
            if (delivery == 'true') {
                if (total<200) {
                    $("#dmsg").show();
                    $('#net_total').val(net_total.toFixed(2));
                } else {
                    $("#dmsg").hide();
                    $('#net_total').val(total.toFixed(2));
                }
            } else {
                    $("#dmsg").hide();
                $('#net_total').val(total.toFixed(2));
            }
        }





        

        $("#delivery").click(function() {
            var total = 0;
            $('.total').each(function(){
                total += ($(this).val()-0);
            })

            var net_total = total + 3.50;
            if (total > 0) {
                if($(this).is(":checked")) {
                
                    if (total<200) {
                        $("#dmsg").show();
                        $('#net_total').val(net_total.toFixed(2));
                        $('#d_charge').val('3.50');
                    } else {
                        $("#dmsg").hide();
                        $('#net_total').val(total.toFixed(2));
                        $('#d_charge').val('');
                    }

                } else {
                        $("#dmsg").hide();
                    $('#net_total').val(total.toFixed(2));
                        $('#d_charge').val('');
                }
            }
            
        });

        $("#collection").click(function() {
            var total = 0;
            $('.total').each(function(){
                total += ($(this).val()-0);
            })

            if($(this).is(":checked")) {
                $("#dmsg").hide();
                $('#net_total').val(total.toFixed(2));
                $('#d_charge').val('');
            }
        });


    });

     
</script>
@endsection

