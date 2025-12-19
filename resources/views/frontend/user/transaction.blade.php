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
                        {{-- <button class="btn-theme bg-primary" type="submit">Search</button> --}}
                        <button class="btn-theme bg-primary" type="button" id="filterButton">Search</button>
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
            <div class="tab-pane fade show mt-4 active" id="transaction" role="tabpanel"
                aria-labelledby="transaction-tab">

                <div class="data-container">
                    <table class="table table-theme pt-4 w-100" id="allTransactionTable">
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
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td><strong>Previous Balance</strong></td>
                                <td id="prev-balance">£0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            
            {{-- transaction In  --}}
            <div class="tab-pane fade" id="moneyIn" role="tabpanel" aria-labelledby="moneyIn-tab">
                    {{-- transaction in  --}}
                    <div class="data-container">
                        <table class="table table-theme mt-4 w-100"  id="exampleIn">
                              <thead>
                                  <tr>

                                    <th style="display: none">ID</th>
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
                                    <td class="fs-16 txt-secondary" style="display: none">{{ $transaction->id }}</td>
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
                    <table class="table table-theme mt-4 w-100" id="exampleOut">
                          <thead>
                              <tr>
                                <th style="display: none">ID</th>
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
                                <td class="fs-16 txt-secondary" style="display: none">{{ $transaction->id }}</td>
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

@section('script')

<script>

$(document).ready(function() {

    var title = 'Report: ';
    var data = 'Data: ';


    var table = $('#allTransactionTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('user.donor.alltransaction') }}",
            data: function (d) {
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'description', name: 'description' },
            { data: 'donation_by', name: 'donation_by', defaultContent: '' },
            { data: 'amount', name: 'amount' },
            { data: 'note', name: 'note', defaultContent: '' },
            { data: 'reference', name: 'reference' },
            { data: 'calculated_balance', name: 'calculated_balance' }
        ],
        order: [[0, 'desc']],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            {extend: 'pdfHtml5',
            title: 'Report',
            orientation : 'portrait',
                header:true,
                customize: function ( doc ) {
                    doc.content.splice(0, 1, {
                            text: [

                                    { text: data+'\n',bold:true,fontSize:12 },
                                    { text: title+'\n',bold:true,fontSize:15 }

                            ],
                            margin: [0, 0, 0, 12],
                            alignment: 'center'
                        });
                    doc.defaultStyle.alignment = 'center'
                }
            },
            {extend: 'print',
            title: "<p style='text-align:center;'>"+data+"<br>"+title+"</p>",
            header:true,
                customize: function (win){
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', 'inherit');
            }
            }
        ],



        drawCallback: function(settings) {
            var api = this.api();
            var lastRow = api.row(':last').data();
            if(lastRow) {
                $('#prev-balance').html(lastRow.calculated_balance);
            }

            // THIS IS THE FIX FOR MODALS:
            // Every time the table draws, move the modals to the body
            // and initialize them if necessary.
            $('body').append($('.modal')); 
        }
    });

    $('#filterButton').on('click', function(e) {
        e.preventDefault();
        table.ajax.reload(); 
    });
});










</script>
    
@endsection
