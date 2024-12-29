@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')
          
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
                                                <th>Date</th>
                                                <th>Order Id</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    
                                            @forelse ($orders as $order)
    
                                            <tr>
                                                <td>{{ $order->created_at}} </td>
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
                                                    <a href="{{ route('donor.vorderEdit',$order->id) }}"> <i class="fa fa-edit"></i></a>
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
