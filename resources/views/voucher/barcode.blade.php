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
    <section class="px-4">
        <div class="ermsg"></div>
        <div class="row my-3">
            <div class="col-md-12 mt-2 ">
                <p><b>Order No: {{$order->order_id}} </b></p>
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="text-start text-muted mb-4 px-2">
                        <p class="mb-1">Name: {{$user->name}}</p>
                        <p class="mb-1">Email: {{$user->email}}</p>
                        <p class="mb-1">Address: {{$user->address}}</p>
                        <br>
                        <p class="mb-1">Delivery Option: {{$order->delivery_option}}</p>
                    </div>
                    <div class="text-start text-muted mb-4">
                        <p class="mb-1">Order date: {{$order->created_at}}</p>
                        <p>Status:
                              @if($order->status == "0")
                            Pending
                            @elseif ($order->status == "1")
                            Complete
                            @else
                            Cancel
                            @endif
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
                            </tr>
                        </thead>
                        <tbody>
                            @php $n = 0; @endphp
                            @foreach ($orderDtls as $orderDtl)
                                @php $n++; @endphp
                            <tr>
                                    <td>{{$n}}</td>
                                    <td>{{ $orderDtl->voucher->type }} @if($orderDtl->voucher->note)(£{{$orderDtl->voucher->single_amount}} of {{ $orderDtl->voucher->note }}) @endif</td>
                                    <td>
                                        @if($orderDtl->startbarcode)
                                        {!! DNS1D::getBarcodeHTML($orderDtl->startbarcode, 'PHARMA') !!}
                                        <br>
                                        <span style="margin: -20px 0px 0px 30px">{{$orderDtl->startbarcode}}</span>
                                        @else
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-primary btn-sm acc" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            add
                                        </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($orderDtl->total_page)
                                        {{ $orderDtl->total_page }}
                                        @else
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-primary btn-sm acc2" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                            add
                                        </button>
                                        @endif
                                    </td>
                                    <td>{{ $orderDtl->number_voucher}}</td>
                                    <td>@if($orderDtl->voucher->type =="Prepaid") £{{ $orderDtl->voucher->amount }}@endif</td>
                                    <td>@if($orderDtl->voucher->type =="Prepaid") £{{ $orderDtl->amount}} @endif</td>

                            </tr>
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
@endsection

@section('script')
<script type="text/javascript">

$(document).ready(function() {



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//
//add stock to voucher
    $(".acc").click(function(){
        var orderid = $(this).attr("order-id");
        $('#orderhisid').val(orderid);
    });

    $(".acc2").click(function(){
        var orderid = $(this).attr("order-id");
        $('#orderhisid2').val(orderid);
    });

        //add start barcode
        var starturl = "{{URL::to('/admin/add-start-barcode')}}";
        $("#addStartBtn").click(function(){
        var orderhisid= $("#orderhisid").val();
        var startbarcode = $("#startbarcode").val();
        var user_id = $("#user_id").val();
        console.log(user_id);
        $.ajax({
            url: starturl,
            method: "POST",
            data: {orderhisid,startbarcode},
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

      //add startbarcode END

        //add number of pages
        var endurl = "{{URL::to('/admin/add-pages')}}";
        $("#addEndBtn").click(function(){
        var orderhisid= $("#orderhisid2").val();
        var pages = $("#pages").val();
        var user_id = $("#user_id").val();
            // console.log(user_id);
        $.ajax({
            url: endurl,
            method: "POST",
            data: {orderhisid,pages,user_id},
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
});
</script>
@endsection
