@extends('layouts.admin')

@section('title', 'Standing Donation Monitoring')

@section('content')

<style>
    #standing-table-active td, #standing-table-inactive td { vertical-align: middle; }
    .text-muted { opacity: 0.5; font-weight: normal; }
    .summary-card {
        border-left: 4px solid #4a90e2; background: #fff; padding: 15px;
        border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); height: 100%;
    }
    .summary-card .card-title { font-size: 14px; color: #555; font-weight: 600; margin-bottom: 5px; }
    .summary-card .card-value { font-size: 24px; font-weight: 700; color: #333; }
    .summary-card .card-note { font-size: 12px; color: #888; margin-top: 8px; font-style: italic; }
    .section-title {
        font-size: 18px; font-weight: bold; color: #333; margin-top: 20px;
        margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px;
    }
</style>

<div class="dashboard-content p-4">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Standing Donation Monitoring</h4>
        </div>
    </section>

    <!-- ===== Summary Cards ===== -->
    <div class="section-title">Summary</div>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Total Standing Orders</div>
                <div class="card-value text-primary">{{ $totalStanding }}</div>
                <div class="card-note">All-time standing orders created.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Active Standing Orders</div>
                <div class="card-value text-success">{{ $activeStanding }}</div>
                <div class="card-note">Currently active and processing.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Inactive/Complete</div>
                <div class="card-value text-warning">{{ $inactiveStanding }}</div>
                <div class="card-note">Paused, ended or complete.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Total Collected (All Time)</div>
                <div class="card-value text-secondary">£{{ number_format($totalCollected, 2) }}</div>
                <div class="card-note">Total amount collected from all standing orders.</div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Today's Collected Amount</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($todayCollected, 2) }}</div>
                <div class="card-note">Total amount collected from standing orders today.</div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="border-left-color: #e83e8c;">
                <div class="card-title">This Week's Collected Amount</div>
                <div class="card-value" style="color: #e83e8c;">£{{ number_format($weekCollected, 2) }}</div>
                <div class="card-note">Total amount collected from standing orders this week.</div>
            </div>
        </div>
    </div>

    <!-- ===== Today's Standing Order Transactions ===== -->
    <div class="section-title">Today's Standing Order Transactions</div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100">
                    <thead>
                        <tr>
                            <th>Trans ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todaysStandingTransactions as $trans)
                        <tr>
                            <td><span class="fw-bold">#{{ $trans->t_id }}</span></td>
                            <td>{{ $trans->user->name ?? 'N/A' }} {{ $trans->user->surname ?? '' }}</td>
                            <td>{{ $trans->charity->name ?? 'N/A' }}</td>
                            <td><span class="fw-bold text-success">£{{ number_format($trans->amount, 2) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($trans->created_at)->format('d M, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== Upcoming 7 Days Standing Donations ===== -->
    <div class="section-title">Upcoming 7 Days Standing Donations (Expected)</div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Amount</th>
                            <th>Expected Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcoming7DaysDonations as $standing)
                        <tr>
                            <td><span class="fw-bold">#{{ $standing->id }}</span></td>
                            <td>{{ $standing->user->name ?? 'N/A' }} {{ $standing->user->surname ?? '' }}</td>
                            <td>{{ $standing->charity->name ?? 'N/A' }}</td>
                            <td><span class="fw-bold text-primary">£{{ number_format($standing->amount, 2) }}</span></td>
                            <td><span class="badge bg-info">{{ $standing->expected_date }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form id="filter-form" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Filter Type</label>
                    <select id="filter_type" class="form-control form-control-sm">
                        <option value="monthly" selected>Monthly</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="last7">Last 7 days</option>
                        <option value="custom">Date to Date</option>
                        <option value="all">ALL</option>
                    </select>
                </div>
                <div class="col-md-2 date-range-wrap" style="display:none;">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 date-range-wrap" style="display:none;">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary btn-sm px-4">Apply Filter</button>
                    <button type="button" id="reset-filter" class="btn btn-light btn-sm px-4">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== Active Standing Donations ===== -->
    <div class="section-title">Active Standing Donations</div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="standing-table-active">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Current Balance</th>
                            <th>Total In</th>
                            <th>Total Out</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Skipped</th>
                            <th>Remaining</th>
                            <th>Interval</th>
                            <th>Start Date</th>
                            <th>Next Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== Inactive Standing Donations ===== -->
    <div class="section-title">Inactive / Complete Standing Donations</div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="standing-table-inactive">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Current Balance</th>
                            <th>Total In</th>
                            <th>Total Out</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Skipped</th>
                            <th>Remaining</th>
                            <th>Interval</th>
                            <th>Start Date</th>
                            <th>Next Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Standing Donation Payment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Charity</th>
                                <th>Amount</th>
                                <th>Instalment Date</th>
                                <th>Mode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="payment-details-content"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
 $(function() {
    // Common columns for both tables
    const columns = [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
        {data: 'date', name: 'created_at', searchable: false},
        {data: 'donor', name: 'donor', searchable: true},
        {data: 'charity', name: 'charity', searchable: true},
        {data: 'current_balance', name: 'current_balance', searchable: false, orderable: false},
        {data: 'balance_in', name: 'balance_in', searchable: false, orderable: false},
        {data: 'balance_out', name: 'balance_out', searchable: false, orderable: false},
        {data: 'amount', name: 'amount', searchable: false},
        {data: 'payment_type', name: 'payment_type', searchable: false},
        {data: 'total_payments', name: 'total_payments', searchable: false},
        {data: 'payments_made', name: 'payments_made', searchable: false},
        {data: 'payments_skipped', name: 'payments_skipped', searchable: false},
        {data: 'payments_remaining', name: 'payments_remaining', searchable: false},
        {data: 'interval_text', name: 'interval_text', searchable: false},
        {data: 'start_date', name: 'start_date', searchable: false},
        {data: 'next_payment', name: 'next_payment', searchable: false},
        {data: 'status', name: 'status', searchable: false}
    ];

    // Active Table
    let tableActive = $('#standing-table-active').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('developer.standing_donations') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
                d.tab_type = 'active';
            }
        },
        columns: columns,
        order: [[1, 'desc']],
        dom: '<"row mb-3"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
        ]
    });

    // Inactive Table
    let tableInactive = $('#standing-table-inactive').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('developer.standing_donations') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
                d.tab_type = 'inactive';
            }
        },
        columns: columns,
        order: [[1, 'desc']],
        dom: '<"row mb-3"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
        ]
    });

    // Handle Filter Type Change
    $('#filter_type').on('change', function() {
        if ($(this).val() == 'custom') {
            $('.date-range-wrap').show();
        } else {
            $('.date-range-wrap').hide();
        }
    });

    // Filter Handlers
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        tableActive.draw();
        tableInactive.draw();
    });

    $('#reset-filter').on('click', function() {
        $('#filter_type').val('monthly').trigger('change');
        $('#fromDate, #toDate').val('');
        tableActive.draw();
        tableInactive.draw();
    });

    // Handle View Payments Click
    $(document).on('click', '.view-payments', function() {
        const standingId = $(this).data('id');
        const url = "{{ route('developer.standing_payments', ':id') }}".replace(':id', standingId);

        $('#payment-details-content').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
        $('#paymentDetailsModal').modal('show');

        $.get(url, function(response) {
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="6" class="text-center">No payment records found.</td></tr>';
            } else {
                response.forEach(item => {
                    html += `<tr>
                        <td>${item.donor}</td>
                        <td>${item.charity}</td>
                        <td>${item.amount}</td>
                        <td>${item.date}</td>
                        <td>${item.mode}</td>
                        <td>${item.status}</td>
                    </tr>`;
                });
            }
            $('#payment-details-content').html(html);
        }).fail(function() {
            $('#payment-details-content').html('<tr><td colspan="6" class="text-center text-danger">Error loading payment details.</td></tr>');
        });
    });
});
</script>
@endsection