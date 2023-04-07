@extends('frontend.layouts.user')
@section('content')
<style>
body {
  overflow-x: hidden;
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
                    <div class="col-lg-3">
                        <div class="inner my-3">
                            <div class="left text-center">
                                <input type="hidden" value="{{$voucher->id}}" name="v_ids[]">
                                <input type="hidden" class="total" value="">
                                <input type="text" class="box-input qty" v_amount="{{ $voucher->amount }}" v_type="{{ $voucher->type }}" name="qty[]" id="cartValue{{$voucher->id}}" value="0">
                                <label>Qty</label>
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
                                
                                <span class="bottom-data">{{$voucher->note}}@if ($voucher->type == 'Prepaid') = £{{ $voucher->amount }} @endif</span>
                                
                            </div>
                        </div>
                    </div>
                    @endforeach


                </div>
            </div>
        </div>
        <div class="row mt-4">
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
@endsection

@section('script')
<script>
    $(document).ready(function () {

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

            $("#loading").show();

            var voucherIds = $("input[name='v_ids[]']")
              .map(function(){return $(this).val();}).get();

            var qtys = $("input[name='qty[]']")
              .map(function(){return $(this).val();}).get();

            var did = $("#donner_id").val();
            var delivery = $('#delivery').prop('checked');
            var collection = $('#collection').prop('checked');

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {voucherIds,qtys,did,delivery,collection},

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
            if (type == "Prepaid") {
            var total = amount * qty;
            } else { 
            var total = parseInt('00');
            }
            // var total = amount * qty;
        row.find('.total').val(total.toFixed(2));
        net_total();

	})

    // net total
    function net_total(){
		var total = 0;
		$('.total').each(function(){
			total += ($(this).val()-0);
		})
		$('#net_total').val(total.toFixed(2));

	}


    });

</script>
@endsection
