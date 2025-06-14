@extends('frontend.layouts.charity')
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
                  <button class="nav-link active" id="transactionOut-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Transaction In</button>
                  <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-transcationIn" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Transcation Out</button>

                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                        type="button" role="tab" aria-controls="pending" aria-selected="false">Pending
                        transaction</button>

                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline" action="{{route('tran_charity_dashboard_search')}}" method ="POST">
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
                                            <th>Donor</th>
                                            <th>Transaction Id</th>
                                            <th>Transaction Type</th>
                                            <th>Voucher Number</th>
                                            <th>Notes</th>
                                            <th>Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($intransactions as $transaction)
                                        <tr>
                                                <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                                <td>{{ $transaction->user->name }}</td>
                                                <td>{{ $transaction->t_id }}</td>
                                                <td>{{ $transaction->title}}
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#tranModal{{$transaction->id}}" style="margin-left: 5px;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#18988B" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                                                            <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="tranModal{{$transaction->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                        <div class="modal-content" style="background-color: #fdf3ee;">
                                                            <div class="modal-header">
                                                            <h1 class="modal-title fs-5 txt-secondary" id="exampleModalLabel">Transaction</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                            
                                                                <table class="table table-borderless">
                                                                    <tr>
                                                                        <td>Date</td>
                                                                        <td>:</td>
                                                                        <td id="t_date">{{ Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Transaction ID</td>
                                                                        <td>:</td>
                                                                        <td id="t_id">{{$transaction->t_id}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Transaction Type</td>
                                                                        <td>:</td>
                                                                        <td id="t_donation_type">{{$transaction->title}}</td>
                                                                    </tr>
                                                                    @if($transaction->charity_id)
                                                                    <tr>
                                                                        <td>Charity Name</td>
                                                                        <td>:</td>
                                                                        <td id="t_charity">{{ $transaction->charity->name}}</td>
                                                                    </tr>
                                                                    @endif
                                                                    @if ($transaction->donation_by)
                                                                    <tr>
                                                                        <td>Donate By</td>
                                                                        <td>:</td>
                                                                        <td id="t_donate_by">{{$transaction->donation_by}}</td>
                                                                    </tr>
                                                                    @endif
                                                                    <tr>
                                                                        <td>Amount</td>
                                                                        <td>:</td>
                                                                        <td id="t_amount">£{{number_format($transaction->amount, 2) }}</td>
                                                                    </tr>
                                                                    @if ($transaction->note)
                                                                    <tr>
                                                                        <td>Comment</td>
                                                                        <td>:</td>
                                                                        <td id="t_comment">{{$transaction->note}}</td>
                                                                    </tr>
                                                                    @endif

                                                                    @if ($transaction->standing_donationdetails_id)
                                                                       @if ($transaction->standingdonationDetail->StandingDonation->charitynote)
                                                                        <tr>
                                                                            <td>Charity Note</td>
                                                                            <td>:</td>
                                                                            <td>{{$transaction->standingdonationDetail->StandingDonation->charitynote}}</td>
                                                                        </tr>
                                                                        @endif 
                                                                    @endif
                                                                    

                                                                    @if ($transaction->barcode_image)
                                                                    <tr>
                                                                        <td colspan="3">
                                                                            <img src="{{ asset($transaction->barcode_image) }}" alt="Barcode Image" class="img-fluid">

                                                                        </td>
                                                                    </tr>
                                                                        
                                                                    @endif
                                                                </table>

                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $transaction->cheque_no}}</td>
                                                <td>{{ $transaction->note}} <br>
                                                    @if ($transaction->standing_donationdetails_id)
                                                    @if ($transaction->standingdonationDetail->StandingDonation->charitynote)
                                                        
                                                        <small><strong>Charity Note:</strong></small> {{ $transaction->standingdonationDetail->StandingDonation->charitynote ?? "" }}<br>
                                                    @endif
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- transaction in  --}}
                <div class="tab-pane fade" id="nav-transcationIn" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="col-md-12 my-3">
                                <div class="container">
                               <div class="row">
                                <div class="col-md-9">

                                <form class="form-inline" action="{{route('tran_charity_dashboard_search')}}" method ="POST">
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
                                            <th>Note</th>
                                            <th>Source</th>
                                            <th>Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($outtransactions as $transaction)
                                        <tr>
                                            <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $transaction->t_id }}</td>
                                            <td>@if($transaction->charity_id){{ $transaction->charity->name}}@endif</td>
                                            <td>{{ $transaction->title }}</td>
                                            <td>{{ $transaction->name}}</td>
                                            <td>{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

        
            {{-- Pending Transaction  --}}
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">

                <div class="data-container">
                    <table class="table table-theme mt-4" id="exampleOut">
                          <thead>
                              <tr>
                                <th>Date</th>
                                <th>Donor</th>
                                <th>Transaction Id</th>
                                <th>Reference/Voucher No</th>
                                <th>Amount</th>
                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($pending_transactions as $transaction)

                                    <tr>
                                        <td>{{ Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td>{{ $transaction->t_id }}</td>
                                        <td>
                                            @if ($transaction->title == "Voucher")
                                            {{$transaction->cheque_no}}
                                            @else
                                            {{$transaction->t_id}}
                                            @endif
                                        </td>
                                        <td>
                                            £{{number_format($transaction->amount, 2)}}
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
