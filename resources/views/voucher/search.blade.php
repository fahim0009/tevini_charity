@extends('layouts.admin')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Voucher Search</div>
        </div>
    </section>


<section class="card m-3">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            <div class="stsermsg"></div>
            <div class="col-md-12">
                <form class="form-inline" action="{{route('voucherSearch')}}" method="POST">
                    @csrf         

                    <div class="row justify-content-center">

                        <div class="col-md-4">
                            <div class="form-group my-2">
                                <label for="voucher_number"><small>Voucher Number</small> </label>
                                <input class="form-control mr-sm-2" id="voucher_number" name="voucher_number" type="number" placeholder="Search" value="{{$vnumber ?? ''}}" aria-label="Search">
                              </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-group d-flex mt-4">
                              <button class="text-white btn-theme ml-1" type="submit">Search</button>
                            </div>
                        </div>

                    </div>


                </form>
            </div>
            
        </div>
    </div>
</section>


@if ($chkVoucher->count() > 0)
<section class="card m-3">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            <div class="stsermsg"></div>
            
            <div class="col-md-12 mt-2 text-center">
                <div class="overflow">
                    <table class="table table-custom shadow-sm bg-white" id="example">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transaction Id</th>
                                <th>Charity Name</th>
                                <th>Donor Name</th>
                                <th>Amount </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($chkVoucher as $transaction)
                            <tr>
                                    <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                    <td>{{ $transaction->t_id }}</td>
                                    <td>
                                        
                                        @if ($transaction->charity_id)
                                        {{ $transaction->charity->name}}
                                        @endif

                                        @if ($transaction->crdAcptID)
                                        {{ $transaction->crdAcptLoc}}
                                        @endif


                                    </td>
                                    <td>{{ $transaction->user->name}}</td>
                                    <td>£{{ $transaction->amount}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</section>  
@endif



</div>
@endsection

@section('script')
<script>
    
</script>
@endsection
