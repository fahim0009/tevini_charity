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
               Order List
            </div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        {{-- <div class="col-md-12 my-3">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline">
                                    <div class="row">
                                        <div class="col-md-5 d-flex align-items-center">
                                            <div class="form-group d-flex mt-4">
                                                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                                                <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                              </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for=""><small>Date From </small> </label>
                                                <input class="form-control mr-sm-2" type="date" placeholder="Search" aria-label="Search">
                                              </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for=""><small>Date To </small> </label>
                                                <input class="form-control mr-sm-2" type="date" placeholder="Search" aria-label="Search">
                                              </div>
                                        </div>
                                    </div>
                                  </form>
                            </div>
                            <div class="col-md-3 d-flex align-items-center justify-content-center">
                                <div>
                                    <button title="Download" class="my-2 btn btn-sm btn-info text-white">Download PDF</button>
                                    <button title="Download" class="my-2 btn btn-sm btn-secondary">Download excel</button>
                                </div>
                            </div>
                           </div>
                        </div> --}}
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="voucherTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Order Id</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <!--<th>Action</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($orders as $key => $order)

                                        <tr>
                                            <td>{{ Carbon::parse($order->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $order->order_id}} </td>
                                            <td>£{{ $order->amount}}</td>
                                            <td>@if($order->status =="0")
                                                Pending
                                                @elseif($order->status =="1")
                                                Complete
                                                @elseif($order->status =="3")
                                                Cancel
                                                @endif
                                            </td>
                                            <!--<td><a href="{{ route('singleorder',$order->id) }}"> <i class="fa fa-eye"></i></a></td>-->
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

@section('script')
    <script>
        // $('#voucherTable').DataTable();
        $('#voucherTable').DataTable({
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[ 0, "desc" ]], //or asc 
            "columnDefs" : [{"targets":3, "type":"date-eu"}],
        });
    </script>
@endsection
