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
                        <p class="mb-1 d-flex align-items-center">Status:
                            <select name="" id="" class="ms-2 form-control" @if($order->status == "3") disabled @endif>
                                <option value="0" @if($order->status == "0") selected @endif>Peding</option>
                                <option value="1" @if($order->status == "1") selected @endif>Complete</option>
                                <option value="3" @if($order->status == "3") selected @endif>Cancel</option>
                            </select>
                        </p>

                    </div>

                </div>
                <div class="overflow mt-5">
                    <table class="table table-custom shadow-sm bg-white">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Voucher Type</th>
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
                                <td>£{{ $orderDtl->voucher->amount }} {{ $orderDtl->voucher->type }} @if($orderDtl->voucher->note)({{ $orderDtl->voucher->note }}) @endif</td>
                                <td>{{ $orderDtl->number_voucher}}</td>
                                <td>@if($orderDtl->voucher->type =="Prepaid") £{{ $orderDtl->voucher->amount }}@endif</td>
                                <td>@if($orderDtl->voucher->type =="Prepaid") £{{ $orderDtl->amount}} @endif</td>
                            </tr>
                            @endforeach

                            <tr>
                                <td colspan="3"> </td>
                                <td>
                                    <b> Total : </b>
                                </td>
                                <td> <b>£{{$order->amount}}</b></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
</div>
@endsection

@section('script')
<script type="text/javascript">

$(document).ready(function() {



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

var url = "{{URL::to('/admin/order-status')}}";

$('select').on('change', function() {
    var status =  this.value;
    var orderId = {!! json_encode($order->id) !!};

    $.ajax({
            url: url,
            method: "POST",
            data: {status,orderId},

            success: function (d) {
                if (d.status == 303) {
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    // window.setTimeout(function(){location.reload()},500)
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

  });



});
</script>
@endsection
