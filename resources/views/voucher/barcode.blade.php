@extends('layouts.admin')
@section('content')
<div class="dashboard-content">
    <div class="container">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify svg-loading" data-icon="fluent:contact-card-28-regular"></span>
            <div class="mx-2">Voucher </div>
        </div>
    </section>
    <section class="profile purchase-status">
        <div class="title-section">
            <a href="{{route('neworder')}}"  class="btn btn-info">back</a>
        </div>
    </section>
    
        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
        </div>
        <!-- Image loader -->
        
    <section class="px-4">
        <div class="ermsg"></div>
        <div class="row my-3">
            <div class="col-md-12 mt-2 ">
                <p><b>Order No: {{$order->order_id}} </b></p>
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="text-start text-muted mb-4 px-2">
                        @if ($user->profile_type == 'Personal')
                        <p class="mb-1">Name: {{$user->name}}</p>
                        @else
                        <p class="mb-1">Company Name: {{$user->name}}</p>
                        <p class="mb-1">Donor Name: {{$user->surname}}</p>

                        @endif


                        <p class="mb-1">Email: {{$user->email}}</p>
                        <p class="mb-1">Address: {{ $user->houseno }} {{ $user->street }} {{ $user->address_third_line }} {{ $user->town }} {{ $user->postcode }} </p>
                        <br>
                        <p class="mb-1">Delivery Option: {{$order->delivery_option}}</p>
                    </div>

                    

                    <div class="text-start text-muted mb-4">
                        <p class="mb-1">Order date: {{$order->created_at}}</p>
                        
                        <p class="mb-1 d-flex align-items-center">Status:
                            {{-- <select 
                                class="ms-2 form-control js-status-change" 
                                data-order-id="{{ $order->id }}"
                                @disabled($order->status == \App\Enums\OrderStatus::CANCELLED)
                            >
                                <option value="0" @selected($order->status == 0)>Pending</option>
                                <option value="1" @selected($order->status == 1)>Complete</option>
                                <option value="3" @selected($order->status == 3)>Cancel</option>

                            </select> --}}

                            <select name="" id=""  data-order-id="{{ $order->id }}"  class="ms-2 form-control js-status-change" @if($order->status == "3") disabled @endif>
                                <option value="0" @if($order->status == "0") selected @endif>Pending</option>
                                <option value="1" @if($order->status == "1") selected @endif>Complete</option>
                                <option value="3" @if($order->status == "3") selected @endif>Cancel</option>
                            </select>
                        </p>

                        <p>
                            <a href="{{ route('downloadpostage', $order->id) }}" class="btn btn-success">Download Postage Label</a>
                        </p>

                    </div>

                </div>
                <div class="overflow mt-5">
                    <table class="table table-custom shadow-sm bg-white">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Voucher Type</th>
                                <th>Start Barcode</th>
                                <th>Number of pages</th>
                                <th>Qty</th>
                                <th>Price </th>
                                <th>Total </th>
                                <th>Cancel </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $n = 1; @endphp
                            @foreach ($orderDtls as $orderDtl)
                            
                            <tr>
                                    <td>{{$n}}</td>
                                    <td>{{ $orderDtl->voucher->type }} 
                                        @if ($orderDtl->voucher->type == "Mixed")
                                            (£{{$orderDtl->mixed_value}})
                                        @else
                                            @if($orderDtl->voucher->note)
                                            (£{{$orderDtl->voucher->single_amount}} of {{ $orderDtl->voucher->note }}) @endif  
                                        @endif
                                        

                                    </td>
                                    <td>
                                        @if($orderDtl->startbarcode)
                                        {!! DNS1D::getBarcodeHTML($orderDtl->startbarcode, 'PHARMA') !!}
                                        <br>
                                        <span style="margin: -20px 0px 0px 30px">{{$orderDtl->startbarcode}}</span>
                                        @else
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-primary btn-sm acc" vtype="{{$orderDtl->voucher->type}}" mixedAmount="{{$orderDtl->mixed_value}}" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            add
                                        </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($orderDtl->total_page)
                                        {{ $orderDtl->total_page }}
                                        @else
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-primary btn-sm acc2" data-bs-toggle="modal" vtype="{{$orderDtl->voucher->type}}" mixedAmount="{{$orderDtl->mixed_value}}" data-bs-target="#exampleModal2">
                                            add
                                        </button>
                                        @endif
                                    </td>
                                    <td>{{ $orderDtl->number_voucher}}</td>
                                    <td>@if($orderDtl->voucher->type =="Prepaid") £{{ $orderDtl->voucher->amount }}@endif</td>
                                    <td>@if($orderDtl->voucher->type =="Prepaid") £{{ $orderDtl->amount}} @endif</td>
                                    <td>

                                        @if($orderDtl->total_page)
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-primary btn-sm acc3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                            Cancel
                                        </button>
                                        @endif


                                        
                                    </td>

                            </tr>
                            @if ($orderDtl->voucher->type != "Mixed")
                            @php $n++; @endphp
                            @endif
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
</div>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Start Barcode </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsg"></div>
            <div class="mb-3">
                <label for="startbarcode" class="form-label">Start Barcode</label>
                <input type="text" class="form-control" id="startbarcode">
                <input type="hidden" class="form-control" value="" id="orderhisid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addStartBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Number of pages </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <div class="mb-3">
                    <label for="endbarcode" class="form-label">Pages</label>
                    <input type="text" class="form-control" id="pages">
                    <input type="hidden" class="form-control" value="" id="orderhisid2">
                    <input type="hidden" class="form-control" value="{{ $user->id }}" id="user_id">
                    <input type="hidden" class="form-control" value="" id="voucherType">
                    <input type="hidden" class="form-control" value="" id="mixedamount">
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" id="addEndBtn" class="btn btn-primary">Save</button>
            </div>
          </div>
        </div>
    </div>
      <!-- Modal End -->

      
    <!-- Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <div class="mb-3">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <input type="hidden" class="form-control" value="" id="orderhisid3">
                    <input type="hidden" class="form-control" value="{{ $user->id }}" id="user_id">
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
              <button type="button" id="cancelBtn" class="btn btn-success">Yes</button>
            </div>
          </div>
        </div>
    </div>
      <!-- Modal End -->
@endsection

@section('script')
<script type="text/javascript">

$(document).ready(function() {



    //header for csrf-token is must in laravel
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //

    const STATUS_URL = "{{ route('admin.order.status') }}";

    $('.js-status-change').on('change', function() {
        const $select = $(this);
        const status = $select.val();
        const orderId = $select.data('order-id');

        // Professional touch: Add a confirmation for dangerous actions (like cancelling)
        if (status == 3 && !confirm("Are you sure you want to cancel this order?")) {
            location.reload(); // Reset select if they cancel
            return;
        }

        $("#loading").show();

        $.ajax({
            url: STATUS_URL,
            method: "POST",
            data: { status, orderId },
            success: function (d) {
                    $(".ermsg").html(d.message);
                    window.setTimeout(function(){location.reload()},1000)
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("Something went wrong. Please try again.");
            },
            complete: function() {
                $("#loading").hide();
            }
        });
    });




//add stock to voucher
    $(".acc").click(function(){
        var orderid = $(this).attr("order-id");
        var vtype = $(this).attr("vtype");
        var mixedamount = $(this).attr("mixedamount");
        $('#orderhisid').val(orderid);
        $('#voucherType').val(vtype);
        $('#mixedamount').val(mixedamount);
    });

    $(".acc2").click(function(){
        var orderid = $(this).attr("order-id");
        var vtype = $(this).attr("vtype");
        var mixedamount = $(this).attr("mixedamount");
        // console.log(orderid);
        $('#orderhisid2').val(orderid);
        $('#voucherType').val(vtype);
        $('#mixedamount').val(mixedamount);
    });

    $(".acc3").click(function(){
        var orderid = $(this).attr("order-id");
        $('#orderhisid3').val(orderid);
    });

        //add start barcode
    var starturl = "{{URL::to('/admin/add-start-barcode')}}";
    $("#addStartBtn").click(function(){
        var orderhisid= $("#orderhisid").val();
        var startbarcode = $("#startbarcode").val();
        var user_id = $("#user_id").val();
        var voucherType = $("#voucherType").val();
        var mixedamount = $("#mixedamount").val();
        
        // console.log(orderhisid, voucherType, mixedamount, user_id);

        $.ajax({
            url: starturl,
            method: "POST",
            data: {orderhisid,startbarcode,user_id,voucherType,mixedamount},
            success: function (d) {
                // console.log(d);
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

    });

      //add startbarcode END

        //add number of pages
        var endurl = "{{URL::to('/admin/add-pages')}}";
        $("#addEndBtn").click(function(){
            var orderhisid= $("#orderhisid2").val();
            var pages = $("#pages").val();
            var user_id = $("#user_id").val();
            var voucherType = $("#voucherType").val();
            var mixedamount = $("#mixedamount").val();
                // console.log(orderhisid, voucherType, mixedamount, pages, user_id);
            $.ajax({
                url: endurl,
                method: "POST",
                data: {orderhisid,pages,user_id,voucherType,mixedamount},
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                        location.reload();
                    }
                },
                error: function (d) {
                    console.log(d);
                }
            });
        });
        //add start barcode END

        //cancel of pages
        var cancelurl = "{{URL::to('/admin/cancel-pages')}}";
        $("#cancelBtn").click(function(){
            var orderhisid= $("#orderhisid3").val();
            var user_id = $("#user_id").val();
                console.log(orderhisid);
            $.ajax({
                url: cancelurl,
                method: "POST",
                data: {orderhisid,user_id},
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                        location.reload();
                    }
                },
                error: function (d) {
                    console.log(d);
                }
            });
        });
        //cancel barcode END




});
</script>
@endsection
