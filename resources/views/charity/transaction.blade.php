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

    /* Ensure all DataTables stretch full width */
    table.dataTable {
        width: 100% !important;
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
                    
                    {{-- 1. DAILY SUMMARY TAB --}}
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
                                                <span class="badge bg-primary text-white px-3 view-daily-details" 
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
                        <form id="searchFormIn" action="{{ route('charity.tranview_search', $id) }}" method="POST" class="row g-3 bg-light p-3 rounded mb-3">
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
                            <table class="table table-custom" id="inTransactionsTable" style="width:100%">
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
                                <tbody></tbody>
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
                        <form id="searchFormOut" action="{{ route('charity.tranview_search', $id) }}" method="POST" class="row g-3 bg-light p-3 rounded mb-3">
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
                            <table class="table table-custom" id="outTransactionsTable" style="width:100%">
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
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 4. REPORTS --}}
                    <div class="tab-pane fade" id="nav-report" role="tabpanel">
                        <table class="table table-custom" id="reportsTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
                                    <table id="ledgerTable" class="table table-bordered table-striped" style="width:100%">
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
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. PENDING VOUCHERS --}}
                    <div class="tab-pane fade" id="nav-pendingVoucher" role="tabpanel">
                        <table class="table table-custom" id="pendingVouchersTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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

                    {{-- 8. CHECK TRANSACTIONS --}}
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
                                        <table class="table table-custom" id="checkTransOutTable" style="width:100%">
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
                                            <tbody></tbody>
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
                                        <table class="table table-custom" id="checkTransInTable" style="width:100%">
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
                                            <tbody></tbody>
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
                        <tbody id="modal-body-content"></tbody>
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


{{-- Single Dynamic Transaction Details Modal --}}
<div class="modal fade" id="tranDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #fdf3ee;">
            <div class="modal-header">
                <h5 class="modal-title txt-secondary">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0" id="tranDetailTable">
                    {{-- Populated via JS --}}
                </table>
            </div>
        </div>
    </div>
</div>



@endsection

@section('script')
<script>
    window.appRoutes = {
        inData: "{{ route('charity.transactions.in.data', $id) }}",
        outData: "{{ route('charity.transactions.out.data', $id) }}",
        ledgerData: "{{ route('charity.ledger.data', $id) }}",
        reportsData: "{{ route('charity.reports.data', $id) }}",
        pendingVouchersData: "{{ route('charity.pending.vouchers.data', $id) }}",
        checkTransOutData: "{{ route('charity.check.trans.out.data', $id) }}",
        checkTransInData: "{{ route('charity.check.trans.in.data', $id) }}",
        emailStore: "{{ route('useremail.store') }}",
        emailUpdate: "{{ route('charityemail.update') }}"
    };
    window.csrfToken = "{{ csrf_token() }}";
</script>

@verbatim
<script>
 $(document).ready(function() {
    
    // --- Daily Details Modal AJAX ---
    $('.view-daily-details').on('click', function() {
        const targetDate = $(this).data('date').toString();
        const displayDate = $(this).data('formatted-date');
        
        $.ajax({
            url: window.appRoutes.inData,
            type: 'GET',
            data: {
                fromDate: targetDate,
                toDate: targetDate,
                length: 1000 
            },
            success: function(res) {
                let rows = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(trans => {
                        rows += `
                            <tr>
                                <td>${trans.t_id || 'N/A'}</td>
                                <td>${trans.cheque_no || 'N/A'}</td>
                                <td>${trans.title || 'Charity Transaction'}</td>
                                <td class="text-end fw-bold">£${parseFloat(trans.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            </tr>
                        `;
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center text-muted">No details found for this date.</td></tr>';
                }
                $('#modal-date-display').text(displayDate);
                $('#modal-body-content').html(rows);
                $('#dailyDetailsModal').modal('show');
            },
            error: function() {
                alert('Error fetching daily details.');
            }
        });
    });

    // --- Transaction Details Modal ---
    $(document).on('click', '.view-tran-btn', function() {
        var data = $(this).data('json');
        
        var html = `
            <tr>
                <td class="text-muted">Date</td>
                <td class="px-2">:</td>
                <td>${data.date || 'N/A'}</td>
            </tr>
            <tr>
                <td class="text-muted">Transaction ID</td>
                <td class="px-2">:</td>
                <td><code>${data.t_id || 'N/A'}</code></td>
            </tr>
            <tr>
                <td class="text-muted">Transaction Type</td>
                <td class="px-2">:</td>
                <td>${data.title || 'N/A'}</td>
            </tr>
            <tr>
                <td class="text-muted">Charity Name</td>
                <td class="px-2">:</td>
                <td>${data.charity || 'N/A'}</td>
            </tr>
            <tr>
                <td class="text-muted">Donor</td>
                <td class="px-2">:</td>
                <td>${data.user || 'N/A'}</td>
            </tr>`;
            
        if (data.donation_by) {
            html += `
            <tr>
                <td class="text-muted">Donate By</td>
                <td class="px-2">:</td>
                <td>${data.donation_by}</td>
            </tr>`;
        }
        
        html += `
            <tr>
                <td class="text-muted fw-bold">Amount</td>
                <td class="px-2 fw-bold">:</td>
                <td class="fw-bold text-success">£${data.amount || '0.00'}</td>
            </tr>`;
            
        if (data.cheque_no) {
            html += `
            <tr>
                <td class="text-muted">Voucher Number</td>
                <td class="px-2">:</td>
                <td>${data.cheque_no}</td>
            </tr>`;
        }
        
        if (data.note) {
            html += `
            <tr>
                <td class="text-muted">Comment</td>
                <td class="px-2">:</td>
                <td>${data.note}</td>
            </tr>`;
        }
        
        if (data.charitynote) {
            html += `
            <tr>
                <td class="text-muted">Charity Note</td>
                <td class="px-2">:</td>
                <td>${data.charitynote}</td>
            </tr>`;
        }
        
        if (data.barcode_image) {
            html += `
            <tr>
                <td class="text-muted align-top">Barcode</td>
                <td class="px-2 align-top">:</td>
                <td>
                    <img src="${data.barcode_image}" alt="Barcode Image" class="img-fluid" style="max-width: 250px;">
                </td>
            </tr>`;
        }
        
        $('#tranDetailTable').html(html);
        $('#tranDetailModal').modal('show');
    });

    // --- Edit Date Modal Trigger ---
    $(document).on('click', '.edit-date-btn', function() {
        const id = $(this).data('id');
        let date = $(this).data('date');
        
        if (date && date.length >= 16) {
            date = date.substring(0, 10) + 'T' + date.substring(11, 16);
        }
        
        $('#modal_transaction_id').val(id);
        $('#modal_date_input').val(date);
        $('#editDateModal').modal('show');
    });

    // ==========================================
    // YAJRA DATATABLES INITIALIZATIONS
    // ==========================================

    var dtDom = '<"row"<"col-md-6"l><"col-md-6 text-end"f>>rtip';

    $('#inTransactionsTable').DataTable({
        processing: true,
        serverSide: true,
        dom: dtDom,
        ajax: {
            url: window.appRoutes.inData,
            data: function (d) {
                d.fromDate = $('#nav-transactionIn input[name="fromDate"]').val();
                d.toDate = $('#nav-transactionIn input[name="toDate"]').val();
            }
        },
        columns: [
            { data: 'formatted_date', name: 'created_at' },
            { data: 'donor_name', name: 'user.name' },
            { data: 't_id', name: 't_id' },
            { data: 'title', name: 'title' },
            { data: 'cheque_no', name: 'cheque_no' },
            { data: 'amount', name: 'amount' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[0, 'desc']] // Sorts by Date descending
    });

    $('#outTransactionsTable').DataTable({
        processing: true,
        serverSide: true,
        dom: dtDom,
        ajax: {
            url: window.appRoutes.outData,
            data: function (d) {
                d.fromDate = $('#nav-transactionOut input[name="fromDate"]').val();
                d.toDate = $('#nav-transactionOut input[name="toDate"]').val();
            }
        },
        columns: [
            { data: 'formatted_date', name: 'created_at' },
            { data: 't_id', name: 't_id' },
            { data: 'name', name: 'name' },
            { data: 'note', name: 'note' },
            { data: 'amount', name: 'amount' },
            { data: 'status_switch', name: 'status_switch', orderable: false, searchable: false }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[0, 'desc']] // Sorts by Date descending
    });

    $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        dom: dtDom,
        ajax: window.appRoutes.reportsData,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'formatted_date', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[1, 'desc']] // Sorts by Date descending (Column 1)
    });

    $('#pendingVouchersTable').DataTable({
        processing: true,
        serverSide: true,
        dom: dtDom,
        ajax: window.appRoutes.pendingVouchersData,
        columns: [
            { data: 'formatted_date', name: 'created_at' },
            { data: 'user_name', name: 'user.name' },
            { data: 'amount', name: 'amount' },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[0, 'desc']] // Sorts by Date descending
    });

    $('#checkTransOutTable').DataTable({
        processing: true,
        serverSide: true,
        dom: dtDom,
        ajax: window.appRoutes.checkTransOutData,
        columns: [
            { data: 'formatted_date', name: 'created_at' },
            { data: 't_id_html', name: 't_id' },
            { data: 't_type', name: 't_type' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: null, name: 'action', orderable: false, searchable: false, defaultContent: '' }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[0, 'desc']] // Sorts by Date descending
    });

    $('#checkTransInTable').DataTable({
        processing: true,
        serverSide: true,
        dom: dtDom,
        ajax: window.appRoutes.checkTransInData,
        columns: [
            { data: 'formatted_date', name: 'created_at' },
            { data: 't_id_html', name: 't_id' },
            { data: 't_type', name: 't_type' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: null, name: 'action', orderable: false, searchable: false, defaultContent: '' }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[0, 'desc']] // Sorts by Date descending
    });

    $('#ledgerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.appRoutes.ledgerData,
        columns: [
            { data: 'date', name: 'date' },
            { data: 't_id', name: 't_id' },
            { data: 'description', name: 'description' },
            { data: 'debit', name: 'debit' },
            { data: 'credit', name: 'credit' },
            { data: 'balance', name: 'balance' },
            { data: 'edit_btn', name: 'edit_btn', orderable: false, searchable: false }
        ],
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        order: [[0, 'desc']],
        autoWidth: false,
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip',
        columnDefs: [
            { orderable: false, targets: [1, 2, 6] },
            { className: 'text-end', targets: [3, 4, 5] }
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
        }
    });

    $('#searchFormIn').on('submit', function(e) {
        e.preventDefault();
        $('#inTransactionsTable').DataTable().ajax.reload();
    });

    $('#searchFormOut').on('submit', function(e) {
        e.preventDefault();
        $('#outTransactionsTable').DataTable().ajax.reload();
    });

    // --- Email AJAX Logic ---
    $("#addBtn").click(function(e){
        e.preventDefault();
        var email = $("#newemail").val();
        var charity_id = $("#charity_id").val();

        $.ajax({
            url: window.appRoutes.emailStore,
            type: "POST",
            data: { email: email, charity_id: charity_id, _token: window.csrfToken },
            success: function(res){
                if(res.status == 200){                    
                    $(".errmsg").html(`<div class="alert alert-success">${res.message}</div>`);
                    $("#emailTable tbody").prepend(`
                        <tr id="row_${res.data.id}">
                            <td>${res.data.date}</td>
                            <td class="email-cell">${res.data.email}</td>
                            <td class="text-right">
                                <button data-udid="${res.data.id}" data-email="${res.data.email}" class="btn btn-sm btn-outline-primary editBtn">Edit</button>
                                <form action="/useremail/${res.data.id}" method="POST" style="display:inline;">
                                    <input type="hidden" name="_token" value="${window.csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `);
                    $("#newemail").val("");
                }
            }
        });
    });

    $("body").on("click", ".editBtn", function(){
        var id = $(this).data("udid");
        var email = $(this).data("email");
        $("#update_id").val(id);
        $("#newemail").val(email);
        $("#addBtn").addClass("d-none");
        $("#updateBtn").removeClass("d-none");
    });

    $("#updateBtn").click(function(e){
        e.preventDefault();
        var id = $("#update_id").val();
        var email = $("#newemail").val();

        $.ajax({
            url: window.appRoutes.emailUpdate,
            type: "POST",
            data: { id: id, email: email, _token: window.csrfToken },
            success: function(res){
                if(res.status == 200){
                    $(".errmsg").html(`<div class="alert alert-success">${res.message}</div>`);
                    $("#newemail").val("");
                    $("#update_id").val("");
                    $("#updateBtn").addClass("d-none");
                    $("#addBtn").removeClass("d-none");
                }
            }
        });
    });

    // --- Status Switch AJAX ---
    $(document).on('change', '.status-switch', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var $switch = $(this);
        
        $.ajax({
            url: '/admin/charity-tran/update-payment-status',
            type: 'POST',
            dataType: 'json',
            data: { id: id, status: isChecked ? 1 : 0, _token: window.csrfToken },
            success: function(response) {
                if (response.success) {
                    alert('Updated successfully');
                }
            },
            error: function(xhr) {
                $switch.prop('checked', !isChecked);
                $switch.prop('disabled', false);
                console.log('Error:', xhr.responseJSON);
            }
        });
    });

});
</script>
@endverbatim
@endsection