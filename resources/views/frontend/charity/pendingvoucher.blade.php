@extends('frontend.layouts.charity')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">View All Pending Voucher</div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            
              
            <div class="row my-2">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Cheque No</th>
                                    <th>Note</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($cvouchers as $voucher)

                                <tr>
                                        <td>{{ $voucher->created_at->format('d/m/Y')}} </td>
                                        <td>{{ $voucher->user->name }}</td>
                                        <td>{{ $voucher->cheque_no}}</td>
                                        <td>{{ $voucher->note}}</td>
                                        <td>£{{ $voucher->amount}}</td>
                                        <td>
                                        @if($voucher->status == "0") Pending @endif
                                        @if($voucher->status == "1") Complete @endif
                                        @if($voucher->status == "3") Cancel @endif
                                        </td>

                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
  </section>
</div>
@endsection
