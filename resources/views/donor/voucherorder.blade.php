@extends('layouts.admin')
@section('content')
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
                                <span>£{{ $voucher->amount }} </span>
                                <input type="hidden" value="{{$voucher->id}}" name="v_ids[]">
                                @if ($voucher->type == 'Prepaid')
                                    <div class="badge rounded-pill bg-secondary">{{ $voucher->type }}</div>
                                    <span class="h6">(@if($voucher->note){{$voucher->note}}@endif)</span>
                                @else
                                    <div class="badge rounded-pill bg-info">{{ $voucher->type }}</div>
                                @endif
                            </div>
                            <div class="items">
                                <div class="cart mx-auto">
                                    <button id="dec{{$voucher->id}}" onclick="dec({{$voucher->id}},{{ $voucher->amount }})">-</button>
                                    <input type="text" name="qty[]" id="cartValue{{$voucher->id}}" value="0">
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
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //
        var url = "{{URL::to('/admin/addvoucher')}}";

        $("#addvoucher").click(function(){

            $("#loading").show();

            var voucherIds = $("input[name='v_ids[]']")
              .map(function(){return $(this).val();}).get();

            var qtys = $("input[name='qty[]']")
              .map(function(){return $(this).val();}).get();

            var did = $("#donner_id").val();
            var delivery_charge = 0;

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {voucherIds:voucherIds,qtys:qtys,did:did,delivery_charge:delivery_charge},

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

    });
</script>
@endsection

