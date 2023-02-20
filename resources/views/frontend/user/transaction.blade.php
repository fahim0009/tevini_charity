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
                My transactions
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-12">
            <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="transaction-tab" data-bs-toggle="tab"
                        data-bs-target="#transaction" type="button" role="tab" aria-controls="transaction"
                        aria-selected="true">All transaction</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="moneyIn-tab" data-bs-toggle="tab" data-bs-target="#moneyIn"
                        type="button" role="tab" aria-controls="moneyIn" aria-selected="false">Transaction
                        in</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="moneyOut-tab" data-bs-toggle="tab"
                        data-bs-target="#moneyOut" type="button" role="tab" aria-controls="moneyOut"
                        aria-selected="false">Transaction out</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                        type="button" role="tab" aria-controls="pending" aria-selected="false">Pending
                        transaction</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="transaction" role="tabpanel" aria-labelledby="transaction-tab">
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


                    <div class="data-container">
                          <table class="table table-theme mt-4" id="exampleAll">
                                <thead>
                                    <tr> 
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Comments</th>
                                        <th>Reference/Voucher No</th>
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
                                            <td>Commission</td>
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
                                                <span class="fs-20 txt-secondary fw-bold">@if($data->charity_id){{ $data->charity->name}}@endif</span>
                                                <span class="fs-16 txt-secondary">{{$data->title}}</span>
                                            </div>
                                        </td>

                                        @if($data->t_type == "In")
                                                @if($data->commission != 0)

                                                    <td class="fs-16 txt-secondary">
                                                        £ {{ number_format($data->amount + $data->commission, 2) }}
                                                        <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.0527 5.89619C9.96315 5.98283 9.84339 6.03126 9.71876 6.03126C9.59413 6.03126 9.47438 5.98283 9.38478 5.89619L5.96876 2.47432V11.656C5.96876 11.7803 5.91938 11.8995 5.83147 11.9874C5.74356 12.0753 5.62433 12.1247 5.50001 12.1247C5.37569 12.1247 5.25646 12.0753 5.16856 11.9874C5.08065 11.8995 5.03126 11.7803 5.03126 11.656V2.47432L1.61525 5.89619C1.52417 5.97094 1.40855 6.00914 1.29087 6.00336C1.17319 5.99758 1.06186 5.94823 0.978549 5.86492C0.895236 5.78161 0.84589 5.67028 0.84011 5.5526C0.834331 5.43492 0.87253 5.3193 0.947278 5.22822L5.16603 1.00947C5.2549 0.92145 5.37493 0.87207 5.50001 0.87207C5.6251 0.87207 5.74512 0.92145 5.834 1.00947L10.0527 5.22822C10.1408 5.31709 10.1901 5.43712 10.1901 5.56221C10.1901 5.68729 10.1408 5.80732 10.0527 5.89619Z" fill="#18988B"/>
                                                            </svg>
                                                    </td>
                                                    <td class="fs-16 txt-secondary">
                                                        {{$data->note}}
                                                    </td>
                                                    <td class="fs-16 txt-secondary">
                                                        {{$data->t_id}}
                                                    </td>
                                                    <td class="fs-16 txt-secondary">
                                                        £{{ number_format($tbalance, 2) }} 
                                                    </td>
                                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                                @else

                                                    <td class="fs-16 txt-secondary">
                                                        £{{number_format($data->amount, 2)}}
                                                        <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.0527 5.89619C9.96315 5.98283 9.84339 6.03126 9.71876 6.03126C9.59413 6.03126 9.47438 5.98283 9.38478 5.89619L5.96876 2.47432V11.656C5.96876 11.7803 5.91938 11.8995 5.83147 11.9874C5.74356 12.0753 5.62433 12.1247 5.50001 12.1247C5.37569 12.1247 5.25646 12.0753 5.16856 11.9874C5.08065 11.8995 5.03126 11.7803 5.03126 11.656V2.47432L1.61525 5.89619C1.52417 5.97094 1.40855 6.00914 1.29087 6.00336C1.17319 5.99758 1.06186 5.94823 0.978549 5.86492C0.895236 5.78161 0.84589 5.67028 0.84011 5.5526C0.834331 5.43492 0.87253 5.3193 0.947278 5.22822L5.16603 1.00947C5.2549 0.92145 5.37493 0.87207 5.50001 0.87207C5.6251 0.87207 5.74512 0.92145 5.834 1.00947L10.0527 5.22822C10.1408 5.31709 10.1901 5.43712 10.1901 5.56221C10.1901 5.68729 10.1408 5.80732 10.0527 5.89619Z" fill="#18988B"/>
                                                            </svg>
                                                    </td>
                                                    <td class="fs-16 txt-secondary">
                                                        {{$data->note}}
                                                    </td>
                                                    <td class="fs-16 txt-secondary">
                                                        {{$data->t_id}}
                                                    </td>
                                                    <td class="fs-16 txt-secondary">
                                                        £{{ number_format($tbalance, 2) }} 
                                                    </td>
                                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                                @endif
                                        @elseif($data->t_type == "Out")
                                                 <td class="fs-16 txt-secondary">
                                                    -£{{number_format($data->amount, 2) }}
                                                    <svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.0527 7.18393C9.96315 7.08574 9.84339 7.03085 9.71876 7.03085C9.59413 7.03085 9.47438 7.08574 9.38478 7.18393L5.96876 11.0621V0.656192C5.96876 0.515295 5.91938 0.380169 5.83147 0.28054C5.74356 0.180912 5.62433 0.124942 5.50001 0.124942C5.37569 0.124942 5.25646 0.180912 5.16856 0.28054C5.08065 0.380169 5.03126 0.515295 5.03126 0.656192V11.0621L1.61525 7.18393C1.52417 7.09921 1.40855 7.05592 1.29087 7.06247C1.17319 7.06902 1.06186 7.12494 0.978549 7.21937C0.895236 7.31379 0.84589 7.43995 0.84011 7.57333C0.834331 7.7067 0.87253 7.83774 0.947278 7.94096L5.16603 12.7222C5.2549 12.822 5.37493 12.8779 5.50001 12.8779C5.6251 12.8779 5.74512 12.822 5.834 12.7222L10.0527 7.94096C10.1408 7.84024 10.1901 7.7042 10.1901 7.56244C10.1901 7.42068 10.1408 7.28465 10.0527 7.18393Z" fill="#003057"/>
                                                    </svg> 
                                                </td>
                                                <td class="fs-16 txt-secondary">
                                                    {{$data->note}}
                                                </td>
                                                <td class="fs-16 txt-secondary">
                                                    {{$data->t_id}}
                                                </td>
                                                <td class="fs-16 txt-secondary">
                                                    £{{ number_format($tbalance, 2) }}
                                                </td> 
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
                                    <td>Previous Balance</td>
                                    <td>£{{ number_format($tbalance, 2) }}</td>
                                </tr>
                                </tbody>
                            </table>
                    </div>
                  
                </div>
                <div class="tab-pane fade" id="moneyIn" role="tabpanel" aria-labelledby="moneyIn-tab">
                
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
                                        {{$transaction->t_id}}
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
                <div class="tab-pane fade" id="moneyOut" role="tabpanel" aria-labelledby="moneyOut-tab">
                    
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
                                        {{$transaction->t_id}}
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
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                   
                    
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
                                @foreach ($alltransactions as $transaction)

                                    @if ($transaction->pending == 1)
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
                                                {{$transaction->t_id}}
                                            </td>
                                            <td class="fs-16 txt-secondary">
                                                £{{number_format($transaction->amount, 2)}} 
                                            </td>
                                        </tr>
                                    @else
                                        
                                    @endif
                                    
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
