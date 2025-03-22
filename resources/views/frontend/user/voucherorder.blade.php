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
                                            <th style="display: none;">Raw Date</th> 
                                            <th>Date</th>
                                            <th>Order Id</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($orders as $key => $order)

                                        <tr>
                                            <td style="display: none;">{{ $order->created_at->format('Y-m-d') }}</td> <!-- Hidden column with correct format -->
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
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
                                            <td>
                                                @if ($order->status == 0)
                                                <a href="{{ route('voucherBookEdit',$order->id) }}" class="btn-theme bg-secondary"> <i class="fa fa-edit"></i>Edit</a>
                                                @endif

                                                <button type="button" class="btn-theme bg-info" data-toggle="modal" data-target="#orderModal{{ $order->id }}">
                                                    <i class="fa fa-eye"></i> View
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">Order Details</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Voucher Type</th>
                                                                            <th>Voucher Amount</th>
                                                                            <th>Quantity</th>
                                                                            <th>Price</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($order->orderhistories as $item)
                                                                        <tr>
                                                                            <td>{{ $item->voucher->type }}</td>
                                                                            <td>£{{ $item->voucher->single_amount }}</td>
                                                                            <td>{{ $item->voucher->note }}</td>
                                                                            <td>£{{ $item->number_voucher }}</td>
                                                                            
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>


                                                                <!-- Add more order details here if needed -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
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

@section('script')
    <script>
        // $('#voucherTable').DataTable();
        $('#voucherTable').DataTable({
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[ 0, "desc" ]], // Order by the first column (Date) in descending order
            "columnDefs" : [{"targets":3, "type":"date-eu"}],
        });
    </script>
@endsection
