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
              <button class="nav-link" id="nav-another-tab" data-bs-toggle="tab" data-bs-target="#nav-another" type="button" role="tab" aria-controls="nav-another" aria-selected="false">Card Transaction</button>

            </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">

                <div class="tab-pane fade show active" id="nav-transactionAll" role="tabpanel" aria-labelledby="nav-all">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-9">
                                        <form class="form-inline" action="{{ route('search.donortran', $donor_id) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group my-2">
                                                        <label for="fromDate"><small>Date From </small></label>
                                                        <input class="form-control mr-sm-2" id="fromDate" name="fromDate" type="date">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group my-2">
                                                        <label for="toDate"><small>Date To </small></label>
                                                        <input class="form-control mr-sm-2" id="toDate" name="toDate" type="date">
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
                                <?php
                                // Calculate the Final Total Balance matching getLiveBalance() logic
                                $finalBalance = 0;
                                foreach ($tamount as $data) {
                                    $isExpiredPre = isset($data->expired) && $data->expired == '0';
                                    if ($isExpiredPre) continue;

                                    if ($data->t_type == "In") {
                                        $finalBalance += $data->amount;
                                    } elseif ($data->t_type == "Out") {
                                        $finalBalance -= $data->amount;
                                        if ($data->commission != 0) {
                                            $finalBalance -= $data->commission;
                                        }
                                    }
                                }
                                ?>

                                <table class="table table-custom shadow-sm bg-white" id="allTransactionTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 8%">Date</th>
                                            <th>Transaction Id</th>
                                            <th>Transaction type</th>
                                            <th>Voucher Number</th>
                                            <th>Charity Name</th>
                                            <th>Note</th>
                                            <th>Donate By</th>
                                            <th>Status</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th style="width: 10%">Balance</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach ($report as $data)
                                            @php
                                            $isExpired = (isset($data->expired) && $data->expired == '0') || (isset($data->provoucher) && $data->provoucher->expired == "Yes");
                                            $dateOrder = Carbon::parse($data->created_at)->format('Y-m-d H:i:s');
                                            $dateDisplay = Carbon::parse($data->created_at)->format('d/m/Y');
                                            @endphp

                                            @if($data->commission != 0)
                                            <tr data-balance-effect="{{ $isExpired ? 0 : -$data->commission }}">
                                                <td data-order="{{ $dateOrder }}">{{ $dateDisplay }}</td>
                                                <td>{{ $data->t_id }}</td>
                                                <td>Commission</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>-£{{ number_format($data->commission, 2) }}</td>
                                                <td class="balance-col"></td>
                                            </tr>
                                            @endif

                                            <tr data-balance-effect="{{ $isExpired ? 0 : ($data->t_type == 'In' ? $data->amount + $data->commission : -$data->amount) }}">
                                                <td data-order="{{ $dateOrder }}">{{ $dateDisplay }}</td>
                                                <td>{{ $data->t_id }}</td>
                                                <td>{{ $data->title }}</td>
                                                <td>
                                                    @if ($data->cheque_no)
                                                        <button type="button" class="text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1 d-block text-center" data-bs-toggle="modal" data-bs-target="#chequeModal{{ $data->id }}">
                                                            {{ $data->cheque_no }} 
                                                        </button>
                                                        <div class="modal fade" id="chequeModal{{ $data->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Cheque Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Cheque Number:</strong> {{ $data->cheque_no }}</p>
                                                                @if($data->barcode_image)
                                                                    <img src="{{ asset($data->barcode_image) }}" class="img-fluid">
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
                                                        @if ($data->pending === 0 || $data->pending === "0")
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                        @if ($data->expired === 0 || $data->expired === "0")
                                                            <span class="badge bg-warning">Expired</span>
                                                        @endif
                                                    @endif 
                                                    @if($data->crdAcptID){{ $data->crdAcptID }}@endif
                                                </td>
                                                <td>
                                                    @if($data->charity_id){{ $data->charity->name }}@endif
                                                    @if($data->crdAcptID){{ $data->crdAcptLoc }}@endif
                                                </td>
                                                <td>
                                                    {{ $data->donation_id ? $data->donation->mynote : $data->note }} <br>
                                                    {{ $data->donation_id ? $data->donation->charitynote : '' }}
                                                </td>
                                                <td>{{ $data->donation_by }}</td>
                                                <td>
                                                    @if($data->pending == "0" || $data->pending === 0) Pending @endif 
                                                    <br>
                                                    @if ($isExpired) Expired @endif
                                                </td>

                                                @if($data->t_type == "In")
                                                    @if($data->commission != 0)
                                                        <td>£{{ number_format($data->amount + $data->commission, 2) }}</td>
                                                        <td></td>
                                                    @else
                                                        <td>£{{ number_format($data->amount, 2) }}</td>
                                                        <td></td>
                                                    @endif
                                                @elseif($data->t_type == "Out")
                                                    <td></td>
                                                    <td>-£{{ number_format($data->amount, 2) }}</td>
                                                @endif
                                                
                                                <td class="balance-col"></td> <!-- JavaScript fills this dynamically -->
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="9"></th>
                                            <th><strong>Previous Balance</strong></th>
                                            <th id="prev-balance-cell"></th>
                                        </tr>
                                    </tfoot>
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
                                <th>Note</th>
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
                                
                                <td>
                                {{$transaction->donation_id ? $transaction->donation->mynote : $transaction->note }} <br>
                                {{$transaction->donation_id ? $transaction->donation->charitynote : ''}}
                                </td>

                                <td>{{ $transaction->cheque_no}}</td>
                                <td>@if($transaction->pending == "0") Pending @endif  <br>
                                @if ($transaction->provoucher)
                                    {{ $transaction->provoucher->expired == "Yes" ? 'Expired' : '' }}
                                @endif</td>
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
                                <th>Note</th>
                                <th>Donate By</th>
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
                                
                                <td>
                                {{$gift->donation_id ? $gift->donation->mynote : $gift->note }} <br>
                                {{$gift->donation_id ? $gift->donation->charitynote : ''}}
                                </td>

                                <td>{{$gift->donation_by}}</td>
                                <td>{{ $gift->source}}</td>
                                <td>£{{ $gift->amount + $gift->commission }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="nav-another" role="tabpanel" aria-labelledby="nav-another-tab">
                    <div class="row my-2">
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                            <table class="table table-custom shadow-sm bg-white">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Id</th>
                                    <th>transaction type</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>

                                <?php
                                    $tbalance = 0;
                                ?>


                                <tbody>
                                    @foreach ($cardTransactions as $data)
                                    <tr>
                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                        <td>{{$data->t_id}} </td>
                                        <td>{{$data->title}} </td>
                                        <td>£{{number_format($data->amount, 2) }}</td>
                                            
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>


                    <br>
                    <hr>
                    <div class="row my-2">
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                            <table class="table table-custom shadow-sm bg-white">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Utid</th>
                                        <th>cardID</th>
                                        <th>Donor CardProxyId</th>
                                        <th>accNo</th>
                                        <th>localDate</th>
                                        <th>crdAcptID</th>
                                        <th>crdAcptLoc</th>
                                        <th>msgType</th>
                                        <th>billAmt</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($authorizations as $data)

                                    @php
                                        $cardProduct = \App\Models\CardProduct::where('user_id', $donor_id)->first();
                                        $realDonor = \App\Models\CardProduct::where('CardProxyId', $data->cardID)->first();
                                        $realDonorId = $realDonor ? $realDonor->user_id : null;
                                    @endphp


                                    <tr>
                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                        <td>{{$data->Utid}} </td>
                                        <td>{{$data->cardID}} - {{ $realDonorId ?? ''}} </td>
                                        <td>
                                            @if ($data->cardID == $cardProduct->CardProxyId)
                                                <span class="badge bg-success">
                                                    {{$cardProduct->CardProxyId ?? ''}} 
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    {{$cardProduct->CardProxyId ?? ''}} 
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                        
                                            @if($data->accNo){{ $data->accNo}} @endif

                                        </td>
                                        <td>
                                            @if($data->localDate){{ $data->localDate}} @endif 
                                        </td>
                                        <td>
                                            {{$data->crdAcptID ? $data->crdAcptID : ''}}
                                        </td>
                                        <td>{{$data->crdAcptLoc}}</td>
                                        <td>{{$data->msgType}}</td>
                                        <td>£{{number_format($data->billAmt, 2) }}</td>

                                        <td>

                                            @if ($data->cardID != $cardProduct->CardProxyId)
                                                <button 
                                                    class="btn btn-sm btn-primary openChangeDonorModal"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#changeDonorModal"
                                                    data-user="{{ $donor_id }}"
                                                    data-real-donor="{{ $realDonorId }}"
                                                    data-auth="{{ $data->Utid }}"
                                                    data-card="{{ $cardProduct->CardProxyId ?? '' }}"
                                                >
                                                    Change to Real Donor
                                                </button>
                                            @endif

                                            
                                        </td>
                                            
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <div class="modal fade" id="changeDonorModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title">Change Real Donor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <p><strong>User ID:</strong> <span id="modalUserId"></span></p>
                                        <p><strong>Authorization ID:</strong> <span id="modalAuthId"></span></p>
                                        <p><strong>Card Proxy ID:</strong> <span id="modalCardProxy"></span></p>
                                        <p><strong>Card Original Donor:</strong> <span id="modalrealDonor"></span></p>

                                        <form method="POST" action="{{ route('change.real.donor') }}">
                                            @csrf

                                            <input type="hidden" name="user_id" id="formUserId">
                                            <input type="hidden" name="authorization_id" id="formAuthId">
                                            <input type="hidden" name="card_proxy_id" id="formCardProxy">
                                            <input type="hidden" name="real_donor_id" id="formrealDonor">

                                            <input type="text" name="usertranid" class="form-control m-3" placeholder="Enter Transaction ID" required>

                                            <button type="submit" class="btn btn-danger">
                                                Change Real Donor
                                            </button>
                                        </form>

                                    </div>

                                </div>
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

@section('script')
<script>
$(document).on('click', '.openChangeDonorModal', function () {

    let userId = $(this).data('user');
    let authId = $(this).data('auth');
    let cardId = $(this).data('card');
    let realDonor = $(this).data('real-donor');

    $('#modalUserId').text(userId);
    $('#modalAuthId').text(authId);
    $('#modalCardProxy').text(cardId); 
    $('#modalrealDonor').text(realDonor); 

    $('#formUserId').val(userId);
    $('#formAuthId').val(authId);
    $('#formCardProxy').val(cardId);
    $('#formrealDonor').val(realDonor);

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pass the PHP Final Balance variable to JavaScript safely
    var finalBalance = {{ $finalBalance ?? 0 }};
    var title = 'Donor Transactions'; // Update to your preferred title
    var data = 'Report'; // Update to your preferred data string

    var table = $('#allTransactionTable').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [ 
                { orderable: false, 'targets': [10] } // Removed the type: 'date' line
            ],
        order: [[ 0, 'desc' ]], // Latest transactions first
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            {extend: 'pdfHtml5', title: 'Report', orientation: 'portrait', header: true,
                customize: function (doc) {
                    doc.content.splice(0, 1, {
                        text: [
                            { text: data + '\n', bold: true, fontSize: 12 },
                            { text: title + '\n', bold: true, fontSize: 15 }
                        ],
                        margin: [0, 0, 0, 12],
                        alignment: 'center'
                    });
                    doc.defaultStyle.alignment = 'center';
                }
            },
            {extend: 'print', title: "<p style='text-align:center;'>" + data + "<br>" + title + "</p>", header: true,
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }
        ],
        
        // THIS FUNCTION CALCULATES BALANCE BACKWARDS (NEWEST TO OLDEST)
        drawCallback: function(settings) {
            var api = this.api();
            
            // 1. Get ALL rows currently filtered/sorted (Newest to Oldest)
            var allNodes = api.rows({search:'applied', order:'current'}).nodes();
            var allEffects = [];
            
            for (var i = 0; i < allNodes.length; i++) {
                allEffects.push(parseFloat($(allNodes[i]).data('balance-effect')) || 0);
            }

            // 2. Calculate "Previous Balance" (the balance BEFORE the oldest transaction in the list)
            // We start with the absolute final balance and subtract all effects to find the starting point
            var previousBalance = finalBalance;
            for (var i = 0; i < allEffects.length; i++) {
                previousBalance -= allEffects[i];
            }
            $('#prev-balance-cell').html('£' + previousBalance.toFixed(2));

            // 3. Assign the running balance to the DOM nodes (Top-down: Newest to Oldest)
            // The first row (newest) gets the absolute finalBalance.
            // Every subsequent row subtracts its effect to show the historical balance.
            var runningBal = finalBalance;
            for (var i = 0; i < allNodes.length; i++) {
                var cell = allNodes[i].cells[10]; // Index 10 is the Balance column
                $(cell).html('£' + runningBal.toFixed(2));
                runningBal -= allEffects[i]; // Step backwards in time chronologically
            }
        }
    });
});
</script>


@endsection
