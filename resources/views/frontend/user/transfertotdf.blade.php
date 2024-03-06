@extends('frontend.layouts.user')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
             <div class="mx-2">
               Transfer to TDF
            </div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Issue Date</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Current dollar amount</th>
                                            <th>Payment dollar amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($data as $data)

                                        <tr>
                                            <td>{{ Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $data->payment_date}} </td>
                                            <td>£{{ $data->tdf_amount}}</td>
                                            <td>£{{ $data->current_dollar_amount}}</td>
                                            <td>£{{ $data->payment_dollar_amount}}</td>
                                            <td>@if($data->status =="0")
                                                Pending
                                                @elseif($data->status =="1")
                                                Complete
                                                @elseif($data->status =="3")
                                                Cancel
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center"> <p>No order found</p> </td>
                                        </tr>
                                        @endforelse


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
  </section>
</div>
@endsection
