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
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                    <button type="button" class="close text-right" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (isset($success))
                <div class="alert alert-success">
                    {{ $success }}
                    <button type="button" class="close text-right float-right" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


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


@if (isset($chkVoucher) && $chkVoucher->count() > 0)
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
                                <th>Status </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($chkVoucher as $transaction)
                            @php
                                $tranId = \App\Models\Usertransaction::where('cheque_no', $transaction->cheque_no)->first();
                            @endphp
                           
                            <tr>
                                    <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                    <td>
                                        @if ($tranId)
                                        {{ $tranId->t_id }}
                                        @endif
                                    </td>
                                    <td>
                                        
                                        @if ($transaction->charity_id)
                                        {{ $transaction->charity->name}}
                                        @endif



                                    </td>
                                    <td>{{ $transaction->user->name}}</td>
                                    <td>£{{ $transaction->amount}}</td>
                                    
                                    <td>
                                        @if($transaction->status == "0") Pending @endif
                                        @if($transaction->status == "1") Complete @endif
                                        @if($transaction->status == "3") Cancel @endif
                                    </td>
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
