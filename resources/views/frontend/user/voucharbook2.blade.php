@extends('frontend.layouts.user')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>
<style>
body {
  overflow-x: hidden;
}
.btn-add {
    border: 0;
    width: auto;
    /* margin: 5px; */
    border-radius: 7px;
    padding: 6px 12px;
    font-size: 16px;
    color: #fff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-content: center;
    font-family: "DarkerGrotesque-semibold", sans-serif;
}
</style>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Order voucher books
            </div>
            <div class="ermsg">

            </div>
        </div>
    </div>
<!-- Image loader -->
<div id='loading' style='display:none ;'>
    <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
</div>
<!-- Image loader -->

        <div class="row ">
            <div class="col-lg-12 px-3">
                <div class="row my-4">

                    @foreach (App\Models\Voucher::where('status','=','1')->get() as $voucher )
                    <div class="col-lg-4">
                        <div class="inner my-3">
                            <div class="left text-center">
                                {{-- <input type="hidden" value="{{$voucher->id}}" name="v_ids[]">
                                <input type="hidden" class="total" value="">
                                <input type="text" class="box-input qty" v_amount="{{ $voucher->amount }}" v_type="{{ $voucher->type }}" data-type="{{ $voucher->type }}" name="qty[]" id="cartValue{{$voucher->id}}" value="0">
                                <label>Qty</label> --}}
                                @if ($voucher->type == 'Prepaid')
                                    <span class="btn-add bg-primary text-white add-to-cart" voucherID="{{$voucher->id}}" v_amount="{{ $voucher->amount }}" v_type="{{ $voucher->type }}" v_note="{{ $voucher->note }}" data-type="{{ $voucher->type }}" single_amount="{{$voucher->single_amount}}" style="cursor: pointer;">+</span>
                                @else
                                    <span class="btn-add bg-secondary text-white add-to-cart" voucherID="{{$voucher->id}}" v_amount="{{ $voucher->amount }}" v_type="{{ $voucher->type }}" v_note="{{ $voucher->note }}" data-type="{{ $voucher->type }}" single_amount="{{$voucher->single_amount}}" style="cursor: pointer;">+</span>
                                @endif

                            </div>
                            <div class="right">
                                <div class="title">
                                    £{{ $voucher->single_amount }} @if ($voucher->single_amount == "0" ) <span class="bottom-data">Blank Cheque</span>  @endif
                                    @if ($voucher->type == 'Prepaid')
                                    <div class="badge prepaid">
                                        Prepaid
                                    </div>
                                    @else
                                    <div class="badge postpaid">
                                        Postpaid
                                    </div>
                                    @endif

                                </div>
                                
                                <span class="bottom-data">{{$voucher->note}} @if ($voucher->type == 'Prepaid') = £{{ $voucher->amount }} @endif</span>
                                
                            </div>
                        </div>
                    </div>
                    @endforeach


                </div>
            </div>

            <div class="col-lg-12"> 
                <div class="calculatior mt-3" style="min-height: 0px">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Your basket</div> <br>
                            <div class="otherermsg"></div>

                            <div class="data-container">

                                <table  class="table inner table-theme mt-0" id="">
                                    <tbody id="cardinner" class="right">
                                        {{-- <tr class="item-row" style="position:realative;"> 
                                            <td width="40px"> <div style="color: white; user-select:none; padding: 2px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 8px;">X</div>
                                            </td>
                                            <td width="250px"> 
                                                <div class="title">
                                                    <span class="bottom-data">Blank Cheque</span>
                                                    <div class="badge prepaid">
                                                        Prepaid
                                                    </div>
                                                </div>
                                                <span class="bottom-data">100 vouchers = £100</span>
                                            </td>

                                            <td width="150px"> <input style="min-width: 50px;" type="number" name="qty[]" class="form-control" value=""> </td>
                                            
                                            
                                            <td width="150px">  <input style="min-width: 50px;" type="text" name="total[]" class="form-control" value="" readonly> </td>
                                        </tr>
                                         --}}

                                        @foreach ($cart as $item)

                                        @php
                                            $cartVoucher = \App\Models\Voucher::where('id', $item->voucher_id)->first();
                                        @endphp

                                        <tr class="item-row" style="position:realative;"> 
                                            <td width="40px"> <div style="color: white; user-select:none; padding: 2px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 8px;" data-cartid="{{ $item->id }}" data-cart-index="{{ $loop->index }}" class="remove-from-cart">X</div>
                                            </td>
                                            <td width="250px"> 
                                                <div class="title">
                                                    
                                                    <input type="hidden" value="{{ $item->voucher_id }}" name="v_ids[]">
                                                    @if ($cartVoucher->type == 'Prepaid') 
                                                    <input type="hidden" class="total" id="sub{{ $item->voucher_id }}" value="{{ $item->tamount }}">
                                                    @else 
                                                    <input type="hidden" class="total" value="">
                                                    @endif
                                                    @if ($cartVoucher->single_amount == "0" ) <span class="bottom-data">Blank Cheque</span> @else <span class="bottom-data">£{{ $cartVoucher->single_amount }}   </span> @endif

                                                    <div class="badge @if ($cartVoucher->type == "Prepaid") prepaid @else postpaid @endif">
                                                        {{ $cartVoucher->type }}
                                                    </div>
                                                </div>
                                                <span class="bottom-data"> {{$cartVoucher->note}} @if ($cartVoucher->type == 'Prepaid') = £{{ $cartVoucher->single_amount  }} @endif</span>
                                            </td>

                                            <td width="80px"> <input style="min-width: 50px;" type="text" name="qty[]" class="form-control qty" onkeypress="return /[0-9]/i.test(event.key)" value="{{$item->qty}}"  v_amount="{{ $item->tamount }}" v_type="{{ $cartVoucher->type }}" data-type="{{ $cartVoucher->type }}" vid="{{$item->voucher_id}}"  id="cartValue{{$item->voucher_id}}"> </td>
                                            
                                            
                                            <td width="150px" class="d-none">  <input style="min-width: 50px;" type="number" name="total[]" class="form-control vtotal" id="vtotal{{$item->voucher_id}}" @if ($cartVoucher->type == 'Prepaid') value="{{ $item->tamount }}" @else value="" @endif readonly> </td>

                                        </tr>

                                    @endforeach




                                    </tbody>

                                </table>

                                
                            </div>



                        </div>

                        <div class="col-lg-6">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">
                                        Delivery
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-lg-12 px-3">
                                    <div class="row my-4">
                                        <div class="col-lg-12">
                                            <div class="dmsg" id="dmsg">
                    
                                                <div class='alert alert-danger'><b>Charge £3.50 on Prepaid voucher orders less than £200. </b></div>
                    
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="inner">
                                                <div class="left text-center">
                                                    <input type="checkbox" id="delivery" name="delivery" class="form-check delivery_option">
                                                </div>
                                                <div class="right">
                                                    <div class="title">
                                                        Express delivery
                                                    </div>
                                                    <span class="bottom-data"> 1-2 working days</span>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="inner">
                                                <div class="left text-center">
                                                    <input type="checkbox" id="collection"  name="collection"  class="form-check delivery_option">
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
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            
                            <div class="row my-4">
                                <div class="col-lg-6 d-flex align-items-center flex-wrap">
                                    <span class="fs-12  fw-bold" style="color:
                                    #003057">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8.16666 13.167H9.83332V8.16699H8.16666V13.167ZM8.99999 6.50033C9.2361 6.50033 9.43416 6.42033 9.59416 6.26033C9.7536 6.10088 9.83332 5.9031 9.83332 5.66699C9.83332 5.43088 9.7536 5.23283 9.59416 5.07283C9.43416 4.91338 9.2361 4.83366 8.99999 4.83366C8.76388 4.83366 8.5661 4.91338 8.40666 5.07283C8.24666 5.23283 8.16666 5.43088 8.16666 5.66699C8.16666 5.9031 8.24666 6.10088 8.40666 6.26033C8.5661 6.42033 8.76388 6.50033 8.99999 6.50033ZM8.99999 17.3337C7.84721 17.3337 6.76388 17.1148 5.74999 16.677C4.7361 16.2398 3.85416 15.6462 3.10416 14.8962C2.35416 14.1462 1.76055 13.2642 1.32332 12.2503C0.885545 11.2364 0.666656 10.1531 0.666656 9.00033C0.666656 7.84755 0.885545 6.76421 1.32332 5.75033C1.76055 4.73644 2.35416 3.85449 3.10416 3.10449C3.85416 2.35449 4.7361 1.7606 5.74999 1.32283C6.76388 0.885603 7.84721 0.666992 8.99999 0.666992C10.1528 0.666992 11.2361 0.885603 12.25 1.32283C13.2639 1.7606 14.1458 2.35449 14.8958 3.10449C15.6458 3.85449 16.2394 4.73644 16.6767 5.75033C17.1144 6.76421 17.3333 7.84755 17.3333 9.00033C17.3333 10.1531 17.1144 11.2364 16.6767 12.2503C16.2394 13.2642 15.6458 14.1462 14.8958 14.8962C14.1458 15.6462 13.2639 16.2398 12.25 16.677C11.2361 17.1148 10.1528 17.3337 8.99999 17.3337ZM8.99999 15.667C10.8611 15.667 12.4375 15.0212 13.7292 13.7295C15.0208 12.4378 15.6667 10.8614 15.6667 9.00033C15.6667 7.13921 15.0208 5.56283 13.7292 4.27116C12.4375 2.97949 10.8611 2.33366 8.99999 2.33366C7.13888 2.33366 5.56249 2.97949 4.27082 4.27116C2.97916 5.56283 2.33332 7.13921 2.33332 9.00033C2.33332 10.8614 2.97916 12.4378 4.27082 13.7295C5.56249 15.0212 7.13888 15.667 8.99999 15.667Z"
                                                fill="#003057" />
                                        </svg>
                                        The delivery address saved on your account is: {{Auth::user()->houseno}}, {{Auth::user()->street}}
                                        {{Auth::user()->town}} {{Auth::user()->postcode}}</span> <br>
                                    {{-- <span class="fs-12 txt-secondary">
                                        If you would like to change it you can do so here
                                    </span> --}}
                                </div>
                                <div class="col-lg-6 d-flex align-items-center flex-wrap">
                                    <span class="fs-16 " style="color:
                                    #003057">Order total</span>
                                    <input style="max-width:136px" type="text" id="net_total" value="" class="rounded text-center mx-3 form-control fw-bold border-0" placeholder="£0.00">
                                    <input type="hidden" value="{{auth()->user()->id}}" id="donner_id">
                                    <button class="btn-theme bg-primary text-white" id="addvoucher" type="button">Place order</button>
                                    {{-- <a href="#" class="btn-theme bg-primary text-white">Place order</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                

            </div>


        </div>
        {{-- <div class="row mt-4">
            <div class="col-lg-12">
                <div class="pagetitle pb-2">
                    Delivery
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-lg-12 px-3">
                <div class="row my-4">
                    <div class="col-lg-4">
                        <div class="inner">
                            <div class="left text-center">
                                <input type="checkbox" id="delivery" name="delivery" class="form-check delivery_option">
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
                                <input type="checkbox" id="collection"  name="collection"  class="form-check delivery_option">
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
                        <span class="fs-12  fw-bold" style="color:
                        #003057">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.16666 13.167H9.83332V8.16699H8.16666V13.167ZM8.99999 6.50033C9.2361 6.50033 9.43416 6.42033 9.59416 6.26033C9.7536 6.10088 9.83332 5.9031 9.83332 5.66699C9.83332 5.43088 9.7536 5.23283 9.59416 5.07283C9.43416 4.91338 9.2361 4.83366 8.99999 4.83366C8.76388 4.83366 8.5661 4.91338 8.40666 5.07283C8.24666 5.23283 8.16666 5.43088 8.16666 5.66699C8.16666 5.9031 8.24666 6.10088 8.40666 6.26033C8.5661 6.42033 8.76388 6.50033 8.99999 6.50033ZM8.99999 17.3337C7.84721 17.3337 6.76388 17.1148 5.74999 16.677C4.7361 16.2398 3.85416 15.6462 3.10416 14.8962C2.35416 14.1462 1.76055 13.2642 1.32332 12.2503C0.885545 11.2364 0.666656 10.1531 0.666656 9.00033C0.666656 7.84755 0.885545 6.76421 1.32332 5.75033C1.76055 4.73644 2.35416 3.85449 3.10416 3.10449C3.85416 2.35449 4.7361 1.7606 5.74999 1.32283C6.76388 0.885603 7.84721 0.666992 8.99999 0.666992C10.1528 0.666992 11.2361 0.885603 12.25 1.32283C13.2639 1.7606 14.1458 2.35449 14.8958 3.10449C15.6458 3.85449 16.2394 4.73644 16.6767 5.75033C17.1144 6.76421 17.3333 7.84755 17.3333 9.00033C17.3333 10.1531 17.1144 11.2364 16.6767 12.2503C16.2394 13.2642 15.6458 14.1462 14.8958 14.8962C14.1458 15.6462 13.2639 16.2398 12.25 16.677C11.2361 17.1148 10.1528 17.3337 8.99999 17.3337ZM8.99999 15.667C10.8611 15.667 12.4375 15.0212 13.7292 13.7295C15.0208 12.4378 15.6667 10.8614 15.6667 9.00033C15.6667 7.13921 15.0208 5.56283 13.7292 4.27116C12.4375 2.97949 10.8611 2.33366 8.99999 2.33366C7.13888 2.33366 5.56249 2.97949 4.27082 4.27116C2.97916 5.56283 2.33332 7.13921 2.33332 9.00033C2.33332 10.8614 2.97916 12.4378 4.27082 13.7295C5.56249 15.0212 7.13888 15.667 8.99999 15.667Z"
                                    fill="#003057" />
                            </svg>
                            The delivery address saved on your account is: {{Auth::user()->houseno}}, {{Auth::user()->street}}
                            {{Auth::user()->town}} {{Auth::user()->postcode}}</span> <br>
                            
                    </div>
                    <div class="col-lg-6 d-flex align-items-center flex-wrap">
                        <span class="fs-16 " style="color:
                        #003057">Order total</span>
                        <input style="max-width:136px" type="text" id="net_total" value="" class="rounded text-center mx-3 form-control fw-bold border-0" placeholder="£0.00">
                        <input type="hidden" value="{{auth()->user()->id}}" id="donner_id">
                        <button class="btn-theme bg-primary text-white" id="addvoucher" type="button">Place order</button>
                    </div>
                </div>
            </div>
        </div> --}}
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        // update cart 
       

        $("#dmsg").hide();
        $('[type="checkbox"]').change(function(){
            if(this.checked){
                $('[type="checkbox"]').not(this).prop('checked', false);
            }
        });

        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

         var url = "{{URL::to('/user/addvoucher')}}";


        $("#addvoucher").click(function(){

            if(!confirm('Are you sure?')) return;
            
            $("#loading").show();

            var voucherIds = $("input[name='v_ids[]']")
              .map(function(){return $(this).val();}).get();

            var qtys = $("input[name='qty[]']")
              .map(function(){return $(this).val();}).get();


            var did = $("#donner_id").val();
            var net_total = $("#net_total").val();
            var delivery = $('#delivery').prop('checked');
            var collection = $('#collection').prop('checked');
            
            var del = document.getElementById("delivery");
            var col = document.getElementById("collection");

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
                    data: {voucherIds,qtys,did,delivery,collection,delivery_charge},

                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            $(".rightbar").animate({ scrollTop: 0 }, "fast");
                        }else if(d.status == 300){

                            $(".ermsg").html(d.message);
                            $(".rightbar").animate({ scrollTop: 0 }, "fast");
                            window.setTimeout(function(){location.reload()},2000);
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


        // each row total price show
        $("body").delegate(".qty","keyup",function(event){
            event.preventDefault();
            var total = 0;

            var row = $(this).parent().parent();
            var type = row.find('.qty').attr("v_type");
            var amount = row.find('.qty').attr("v_amount");
            var qty = row.find('.qty').val();
            var vid = row.find('.qty').attr("vid");
            var vtotal = amount * qty;
                if (type == "Prepaid") {
                var total = amount * qty;

                var net_total = total + 3.50;

                var delivery = $('#delivery').prop('checked');
                var del = document.getElementById("delivery");
                var col = document.getElementById("collection");
                
                    $('#vtotal'+vid).val(vtotal.toFixed(2));
                    console.log(vtotal, vid, amount);
                    if (total<200) {
                        if (del.checked) {
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
                $('[type="checkbox"]').prop('checked', false);
                }
                // var total = amount * qty;
            row.find('.total').val(total.toFixed(2));
            net_total_value();

	})

    // net total
    function net_total_value(){
		var total = 0;
		$('.total').each(function(){
			total += ($(this).val()-0);
		})
        
        var net_total = total + 3.50;
        var delivery = $('#delivery').prop('checked');
        var del = document.getElementById("delivery");
        var col = document.getElementById("collection");
        if (del.checked) {
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


        var del = document.getElementById("delivery");

            $( '.qty' ).each( function() {
                var type = $( this ).data('type');
                var value = $( this ).val();
                
                if (type == "Prepaid") {
                    if (value > 0) {

                        var net_total = total + 3.50;
                        if(del.checked) {
                            
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
                }
                
            });
    });

    $("#collection").click(function() {
        var total = 0;
        $('.total').each(function(){
            total += ($(this).val()-0);
        })

        if($(this).is(":checked")) {
            $("#dmsg").hide();
            $('#net_total').val(total.toFixed(2));
        }
    });

    

    // add to cart btn
        $(document).on('click', '.add-to-cart', function (e) {
            e.preventDefault();


            // swal.fire({ type: 'success', title: 'Done!', 
            // showConfirmButton: false, timer: 1500 
            // });

            v_amount = $(this).attr('v_amount');
            voucherID = $(this).attr('voucherID');
            v_type = $(this).attr('v_type');
            v_note = $(this).attr('v_note');
            single_amount = $(this).attr('single_amount');
            quantity = 1;

            console.log(single_amount);

            // var cart = JSON.parse(localStorage.getItem('cart')) || [];

            // var existingItem = cart.find(function(item) {
            //     return item.v_amount === v_amount && 
            //            item.voucherID === voucherID && 
            //            item.v_type === v_type && 
            //            item.v_note === v_note && 
            //            item.single_amount === single_amount;
            // });

            // if (existingItem) {
            //     existingItem.quantity += quantity;
            //     // alert("work");
            //     $('#cartValue'+voucherID).val(existingItem.quantity);
            //     $('#vtotal'+voucherID).val(existingItem.quantity * v_amount);
            //     $('#sub'+voucherID).val(existingItem.quantity * v_amount);
                
            //     $.ajax({
            //         url: "{{ route('orderbook.cart.store') }}",
            //         method: "PUT",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             cart: JSON.stringify(cartlist)
            //         },
            //         success: function() {
            //             console.log('success2')
            //         }
            //     });
            //     return;
            // } else {
            //     var cartItem = {
            //         v_amount: v_amount,
            //         voucherID: voucherID,
            //         v_type: v_type,
            //         v_note: v_note,
            //         single_amount: single_amount,
            //         quantity: quantity
            //     };
            //     cart.push(cartItem);
            // }

            var v_ids = $("input[name='v_ids[]']")
                        .map(function(){return $(this).val();}).get();
                        
                        v_ids.push(voucherID);
                seen = v_ids.filter((s => v => s.has(v) || !s.add(v))(new Set));
                
                if (Array.isArray(seen) && seen.length) {
                    console.log(voucherID);
                    cartValue = $("#cartValue"+voucherID).val();
                    total_qty = parseFloat(cartValue) + 1;
                    $("#cartValue"+voucherID).val(total_qty);
                    $("#sub"+voucherID).val(total_qty*v_amount);
                    // $("#parent_product_qty"+pid).val(new_parent_product_qty);
                    swal.fire("Successfully added to the basket!");
                    $('[type="checkbox"]').prop('checked', false);
                    return;
                }




                

            $.ajax({
                url: "{{ route('orderbook.cart.store') }}",
                method: "POST",
                data: {v_amount,voucherID,v_type,v_note,single_amount,quantity},
                success: function (d) {
                    
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                        $(".rightbar").animate({ scrollTop: 0 }, "fast");
                    }else if(d.status == 300){

                        if (v_type == 'Prepaid') {
                            // prepaid
                            var markup = '<tr class="item-row" style="position:realative;"><td width="33%"> <div style="color: white; user-select:none; padding: 2px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 8px;" data-cartid="'+d.newID+'"  class="remove-from-cart">X</div></td><td width="33%"><div class="title"><input type="hidden" value="'+voucherID+'" name="v_ids[]"><input type="hidden" class="total"  id="sub'+voucherID+'" value="'+v_amount+'"><span class="bottom-data">£'+single_amount+'</span><div class="badge prepaid">'+v_type+'</div></div><span class="bottom-data">'+v_note+' £'+v_amount+'</span></td><td width="width" > <input style="min-width: 50px;" type="text" name="qty[]" class="form-control qty" onkeypress="return /[0-9]/i.test(event.key)" value="'+quantity+'"  v_amount="'+v_amount+'" v_type="'+v_type+'" data-type="'+v_type+'" vid="'+voucherID+'"  id="cartValue'+voucherID+'"> </td><td class="d-none">  <input style="min-width: 50px;" type="number" name="total[]" class="form-control vtotal" id="vtotal'+voucherID+'" value="'+v_amount+'" readonly> </td></tr>';
                                // prepaid end
                        } else if (single_amount < "1") {

                            var markup = '<tr class="item-row" style="position:realative;"><td width="40px"> <div style="color: white; user-select:none; padding: 2px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 8px;" data-cartid="'+d.newID+'"  class="remove-from-cart">X</div></td><td width="250px"><div class="title"><input type="hidden" value="'+voucherID+'" name="v_ids[]"><input type="hidden" class="total" value=""><span class="bottom-data">Blank Cheque</span><div class="badge postpaid">'+v_type+'</div></div><span class="bottom-data">'+v_note+'</span></td><td width="80px"> <input style="min-width: 50px;" type="text" name="qty[]" class="form-control qty" onkeypress="return /[0-9]/i.test(event.key)" value="'+quantity+'"  v_amount="'+v_amount+'" v_type="'+v_type+'" data-type="'+v_type+'" vid="'+voucherID+'"  id="cartValue'+voucherID+'"> </td><td width="150px" class="d-none">  <input style="min-width: 50px;" type="number" name="total[]" class="form-control vtotal" id="vtotal'+voucherID+'" value="" readonly> </td></tr>';

                        }else if (v_type == 'Postpaid') {

                            var markup = '<tr class="item-row" style="position:realative;"><td width="40px"> <div style="color: white; user-select:none; padding: 2px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 8px;" data-cartid="'+d.newID+'"  class="remove-from-cart">X</div></td><td width="250px"><div class="title"><input type="hidden" value="'+voucherID+'" name="v_ids[]"><input type="hidden" class="total" value=""><span class="bottom-data">£'+single_amount+'</span><div class="badge postpaid">'+v_type+'</div></div><span class="bottom-data">'+v_note+'</span></td><td width="80px"> <input style="min-width: 50px;" type="text" name="qty[]" class="form-control qty" onkeypress="return /[0-9]/i.test(event.key)" value="'+quantity+'"  v_amount="'+v_amount+'" v_type="'+v_type+'" data-type="'+v_type+'" vid="'+voucherID+'"  id="cartValue'+voucherID+'"> </td><td width="150px" class="d-none">  <input style="min-width: 50px;" type="number" name="total[]" class="form-control vtotal" id="vtotal'+voucherID+'" value="" readonly> </td></tr>';

                        } else {
                            
                        }
                        $("table #cardinner ").append(markup);
                        swal.fire("Successfully added to the basket!");
                        
                        
                    }
                },
            });
            
        });


        $(document).on('click', '.remove-from-cart', function(event) {
            
            event.target.parentElement.parentElement.remove();
            var index = $(this).data('cart-index');
            var cartid = $(this).data('cartid');
            
            $('[type="checkbox"]').prop('checked', false);
            if (cartid) {
                $.ajax({
                    url: "{{ route('orderbook.cart.store') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cartid: cartid
                    },
                    success: function() {
                        swal.fire("Successfully delete from the basket!");
                        console.log('success')
                    }
                });
            }
            
            net_total_value();
        });





    });

</script>
@endsection
