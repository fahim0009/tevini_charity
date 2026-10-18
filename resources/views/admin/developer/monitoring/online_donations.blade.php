@extends('layouts.admin')

@section('title', 'Online Donation Monitoring')

@section('content')

<style>
    #donation-table td { vertical-align: middle; }
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
            <h4 class="mb-0 mx-2">Online Donation Monitoring</h4>
        </div>
    </section>

    <!-- ===== Today's Summary ===== -->
    <div class="section-title">Today's Summary</div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Today's Donations</div>
                <div class="card-value text-success">{{ $todayCount }}</div>
                <div class="card-note">Total number of online donations today.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Today's Amount</div>
                <div class="card-value text-primary">£{{ number_format($todayAmount, 2) }}</div>
                <div class="card-note">Total monetary value of today's donations.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Today's Fee Collected</div>
                <div class="card-value text-warning">£{{ number_format($todayFee, 2) }}</div>
                <div class="card-note">Total Stripe fees collected today.</div>
            </div>
        </div>
    </div>

    <!-- ===== This Week's Summary ===== -->
    <div class="section-title">This Week's Summary</div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Week's Donations</div>
                <div class="card-value text-success">{{ $weekCount }}</div>
                <div class="card-note">Total number of online donations this week.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Week's Amount</div>
                <div class="card-value text-primary">£{{ number_format($weekAmount, 2) }}</div>
                <div class="card-note">Total monetary value of this week's donations.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Week's Fee Collected</div>
                <div class="card-value text-warning">£{{ number_format($weekFee, 2) }}</div>
                <div class="card-note">Total Stripe fees collected this week.</div>
            </div>
        </div>
    </div>

    <!-- ===== All-Time Summary ===== -->
    <div class="section-title">All-Time Summary</div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Total Donations</div>
                <div class="card-value text-secondary">{{ $allCount }}</div>
                <div class="card-note">All-time total number of online donations.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Total Amount</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($allAmount, 2) }}</div>
                <div class="card-note">All-time total monetary value of donations.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #e83e8c;">
                <div class="card-title">Total Fee Collected</div>
                <div class="card-value" style="color: #e83e8c;">£{{ number_format($allFee, 2) }}</div>
                <div class="card-note">All-time total Stripe fees collected.</div>
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

    <!-- Main Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="donation-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Tran ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Current Balance</th>
                            <th>Total Balance IN</th>
                            <th>Total Balance OUT</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
 $(function() {
    let table = $('#donation-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('developer.online_donations') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false}, // SL
            {data: 'date', name: 'created_at', searchable: false},
            {data: 'tran_id', name: 'tran_id', searchable: false},
            {data: 'donor', name: 'donor', searchable: true},
            {data: 'charity', name: 'charity', searchable: true},
            {data: 'current_balance', name: 'current_balance', searchable: false, orderable: false},
            {data: 'balance_in', name: 'balance_in', searchable: false, orderable: false},
            {data: 'balance_out', name: 'balance_out', searchable: false, orderable: false},
            {data: 'amount', name: 'amount', searchable: false},
            {data: 'payment_method', name: 'payment_method', searchable: false}
        ],
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
        table.draw();
    });

    $('#reset-filter').on('click', function() {
        $('#filter_type').val('monthly').trigger('change');
        $('#fromDate, #toDate').val('');
        table.draw();
    });
});
</script>
@endsection