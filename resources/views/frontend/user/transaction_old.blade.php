@extends('frontend.layouts.user')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">View All Transactions</div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionAll" type="button" role="tab" aria-controls="nav-all" aria-selected="true">All Transaction</button>
                  <button class="nav-link" id="in-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionIn" type="button" role="tab" aria-controls="nav-in" aria-selected="false">Transaction In</button>
                  <button class="nav-link" id="out-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab" aria-controls="nav-out" aria-selected="false">Transcation Out</button>
                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">


                <div class="tab-pane fade show active" id="nav-transactionAll" role="tabpanel" aria-labelledby="nav-all">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline" action="{{route('user.transaction_search')}}" method ="POST">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="fromDate"><small>Date From </small> </label>
                                                <input class="form-control mr-sm-2" id="fromDate" name="fromDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="toDate"><small>Date To </small> </label>
                                                <input class="form-control mr-sm-2" id="toDate" name="toDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-5 d-flex align-items-center">
                                            <div class="form-group d-flex mt-4">
                                            <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>

                           </div>
                        </div>
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Id</th>
                                            <th>transaction type</th>
                                            <th>Voucher Number</th>
                                            <th>Charity Name</th>
                                            <th>Status</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>

                                <?php
                                    $tbalance = 0;
                                ?>

                                  @foreach ($tamount as $data)
                                        @if($data->commission != 0)
                                            @php
                                            $tbalance = $tbalance - $data->commission;
                                            @endphp
                                        @endif

                                        @php
                                        if($data->t_type == "In"){
                                            if($data->commission != 0){

                                            $tbalance = $tbalance + $data->amount + $data->commission;
                                            }else {

                                            $tbalance = $tbalance + $data->amount;
                                            }

                                        }
                                        @endphp

                                                @php
                                                if($data->t_type == "Out"){
                                                $tbalance = $tbalance - $data->amount;
                                                }
                                                @endphp
                                    @endforeach

                                <tbody>

                                    @foreach ($alltransactions as $data)
                                    @if($data->commission != 0)
                                    <tr>
                                        <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                        <td>{{$data->t_id}} </td>
                                        <td>Commission</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>-£{{$data->commission}}</td>
                                        <td>£{{ number_format($tbalance, 2) }}</td>
                                        @php
                                        $tbalance = $tbalance + $data->commission;
                                        @endphp
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                        <td>{{$data->t_id}} </td>
                                        <td>{{$data->title}} </td>
                                        <td>{{$data->cheque_no}}</td>
                                        <td>@if($data->charity_id){{ $data->charity->name}}@endif</td>
                                        <td>@if($data->pending == "0") Pending @endif</td>

                                            @if($data->t_type == "In")
                                                @if($data->commission != 0)
                                                    <td>£ {{ number_format($data->amount + $data->commission, 2) }} </td>
                                                    <td></td>
                                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                                @else
                                                    <td>£{{number_format($data->amount, 2)}} </td>
                                                    <td></td>
                                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                                @endif
                                            @elseif($data->t_type == "Out")
                                                <td></td>
                                                <td>-£{{number_format($data->amount, 2) }}</td>
                                                 <td> £{{ number_format($tbalance, 2) }} </td>
                                                 @if($data->pending != "0")
                                                 @php  $tbalance = $tbalance + $data->amount;  @endphp
                                                 @endif
                                            @endif

                                    </tr>
                                @endforeach
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Previous Balance</td>
                                    <td>£{{ number_format($tbalance, 2) }}</td>
                                </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade show" id="nav-transactionIn" role="tabpanel" aria-labelledby="nav-in">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline" action="{{route('user.transaction_search')}}" method ="POST">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="fromDate"><small>Date From </small> </label>
                                                <input class="form-control mr-sm-2" id="fromDate" name="fromDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="toDate"><small>Date To </small> </label>
                                                <input class="form-control mr-sm-2" id="toDate" name="toDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-5 d-flex align-items-center">
                                            <div class="form-group d-flex mt-4">
                                            <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>

                           </div>
                        </div>
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Id</th>
                                            <th>Source</th>
                                            <th>Note</th>
                                            <th>Commission</th>
                                            <th>Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($intransactions as $transaction)
                                        <tr>
                                                <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}} </td>
                                                <td>{{ $transaction->t_id }}</td>
                                                <td>{{ $transaction->source}}</td>
                                                <td>{{ $transaction->note}}</td>
                                                <td>£{{($transaction->commission)}}</td>
                                                <td>£{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-out">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="col-md-12 my-3">
                                <div class="container">
                               <div class="row">
                                <div class="col-md-9">
                                    <form class="form-inline" action="{{route('user.transaction_search')}}" method ="POST">
                                        @csrf
                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group my-2">
                                                    <label for="fromDate"><small>Date From </small> </label>
                                                    <input class="form-control mr-sm-2" id="fromDate" name="fromDate" type="date" placeholder="Search" aria-label="Search">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group my-2">
                                                    <label for="toDate"><small>Date To </small> </label>
                                                    <input class="form-control mr-sm-2" id="toDate" name="toDate" type="date" placeholder="Search" aria-label="Search">
                                                </div>
                                            </div>
                                            <div class="col-md-5 d-flex align-items-center">
                                                <div class="form-group d-flex mt-4">
                                                <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                </div>

                               </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="exampleIn">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Id</th>
                                            <th>Charity Name</th>
                                            <th>Voucher Number</th>
                                            <th>Transaction Type</th>
                                            <th>Status </th>
                                            <th>Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($outtransactions as $transaction)
                                        <tr>
                                            <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}} </td>
                                            <td>{{ $transaction->t_id }}</td>
                                            <td>@if($transaction->charity_id){{ $transaction->charity->name}}@endif</td>
                                            <td>{{ $transaction->cheque_no}}</td>
                                            <td>{{ $transaction->title}}</td>
                                            <td>@if($transaction->pending == "0") Pending @endif</td>
                                            <td>£{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


              </div>
        </div>
    </div>
  </section>
</div>
@endsection
