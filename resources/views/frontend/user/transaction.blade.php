@extends('frontend.layouts.user')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<!-- content area -->
<div class="content">
<div class="row">
    <div class="col-lg-12">
        <div class="pagetitle pb-2">
            Transactions
        </div>
    </div>
</div>
<div class="row ">
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
                        <button class="btn-theme bg-primary" type="submit">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>

       </div>
    </div>
    <div class="col-lg-12">
        <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="transaction-tab" data-bs-toggle="tab"
                    data-bs-target="#transaction" type="button" role="tab" aria-controls="transaction"
                    aria-selected="true">All transaction</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="moneyIn-tab" data-bs-toggle="tab" data-bs-target="#moneyIn"
                    type="button" role="tab" aria-controls="moneyIn" aria-selected="false">Money
                    in</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="moneyOut-tab" data-bs-toggle="tab"
                    data-bs-target="#moneyOut" type="button" role="tab" aria-controls="moneyOut"
                    aria-selected="false">Money out</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                    type="button" role="tab" aria-controls="pending" aria-selected="false">Pending
                    transaction</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gift-tab" data-bs-toggle="tab" data-bs-target="#gift"
                    type="button" role="tab" aria-controls="gift" aria-selected="false">Gift Aid</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="transaction" role="tabpanel"
                aria-labelledby="transaction-tab">
                <div class="data-container">
                      <table class="table table-theme mt-4" id="">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Description</th>
                            <th scope="col">Donate By</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Comments</th>
                            <th scope="col">Reference/Voucher no.</th>
                            <th scope="col">Balance</th>
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
                            <td class="fs-16 txt-secondary">{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                            <td>Commission</td>
                            <td></td>
                            <td>-£{{$data->commission}}</td>
                            <td></td>
                            <td>{{$data->t_id}}</td>
                            <td>£{{ number_format($tbalance, 2) }}</td>
                            @php
                            $tbalance = $tbalance + $data->commission;
                            @endphp
                        </tr>
                        @endif
                        <tr>
                            <td class="fs-16 txt-secondary">{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fs-20 txt-secondary fw-bold">@if($data->charity_id){{ $data->charity->name}}@endif 
                                        @if($data->crdAcptID){{ $data->crdAcptLoc}}@endif
                                    </span>
                                    <span class="fs-16 txt-secondary">
                                        {{$data->title}} 
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#tranModal{{$data->id}}" style="margin-left: 5px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#18988B" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                                                <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
                                            </svg>
                                        </a>
                                    </span>

                                    <!-- Modal -->
                                    <div class="modal fade" id="tranModal{{$data->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                        <td id="t_date">{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Transaction ID</td>
                                                        <td>:</td>
                                                        <td id="t_id">{{$data->t_id}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Transaction Type</td>
                                                        <td>:</td>
                                                        <td id="t_donation_type">{{$data->title}}</td>
                                                    </tr>
                                                    @if($data->charity_id)
                                                    <tr>
                                                        <td>Charity Name</td>
                                                        <td>:</td>
                                                        <td id="t_charity">{{ $data->charity->name}}</td>
                                                    </tr>
                                                    @endif
                                                    @if ($data->donation_by)
                                                    <tr>
                                                        <td>Donate By</td>
                                                        <td>:</td>
                                                        <td id="t_donate_by">{{$data->donation_by}}</td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <td>Amount</td>
                                                        <td>:</td>
                                                        <td id="t_amount">£{{number_format($data->amount, 2) }}</td>
                                                    </tr>
                                                    @if ($data->note)
                                                    <tr>
                                                        <td>Comment</td>
                                                        <td>:</td>
                                                        <td id="t_comment">{{$data->note}}</td>
                                                    </tr>
                                                    @endif

                                                    @if ($data->barcode_image)
                                                    <tr>
                                                        <td colspan="3">
                                                            <img src="{{ asset($data->barcode_image) }}" alt="Barcode Image" class="img-fluid">

                                                        </td>
                                                    </tr>
                                                        
                                                    @endif
                                                </table>

                                            </div>
                                        </div>
                                        </div>
                                    </div>



                                </div>
                            </td>
                            <td class="fs-16 txt-secondary">{{$data->donation_by}}</td>
                                @if($data->t_type == "In")
                                    @if($data->commission != 0)
                                        <td>£{{ number_format($data->amount + $data->commission, 2) }} </td>
                                        <td></td>
                                        <td class="fs-16 txt-secondary">
                                            @if ($data->title == "Voucher")
                                            {{$data->cheque_no}}
                                            @else
                                            {{$data->t_id}}
                                            @endif
                                        </td>
                                        <td> £{{ number_format($tbalance, 2) }} </td>
                                        @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                    @else
                                    <td class="fs-16 txt-secondary">
                                            £{{number_format($data->amount, 2)}}
                                            <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.0527 5.89619C9.96315 5.98283 9.84339 6.03126 9.71876 6.03126C9.59413 6.03126 9.47438 5.98283 9.38478 5.89619L5.96876 2.47432V11.656C5.96876 11.7803 5.91938 11.8995 5.83147 11.9874C5.74356 12.0753 5.62433 12.1247 5.50001 12.1247C5.37569 12.1247 5.25646 12.0753 5.16856 11.9874C5.08065 11.8995 5.03126 11.7803 5.03126 11.656V2.47432L1.61525 5.89619C1.52417 5.97094 1.40855 6.00914 1.29087 6.00336C1.17319 5.99758 1.06186 5.94823 0.978549 5.86492C0.895236 5.78161 0.84589 5.67028 0.84011 5.5526C0.834331 5.43492 0.87253 5.3193 0.947278 5.22822L5.16603 1.00947C5.2549 0.92145 5.37493 0.87207 5.50001 0.87207C5.6251 0.87207 5.74512 0.92145 5.834 1.00947L10.0527 5.22822C10.1408 5.31709 10.1901 5.43712 10.1901 5.56221C10.1901 5.68729 10.1408 5.80732 10.0527 5.89619Z" fill="#18988B"></path>
                                                </svg>
                                        </td>
                                        <td>{{$data->note}}</td>
                                        <td class="fs-16 txt-secondary">
                                            @if ($data->title == "Voucher")
                                            {{$data->cheque_no}}
                                            @else
                                            {{$data->t_id}}
                                            @endif
                                        </td>
                                        <td> £{{ number_format($tbalance, 2) }} </td>
                                        @php $tbalance = $tbalance - $data->amount; @endphp
                                    @endif
                                @elseif($data->t_type == "Out")
                                <td class="fs-16 txt-secondary">
                                    -£{{number_format($data->amount, 2) }}
                                    <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.0527 7.18393C9.96315 7.08574 9.84339 7.03085 9.71876 7.03085C9.59413 7.03085 9.47438 7.08574 9.38478 7.18393L5.96876 11.0621V0.656192C5.96876 0.515295 5.91938 0.380169 5.83147 0.28054C5.74356 0.180912 5.62433 0.124942 5.50001 0.124942C5.37569 0.124942 5.25646 0.180912 5.16856 0.28054C5.08065 0.380169 5.03126 0.515295 5.03126 0.656192V11.0621L1.61525 7.18393C1.52417 7.09921 1.40855 7.05592 1.29087 7.06247C1.17319 7.06902 1.06186 7.12494 0.978549 7.21937C0.895236 7.31379 0.84589 7.43995 0.84011 7.57333C0.834331 7.7067 0.87253 7.83774 0.947278 7.94096L5.16603 12.7222C5.2549 12.822 5.37493 12.8779 5.50001 12.8779C5.6251 12.8779 5.74512 12.822 5.834 12.7222L10.0527 7.94096C10.1408 7.84024 10.1901 7.7042 10.1901 7.56244C10.1901 7.42068 10.1408 7.28465 10.0527 7.18393Z" fill="#003057"/>
                                    </svg>
                                </td>
                                    <td>{{$data->note}}</td>
                                    <td class="fs-16 txt-secondary">
                                        @if ($data->title == "Voucher")
                                        {{$data->cheque_no}}
                                        @else
                                        {{$data->t_id}}
                                        @endif
                                    </td>
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
                        <td>Previous Balance</td>
                        <td>£{{ number_format($tbalance, 2) }}</td>
                    </tr>
                    </tbody>
                </table>
                </div>

            </div>
            {{-- transaction In  --}}
            <div class="tab-pane fade" id="moneyIn" role="tabpanel" aria-labelledby="moneyIn-tab">
                    {{-- transaction in  --}}
                    <div class="data-container">
                        <table class="table table-theme mt-4"  id="exampleIn">
                              <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Comments</th>
                                    <th>Reference/Voucher No</th>
                                    <th>Amount</th>

                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($intransactions as $transaction)
                                <tr>
                                    <td class="fs-16 txt-secondary">{{ Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fs-20 txt-secondary fw-bold">@if($transaction->charity_id){{ $transaction->charity->name}}@endif</span>
                                            <span class="fs-16 txt-secondary">{{$transaction->title}}</span>
                                        </div>
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        {{$transaction->note}}
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        @if ($transaction->title == "Voucher")
                                                {{$transaction->cheque_no}}
                                                @else
                                                {{$transaction->t_id}}
                                                @endif
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        £{{number_format($transaction->amount, 2)}}
                                    </td>
                                </tr>
                                @endforeach


                              </tbody>
                          </table>
                  </div>
            </div>

            {{-- Transaction out  --}}
            <div class="tab-pane fade" id="moneyOut" role="tabpanel" aria-labelledby="moneyOut-tab">
                <div class="data-container">
                    <table class="table table-theme mt-4" id="exampleOut">
                          <thead>
                              <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Comments</th>
                                <th>Reference/Voucher No</th>
                                <th>Amount</th>
                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($outtransactions as $transaction)
                            <tr>
                                <td class="fs-16 txt-secondary">{{ Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fs-20 txt-secondary fw-bold">@if($transaction->charity_id){{ $transaction->charity->name}}@endif</span>
                                        <span class="fs-16 txt-secondary">{{$transaction->title}}</span>
                                    </div>
                                </td>
                                <td class="fs-16 txt-secondary">
                                    {{$transaction->note}}
                                </td>
                                <td class="fs-16 txt-secondary">
                                    @if ($transaction->title == "Voucher")
                                            {{$transaction->cheque_no}}
                                            @else
                                            {{$transaction->t_id}}
                                            @endif
                                </td>
                                <td class="fs-16 txt-secondary">
                                    £{{number_format($transaction->amount, 2)}}
                                </td>
                            </tr>
                            @endforeach
                          </tbody>
                      </table>
              </div>

            </div>

            {{-- Pending Transaction  --}}
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">

                <div class="data-container">
                    <table class="table table-theme mt-4" id="example">
                          <thead>
                              <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Comments</th>
                                <th>Reference/Voucher No</th>
                                <th>Amount</th>

                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($pending_transactions as $transaction)

                                    <tr>
                                        <td class="fs-16 txt-secondary">{{ Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fs-20 txt-secondary fw-bold">@if($transaction->charity_id){{ $transaction->charity->name}}@endif
                                                @if(isset($transaction->crdAcptID)){{ $transaction->crdAcptLoc}}@endif
                                                </span>
                                                <span class="fs-16 txt-secondary">{{$transaction->title}}</span>
                                            </div>
                                        </td>
                                        <td class="fs-16 txt-secondary">
                                            {{$transaction->note}}
                                        </td>
                                        <td class="fs-16 txt-secondary">
                                            @if ($transaction->title == "Voucher")
                                            {{$transaction->cheque_no}}
                                            @else
                                            {{$transaction->t_id}}
                                            @endif
                                        </td>
                                        <td class="fs-16 txt-secondary">
                                            £{{number_format($transaction->amount, 2)}}
                                        </td>
                                    </tr>


                            @endforeach


                          </tbody>
                      </table>
              </div>




            </div>

            
            {{-- gift Transaction  --}}
            <div class="tab-pane fade" id="gift" role="tabpanel" aria-labelledby="gift-tab">

                <div class="data-container">
                    <table class="table table-theme mt-4" id="example3">
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
                                $giftAid = \App\Models\Usertransaction::where('user_id', Auth::user()->id)->whereNotNull('gift')->orderby('id', 'DESC')->get();
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



@endsection
