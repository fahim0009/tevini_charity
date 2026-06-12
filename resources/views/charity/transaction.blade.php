@extends('layouts.admin')

@section('content')
<style>
    .status-switch:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* ===== Fix DataTable Header Colors ===== */
    
    /* Ledger Table */
    #ledgerTable thead th {
        background-color: #212529 !important;
        color: #ffffff !important;
        border-color: #32383e !important;
    }
    
    #ledgerTable tfoot td {
        background-color: #cfe2ff !important;
    }

    /* Daily Summary Table */
    #nav-summary .bg-light th {
        background-color: #f8f9fa !important;
    }

    /* General fix for all tables with bg-light header */
    table thead.bg-light th,
    table thead tr.bg-light th {
        background-color: #f8f9fa !important;
    }

    /* General fix for all tables with table-dark header */
    table thead.table-dark th,
    table thead tr.table-dark th {
        background-color: #212529 !important;
        color: #ffffff !important;
    }
</style>
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section d-flex align-items-center">
            <span class="iconify" data-icon="icon-park-outline:transaction" data-width="25"></span> 
            <h4 class="mx-2 mb-0">Charity Financial Overview</h4>
        </div>
    </section>

    <section class="mt-3">
        <div class="row mx-0">
            <div class="col-md-12">
                <nav>
                    <div class="nav nav-tabs" id="charityTab" role="tablist">
                        <button class="nav-link " id="summary-tab" data-bs-toggle="tab" data-bs-target="#nav-summary" type="button" role="tab">Daily Summary</button>
                        <button class="nav-link active" id="transactionIn-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionIn" type="button" role="tab">Transaction In</button>
                        <button class="nav-link" id="transactionOut-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab">Transaction Out</button>
                        <button class="nav-link" id="report-tab" data-bs-toggle="tab" data-bs-target="#nav-report" type="button" role="tab">Reports</button>
                        <button class="nav-link" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#nav-ledger" type="button" role="tab">Ledger</button>
                        <button class="nav-link" id="pendingVoucher-tab" data-bs-toggle="tab" data-bs-target="#nav-pendingVoucher" type="button" role="tab">Pending Vouchers</button>
                        <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#nav-email" type="button" role="tab">Email Configuration</button>
                        <button class="nav-link" id="check-tran-tab" data-bs-toggle="tab" data-bs-target="#check-trans" type="button" role="tab">Check Transactions</button>
                    </div>
                </nav>

                <div class="tab-content bg-white shadow-sm p-3" id="nav-tabContent">
                    
                    {{-- 1. DAILY SUMMARY TAB (Optimized) --}}
                    <div class="tab-pane fade" id="nav-summary" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Grouped Daily Totals</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover border datatable-init">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Charity Name</th>
                                        <th class="text-center">Transaction Count</th>
                                        <th class="text-end">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailySummary as $summary)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($summary->trans_date)->format('d/m/Y') }}</td>
                                        <td>{{ $summary->charity->name ?? 'Unknown Charity' }}</td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <span class="badge  bg-primary text-white px-3 view-daily-details" 
                                                    style="cursor: pointer;"
                                                    data-date="{{ $summary->trans_date }}"
                                                    data-formatted-date="{{ \Carbon\Carbon::parse($summary->trans_date)->format('d/m/Y') }}">
                                                    {{ $summary->total_entries }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="text-end font-monospace fw-bold text-success">£{{ number_format($summary->total_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 2. TRANSACTION IN --}}
                    <div class="tab-pane fade show active" id="nav-transactionIn" role="tabpanel">
                        
                        <form action="{{ route('charity.tranview_search', $id) }}" method="POST" class="row g-3 bg-light p-3 rounded mb-3">
                            @csrf
                            <div class="col-md-3">
                                <label class="small">Date From</label>
                                <input type="date" name="fromDate" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="small">Date To</label>
                                <input type="date" name="toDate" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-theme text-white w-100">Search</button>
                            </div>
                        </form>
                        

                        <div class="overflow mt-3">
                            <table class="table table-custom datatable-init">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Donor</th>
                                        <th>Transaction ID</th>
                                        <th>Type</th>
                                        <th>Voucher #</th>
                                        <th>Amount</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($intransactions as $transaction)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->t_id }}</td>
                                        <td>{{ $transaction->title }}</td>
                                        <td>{{ $transaction->cheque_no }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#tranDetailModal{{ $transaction->id }}" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#18988B" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                                                    <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
                                                </svg>
                                            </a>

                                            {{-- Modal --}}
                                            <div class="modal fade" id="tranDetailModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="tranDetailLabel{{ $transaction->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="background-color: #fdf3ee;">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title txt-secondary" id="tranDetailLabel{{ $transaction->id }}">Transaction Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-borderless mb-0">
                                                                <tr>
                                                                    <td class="text-muted">Date</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Transaction ID</td>
                                                                    <td class="px-2">:</td>
                                                                    <td><code>{{ $transaction->t_id }}</code></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Transaction Type</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->title }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Charity Name</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->charity->name ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Donor</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                                                </tr>
                                                                @if($transaction->donation_by)
                                                                <tr>
                                                                    <td class="text-muted">Donate By</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->donation_by }}</td>
                                                                </tr>
                                                                @endif
                                                                <tr>
                                                                    <td class="text-muted fw-bold">Amount</td>
                                                                    <td class="px-2 fw-bold">:</td>
                                                                    <td class="fw-bold text-success">£{{ number_format($transaction->amount, 2) }}</td>
                                                                </tr>
                                                                @if($transaction->cheque_no)
                                                                <tr>
                                                                    <td class="text-muted">Voucher Number</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->cheque_no }}</td>
                                                                </tr>
                                                                @endif
                                                                @if($transaction->note)
                                                                <tr>
                                                                    <td class="text-muted">Comment</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->note }}</td>
                                                                </tr>
                                                                @endif
                                                                @if($transaction->standing_donationdetails_id && $transaction->standingdonationDetail && $transaction->standingdonationDetail->StandingDonation && $transaction->standingdonationDetail->StandingDonation->charitynote)
                                                                <tr>
                                                                    <td class="text-muted">Charity Note</td>
                                                                    <td class="px-2">:</td>
                                                                    <td>{{ $transaction->standingdonationDetail->StandingDonation->charitynote }}</td>
                                                                </tr>
                                                                @endif
                                                                @if($transaction->barcode_image)
                                                                <tr>
                                                                    <td class="text-muted align-top">Barcode</td>
                                                                    <td class="px-2 align-top">:</td>
                                                                    <td>
                                                                        <img src="{{ asset($transaction->barcode_image) }}" alt="Barcode Image" class="img-fluid" style="max-width: 250px;">
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <style>
                        .status-switch:disabled {
                            opacity: 0.6;
                            cursor: not-allowed;
                        }
                    </style>

                    {{-- 3. TRANSACTION OUT --}}
                    <div class="tab-pane fade" id="nav-transactionOut" role="tabpanel">
                        <form action="{{ route('charity.tranview_search', $id) }}" method="POST" class="row g-3 bg-light p-3 rounded mb-3">
                            @csrf
                            <div class="col-md-3">
                                <label class="small">Date From</label>
                                <input type="date" name="fromDate" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="small">Date To</label>
                                <input type="date" name="toDate" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-theme text-white w-100">Search</button>
                            </div>
                        </form>
                        <div class="overflow mt-3">
                            <table class="table table-custom datatable-init">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction ID</th>
                                        <th>Source</th>
                                        <th>Note</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($outtransactions as $transaction)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->t_id }}</td>
                                        <td>{{ $transaction->name }}</td>
                                        <td>{{ $transaction->note }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input status-switch"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="status-{{ $transaction->id }}"
                                                    data-id="{{ $transaction->id }}"
                                                    @if($transaction->bank_payment_status) checked @endif >
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 4. REPORTS --}}
                    <div class="tab-pane fade" id="nav-report" role="tabpanel">
                        <table class="table table-custom datatable-init">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $key => $report)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                    <td><a class="btn btn-sm btn-theme text-white" href="{{ route('instreport', $report->id) }}">View Report</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 5. LEDGER --}}
                    <div class="tab-pane fade" id="nav-ledger" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <table class="table table-bordered text-center mt-4">
                                    <tr class="bg-light"><th>Total In</th><td>{{ number_format($totalIN, 2) }}</td></tr>
                                    <tr class="bg-light"><th>Total Out</th><td>{{ number_format($totalOUT, 2) }}</td></tr>
                                    <tr class="table-primary"><th>Current Balance</th><td><strong>{{ number_format($totalIN - $totalOUT, 2) }}</strong></td></tr>
                                </table>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="ledgerTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="table-dark">
                                                <th>Date</th>
                                                <th>Transaction ID</th>
                                                <th>Description</th>
                                                <th class="text-end">Debit (-)</th>
                                                <th class="text-end">Credit (+)</th>
                                                <th class="text-end">Balance</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr class="table-info font-weight-bold">
                                                <td colspan="5" class="text-end">Current Total Balance:</td>
                                                <td class="text-end">{{ number_format($currentTotalBalance, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach($finalLedger as $entry)
                                                <tr>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d H:i') }}
                                                        @if($entry['credit'] > 0)
                                                            <a href="javascript:void(0)" 
                                                            class="text-primary ml-2 edit-date-btn" 
                                                            data-id="{{ $entry['real_id'] }}" 
                                                            data-date="{{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d\TH:i') }}"
                                                            data-toggle="modal" 
                                                            data-target="#editDateModal"
                                                            title="Edit Date">
                                                                <i class="fas fa-edit fa-sm"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td><code>{{ $entry['t_id'] }}</code></td>
                                                    <td>{{ $entry['description'] }}</td>
                                                    <td class="text-danger text-end">
                                                        {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}
                                                    </td>
                                                    <td class="text-success text-end">
                                                        {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}
                                                    </td>
                                                    <td class="text-end"><strong>{{ number_format($entry['balance'], 2) }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. PENDING VOUCHERS --}}
                    <div class="tab-pane fade" id="nav-pendingVoucher" role="tabpanel">
                        <table class="table table-custom datatable-init">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pvouchers as $voucher)
                                <tr>
                                    <td>{{ $voucher->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $voucher->user->name ?? 'N/A' }}</td>
                                    <td>£{{ number_format($voucher->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $voucher->status == 0 ? 'bg-warning' : ($voucher->status == 1 ? 'bg-success' : 'bg-danger') }}">
                                            {{ $voucher->status == 0 ? 'Pending' : ($voucher->status == 1 ? 'Complete' : 'Cancelled') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 7. EMAIL CONFIG --}}
                    <div class="tab-pane fade" id="nav-email" role="tabpanel">
                        <div class="card p-4 border-0">
                            <h6>Add Supplementary Email</h6>
                            <div class="errmsg"></div>
                            <form class="row g-3 align-items-end" id="emailAjaxForm">
                                <div class="col-md-5">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="newemail" placeholder="email@charity.com">
                                    <input type="hidden" id="charity_id" value="{{$id}}">
                                    <input type="hidden" id="update_id" value="">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-theme text-white w-100" id="addBtn" type="button">Add Email</button>
                                    <button class="btn btn-primary w-100 d-none" id="updateBtn" type="button">Update Email</button>
                                </div>
                            </form>
                        </div>
                        
                        <table class="table table-custom mt-4" id="emailTable">
                            <thead>
                                <tr>
                                    <th>Date Added</th>
                                    <th>Email</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (\App\Models\UserDetail::where('charity_id', $id)->get() as $data)
                                <tr id="row_{{$data->id}}">
                                    <td>{{ $data->date }}</td>
                                    <td class="email-cell">{{ $data->email }}</td>
                                    <td class="text-right">
                                        <button data-udid="{{$data->id}}" data-email="{{$data->email}}" class="btn btn-sm btn-outline-primary editBtn">Edit</button>
                                        <form action="{{ route('useremail.destroy', $data->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 8. --}}

                    <div class="tab-pane fade" id="check-trans" role="tabpanel">
                        <div class="accordion mt-4" id="transactionAccordion">
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                         Transactions OUT Table
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#transactionAccordion">
                                    <div class="accordion-body">
                                        <table class="table table-custom" id="emailTable1">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>TranID</th>
                                                    <th>Tran type</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (\App\Models\Transaction::where('charity_id', $id)->orderby('id', 'DESC')->get() as $data)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                                    <td class="email-cell">
                                                        @if($data->status == 0)
                                                            <span class="badge bg-danger">{{ $data->t_id }}</span>
                                                        @else
                                                            {{ $data->t_id }}
                                                        @endif
                                                    </td>
                                                    <td class="email-cell">{{ $data->t_type }}</td>
                                                    <td class="email-cell">{{ $data->amount }}</td>
                                                    <td class="email-cell">
                                                        <span class="{{ $data->status == 0 ? 'text-danger fw-bold' : '' }}">
                                                            {{ $data->status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Transactions IN Table
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#transactionAccordion">
                                    <div class="accordion-body">
                                        <table class="table table-custom" id="emailTable2">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>TranID</th>
                                                    <th>Tran type</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (\App\Models\Usertransaction::where('charity_id', $id)->orderby('id', 'DESC')->limit(200)->get() as $data)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                                    <td class="email-cell">
                                                        @if($data->status == 0)
                                                            <span class="badge bg-danger">{{ $data->t_id }}</span>
                                                        @else
                                                            {{ $data->t_id }}
                                                        @endif
                                                    </td>
                                                    <td class="email-cell">{{ $data->t_type }}</td>
                                                    <td class="email-cell">{{ $data->amount }}</td>
                                                    <td class="email-cell">
                                                        <span class="{{ $data->status == 0 ? 'text-danger fw-bold' : '' }}">
                                                            {{ $data->status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right"></td>
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
        </div>
    </section>
</div>

<div class="modal fade" id="dailyDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Transactions for <span id="modal-date-display"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped" id="modal-transactions-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Voucher #</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="modal-body-content">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editDateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaction Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('transactions.update-date') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="transaction_id" id="modal_transaction_id">
                    <div class="form-group">
                        <label>Transaction Date & Time</label>
                        <input type="datetime-local" name="new_date" id="modal_date_input" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Date</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
@section('script')
<script>
$(document).ready(function() {
    $('.edit-date-btn').on('click', function() {
        const id = $(this).data('id');
        const date = $(this).data('date');
        
        $('#modal_transaction_id').val(id);
        $('#modal_date_input').val(date);
    });
});
</script>
<script>
    $(document).ready(function() {
        // 1. Convert the PHP Collection to a JS Object
        const allTransactions = @json($intransactions);

        $('.view-daily-details').on('click', function() {
            const targetDate = $(this).data('date').toString(); // Ensure it's a string
            const displayDate = $(this).data('formatted-date');
            
            console.log('Filtering for date:', targetDate);
            console.log('Total pool size:', allTransactions.length);

            // 2. Filter transactions (Handling potential timestamp mismatches)
            const filtered = allTransactions.filter(item => {
                if (!item.created_at) return false;
                // Split by ' ' or 'T' to handle different ISO formats and get YYYY-MM-DD
                const itemDate = item.created_at.split(/[ T]/)[0]; 
                return itemDate === targetDate;
            });

            console.log('Found matches:', filtered.length);

            // 3. Populate the Modal
            let rows = '';
            if (filtered.length > 0) {
                filtered.forEach(trans => {
                    rows += `
                        <tr>
                            <td>${trans.t_id || 'N/A'}</td>
                            <td>${trans.cheque_no || 'N/A'}</td>
                            <td>${trans.note || 'Charity Transaction'}</td>
                            <td class="text-end fw-bold">£${parseFloat(trans.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        </tr>
                    `;
                });
            } else {
                rows = '<tr><td colspan="4" class="text-center text-muted">No details found for this date.</td></tr>';
            }

            $('#modal-date-display').text(displayDate);
            $('#modal-body-content').html(rows);
            
            // 4. Show the Modal
            $('#dailyDetailsModal').modal('show');
        });
    });
</script>

<script>
    $(document).ready(function() {

        var title = 'Report: ';
        var data = 'Data: ';


        // datatable common
        $('#reportDT').DataTable({
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true,
            columnDefs: [ { type: 'date', 'targets': [0] } ],
            order: [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {extend: 'copy'},
                {extend: 'excel', title: title},
                {extend: 'print',
                exportOptions: {
                stripHtml: false
            },
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
            ]
        });

    });
</script>

<script>
$(document).ready(function(){

    // -------------------
    // Add Email (AJAX)
    // -------------------
    $("#addBtn").click(function(e){
        e.preventDefault();

        var email = $("#newemail").val();
        var charity_id = $("#charity_id").val();

        $.ajax({
            url: "{{ route('useremail.store') }}",
            type: "POST",
            data: {
                email: email,
                charity_id: charity_id,
                _token: "{{ csrf_token() }}"
            },
            success: function(res){

                if(res.status == 200){                    
                    $(".errmsg").html(`<div class="alert alert-success">${res.message}</div>`);

                    $("#example tbody").prepend(`
                        <tr id="row_${res.data.id}">
                            <td>${res.data.date}</td>
                            <td class="email_${res.data.id}">${res.data.email}</td>
                            <td class="text-right">
                                <button data-id="${res.data.id}" data-email="${res.data.email}" class="btn btn-sm btn-primary mr-1 editBtn">Edit</button>
                                <form action="/useremail/${res.data.id}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-warning mr-1">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `);

                    $("#newemail").val("");
                }
            }
        });
    });

    // -------------------
    // Get Edit Data
    // -------------------
    $("body").on("click", ".editBtn", function(){

        var id = $(this).data("udid");
        var email = $(this).data("email");

        $("#update_id").val(id);
        $("#newemail").val(email);

        // Switch form buttons
        $("#addBtn").addClass("d-none");
        $("#updateBtn").removeClass("d-none");

    });

    // -------------------
    // Update Email (AJAX)
    // -------------------
    $("#updateBtn").click(function(e){
        e.preventDefault();

        var id = $("#update_id").val();
        var email = $("#newemail").val();

        $.ajax({
            url: "{{ route('charityemail.update') }}",
            type: "POST",
            data: {
                id: id,
                email: email,
                _token: "{{ csrf_token() }}"
            },
            success: function(res){

                if(res.status == 200){
                    $(".errmsg").html(`<div class="alert alert-success">${res.message}</div>`);

                    // Update email in table row
                    $(".email_" + id).text(email);

                    // reset form
                    $("#newemail").val("");
                    $("#update_id").val("");

                    $("#updateBtn").addClass("d-none");
                    $("#addBtn").removeClass("d-none");
                }
            }
        });

    });

});
</script>

<script>
 $(document).ready(function() {
    $(document).on('change', '.status-switch', function(e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var $switch = $(this);
        
        $.ajax({
            url:  '/admin/charity-tran/update-payment-status',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                status: isChecked ? 1 : 0,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Success - keep the new state
                    alert('Updated successfully');
                } else {
                }
            },
            error: function(xhr) {
                $switch.prop('checked', !isChecked);
                $switch.prop('disabled', false);
                
                console.log('=== DEBUG INFO ===');
                console.log('Status Code:', xhr.status);
                console.log('Response:', xhr.responseJSON);
                console.log('Response Text:', xhr.responseText);
                
            }
        });
    });
});
</script>

<script>
    // Ledger DataTable
 $(document).ready(function () {
    $('#ledgerTable').DataTable({
        responsive: true,
        pageLength: 100,           // Show 100 rows per page
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]], // Page length options
        order: [[0, 'desc']],      // Sort by Date descending (newest first)
        autoWidth: false,
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip', // Show length menu and search
        columnDefs: [
            { orderable: false, targets: [1, 2] }, // Disable sorting on Transaction ID and Description
            { className: 'text-end', targets: [3, 4, 5] } // Right-align numeric columns
        ],
        language: {
            search: "",
            searchPlaceholder: "Search ledger...",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries available",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Calculate totals for visible page only
            var pageDebit = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace(/[^0-9.-]/g, '')) || 0;
            }, 0);

            var pageCredit = api.column(4, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace(/[^0-9.-]/g, '')) || 0;
            }, 0);

            // Update footer (optional - shows page totals)
            // If you want to keep only Current Total Balance, remove this
        }
    });
});
</script>


@endsection
