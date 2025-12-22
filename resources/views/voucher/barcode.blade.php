@extends('layouts.admin')

@section('content')
<div class="dashboard-content py-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div class="d-flex align-items-center">
                <a href="{{route('neworder')}}" class="btn btn-outline-secondary btn-sm me-3">
                    <span class="iconify" data-icon="fluent:arrow-left-24-filled"></span> Back
                </a>
                <h4 class="mb-0 fw-bold text-dark">Voucher Order Details</h4>
            </div>
            <div class="text-end">
                <span class="text-muted small d-block">Order Number</span>
                <span class="fw-bold text-primary">#{{$order->order_id}}</span>
            </div>
        </div>

        <div id="loading" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; flex-direction: column; justify-content: center; align-items: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
            <div class="mt-3 fw-bold text-primary">Processing...</div>
        </div>

        <div class="ermsg"></div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-uppercase small tracking-wider text-muted">Customer & Delivery Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Customer Name</label>
                                <p class="fw-semibold">
                                    @if ($user->profile_type == 'Personal')
                                        {{$user->name}} {{$user->surname}}
                                    @else
                                        {{$user->name}} <br>
                                        <span class="text-muted small">(Donor: {{$user->surname}})</span>
                                    @endif
                                </p>
                                
                                <label class="text-muted small d-block">Email Address</label>
                                <p>{{$user->email}}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Shipping Address</label>
                                <p class="small text-dark">
                                    {{ $user->houseno }} {{ $user->street }}<br>
                                    {{ $user->address_third_line }}<br>
                                    {{ $user->town }}, {{ $user->postcode }}
                                </p>
                                <label class="text-muted small d-block">Delivery Option</label>
                                <span class="badge bg-light text-dark border">{{$order->delivery_option}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-uppercase small tracking-wider text-muted">Order Management</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">Order Date</label>
                            <p class="mb-0 fw-semibold text-dark">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="text-muted small d-block mb-2">Update Order Status</label>
                            <select id="" data-order-id="{{ $order->id }}" class="form-select js-status-change @if($order->status == '3') bg-light @endif" @if($order->status == "3") disabled @endif>
                                <option value="0" @if($order->status == "0") selected @endif>Pending</option>
                                <option value="1" @if($order->status == "1") selected @endif>Complete</option>
                                <option value="3" @if($order->status == "3") selected @endif>Cancel</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('downloadpostage', $order->id) }}" class="btn btn-success shadow-sm">
                                <span class="iconify" data-icon="fluent:print-24-regular"></span> Download Postage Label
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-uppercase small tracking-wider text-muted">Order Items (Vouchers)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Sl</th>
                                <th>Voucher Type</th>
                                <th>Start Barcode</th>
                                <th>Pages</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $n = 1; @endphp
                            @foreach ($orderDtls as $orderDtl)
                            <tr>
                                <td class="ps-4 text-muted">{{$n}}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $orderDtl->voucher->type }}</span>
                                    <div class="small text-muted">
                                        @if ($orderDtl->voucher->type == "Mixed")
                                            (£{{$orderDtl->mixed_value}})
                                        @else
                                            @if($orderDtl->voucher->note)
                                                (£{{$orderDtl->voucher->single_amount}} of {{ $orderDtl->voucher->note }}) 
                                            @endif  
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($orderDtl->startbarcode)
                                        <div class="d-inline-block p-2 bg-white border rounded text-center">
                                            {!! DNS1D::getBarcodeHTML($orderDtl->startbarcode, 'PHARMA') !!}
                                            <span class="d-block small mt-1 fw-bold tracking-tighter">{{$orderDtl->startbarcode}}</span>
                                        </div>
                                    @else
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-outline-primary btn-sm acc" vtype="{{$orderDtl->voucher->type}}" mixedAmount="{{$orderDtl->mixed_value}}" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            + Add Barcode
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if($orderDtl->total_page)
                                        <span class="badge bg-info text-dark">{{ $orderDtl->total_page }} Pages</span>
                                    @else
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-outline-primary btn-sm acc2" data-bs-toggle="modal" vtype="{{$orderDtl->voucher->type}}" mixedAmount="{{$orderDtl->mixed_value}}" data-bs-target="#exampleModal2">
                                            + Set Pages
                                        </button>
                                    @endif
                                </td>
                                <td>{{ $orderDtl->number_voucher}}</td>
                                <td class="fw-semibold text-dark">@if($orderDtl->voucher->type =="Prepaid") £{{ number_format($orderDtl->voucher->amount, 2) }}@endif</td>
                                <td class="fw-bold text-primary">@if($orderDtl->voucher->type =="Prepaid") £{{ number_format($orderDtl->amount, 2)}} @endif</td>
                                <td class="text-end pe-4">
                                    @if($orderDtl->total_page)
                                        <button type="button" order-id="{{$orderDtl->id}}" class="btn btn-link text-danger btn-sm p-0 acc3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                            Cancel Item
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
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add Start Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <div class="mb-3">
                    <label for="startbarcode" class="form-label small text-muted">Scan or Enter Barcode</label>
                    <input type="text" class="form-control form-control-lg" id="startbarcode">
                    <input type="hidden" id="orderhisid">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" id="addStartBtn" class="btn btn-primary px-4">Save Barcode</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Number of Pages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <div class="mb-3">
                    <label for="pages" class="form-label small text-muted">Enter Total Pages</label>
                    <input type="number" class="form-control form-control-lg" id="pages">
                    <input type="hidden" id="orderhisid2">
                    <input type="hidden" value="{{ $user->id }}" id="user_id">
                    <input type="hidden" id="voucherType">
                    <input type="hidden" id="mixedamount">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" id="addEndBtn" class="btn btn-primary px-4">Save Pages</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4">
                <div class="text-danger mb-3">
                    <span class="iconify" data-icon="fluent:warning-24-filled" data-width="48"></span>
                </div>
                <h5 class="fw-bold">Are you sure?</h5>
                <p class="text-muted small">This action will cancel this specific item.</p>
                <input type="hidden" id="orderhisid3">
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">No</button>
                    <button type="button" id="cancelBtn" class="btn btn-danger px-4">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
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

        $("#loading").css('display', 'flex');

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
