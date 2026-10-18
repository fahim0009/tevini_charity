@extends('layouts.admin')

@section('title', 'Charity Monitoring')

@section('content')

<style>
    #charity-table td { vertical-align: middle; }
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
            <h4 class="mb-0 mx-2">Charity Monitoring</h4>
        </div>
    </section>

    <!-- ===== Summary Cards ===== -->
    <div class="section-title">Summary</div>
    <div class="row mb-4">
        <!-- Today's New Charities -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Today's New Charities</div>
                <div class="card-value text-success">{{ $todayNewCharities }}</div>
                <div class="card-note">Charities registered today.</div>
            </div>
        </div>
        
        <!-- This Week's New Charities -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">This Week's New Charities</div>
                <div class="card-value text-primary">{{ $weekNewCharities }}</div>
                <div class="card-note">Charities registered this week.</div>
            </div>
        </div>
        
        <!-- Total Active Charities -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Total Active Charities</div>
                <div class="card-value" style="color: #17a2b8;">{{ $activeCharities }}</div>
                <div class="card-note">Charities with active status.</div>
            </div>
        </div>
        
        <!-- Total Inactive Charities -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #dc3545;">
                <div class="card-title">Total Inactive Charities</div>
                <div class="card-value text-danger">{{ $inactiveCharities }}</div>
                <div class="card-note">Charities with inactive status.</div>
            </div>
        </div>
        
        <!-- No Transaction Charities -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">No Transaction Charities</div>
                <div class="card-value text-warning">{{ $noTransactionCharities }}</div>
                <div class="card-note">Charities who haven't made any transactions.</div>
            </div>
        </div>
        
        <!-- Auto Payment Enabled -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6f42c1;">
                <div class="card-title">Auto Payment Enabled</div>
                <div class="card-value" style="color: #6f42c1;">{{ $autoPaymentEnabled }}</div>
                <div class="card-note">Charities with auto-payment enabled.</div>
            </div>
        </div>
        
        <!-- Auto Payment Disabled -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #e83e8c;">
                <div class="card-title">Auto Payment Disabled</div>
                <div class="card-value" style="color: #e83e8c;">{{ $autoPaymentDisabled }}</div>
                <div class="card-note">Charities with auto-payment disabled.</div>
            </div>
        </div>
        
        <!-- Total Charity Balance -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Total Charity Balance</div>
                <div class="card-value text-secondary">£{{ number_format($charityBalanceSum, 2) }}</div>
                <div class="card-note">Sum of balances of all charities.</div>
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
    <div class="section-title">All Charities</div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="charity-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name & Email</th>
                            <th>Account No</th>
                            <th>Balance In</th>
                            <th>Balance Out</th>
                            <th>Diff (In-Out)</th>
                            <th>Balance</th>
                            <th>Auto Payment</th>
                            <th>Status</th>
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
    let table = $('#charity-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('developer.charities') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
            {data: 'charity', name: 'charity', searchable: true},
            {data: 'account_no', name: 'account_no', searchable: false},
            {data: 'balance_in', name: 'balance_in', searchable: false, orderable: false},
            {data: 'balance_out', name: 'balance_out', searchable: false, orderable: false},
            {data: 'difference', name: 'difference', searchable: false, orderable: false},
            {data: 'balance', name: 'balance', searchable: false},
            {data: 'auto_payment', name: 'auto_payment', searchable: false},
            {data: 'status', name: 'status', searchable: false}
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