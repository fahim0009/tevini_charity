@extends('layouts.admin')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
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
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionAll" type="button" role="tab" aria-controls="nav-all" aria-selected="true">All Transaction</button>
                  <button class="nav-link" id="transactionOut-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Transaction In</button>
                  <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-transcationIn" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Transcation Out</button>

                  
                  <button class="nav-link" id="nav-giftAid-tab" data-bs-toggle="tab" data-bs-target="#nav-giftAid" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Gift Aid</button>

                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-transactionAll" role="tabpanel" aria-labelledby="nav-all">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline" action="{{route('search.donortran', $donor_id)}}" method ="POST">
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
                                            <th>Note</th>
                                            <th>Donate By</th>
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
                                    @foreach ($report as $data)
                                        @if($data->commission != 0)
                                        <tr>
                                            <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                            <td>{{$data->t_id}} </td>
                                            <td>Commission</td>
                                            <td></td>
                                            <td></td>
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
                                            <td>
                                                @if ($data->cheque_no)
                                                    <!-- Modal -->
                                                    <button type="button" class="text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1 d-block text-center" data-bs-toggle="modal" data-bs-target="#chequeModal{{$data->id}}">
                                                        {{$data->cheque_no}} 
                                                    </button>
                                                    <div class="modal fade" id="chequeModal{{$data->id}}" tabindex="-1" aria-labelledby="chequeModalLabel{{$data->id}}" aria-hidden="true">
                                                      <div class="modal-dialog">
                                                        <div class="modal-content">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title" id="chequeModalLabel{{$data->id}}">Cheque Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                          </div>
                                                          <div class="modal-body">
                                                            <p><strong>Cheque Number:</strong> {{$data->cheque_no}}</p>
                                                            @if($data->barcode_image)
                                                              <img src="{{ asset($data->barcode_image) }}" alt="Cheque Image" class="img-fluid">
                                                            @else
                                                              <p>No cheque image available.</p>
                                                            @endif
                                                          </div>
                                                          <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                @endif
                                                
                                                @if($data->crdAcptID){{ $data->crdAcptID}}@endif

                                            </td>
                                            <td>@if($data->charity_id){{ $data->charity->name}}@endif
                                                @if($data->crdAcptID){{ $data->crdAcptLoc}}@endif
                                            </td>
                                            <td>{{$data->note}}</td>
                                            <td>{{$data->donation_by}}</td>
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

                <div class="tab-pane fade show" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline" action="{{route('search.donortran', $donor_id)}}" method ="POST">
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
                                        <input type="hidden" name="donor_id" id="donor_id" value="{{$user->id}}">
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
                                                <td><span style="display:none;">{{ $transaction->id }}</span>{{Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                                <td>{{ $transaction->t_id }}</td>
                                                <td>{{ $transaction->source}}</td>
                                                <td>{{ $transaction->note}}</td>
                                                <td>£{{$transaction->commission}}</td>
                                                <td>£{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="nav-transcationIn" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="col-md-12 my-3">
                                <div class="container">
                               <div class="row">
                                <div class="col-md-9">
                                    <form class="form-inline" action="{{route('search.donortran', $donor_id)}}" method ="POST">
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
                                            <input type="hidden" name="donor_id" id="donor_id" value="{{$user->id}}">
                                            <div class="col-md-5 d-flex align-items-center">
                                                <div class="form-group d-flex mt-4">
                                                <button class="text-white btn-theme ml-1" name="search" title="Search" type="submit">Search</button>
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
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($outtransactions as $transaction)
                                        <tr>
                                            <td><span style="display:none;">{{ $transaction->id }}</span>{{Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $transaction->t_id }}</td>
                                            <td>@if($transaction->charity_id){{ $transaction->charity->name}}@endif</td>
                                            <td>{{ $transaction->cheque_no}}</td>
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
                
                <div class="tab-pane fade" id="nav-giftAid" role="tabpanel" aria-labelledby="nav-giftAid-tab">
                    <div class="row my-2">
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="exampleIn">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Id</th>
                                            <th>Donor Name</th>
                                            <th>Source</th>
                                            <th>Gift Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $giftAid = \App\Models\Usertransaction::where('user_id', $donor_id)->whereNotNull('gift')->orderby('id', 'DESC')->where('status', 1)->get();
                                        @endphp
                                        @foreach ($giftAid as $gift)
                                        <tr>
                                            <td><span style="display:none;">{{ $gift->id }}</span>{{Carbon::parse($gift->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $gift->t_id }}</td>
                                            <td>{{$gift->user->name ?? ""}}</td>
                                            <td>{{ $gift->source}}</td>
                                            <td>£{{ $gift->amount}}</td>
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
