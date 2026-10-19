@extends('layouts.admin')

@section('title', 'Daily Balance Reconciliation')

@section('content')

<style>
    #recon-table td { vertical-align: middle; }
    .text-muted { opacity: 0.5; font-weight: normal; }
    .summary-card {
        border-left: 4px solid #4a90e2; background: #fff; padding: 20px;
        border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .summary-card .card-title { font-size: 14px; color: #555; font-weight: 600; margin-bottom: 8px; }
    .summary-card .card-value { font-size: 28px; font-weight: 700; color: #333; }
    .summary-card .card-note { font-size: 12px; color: #888; margin-top: 8px; font-style: italic; }
    .section-title {
        font-size: 18px; font-weight: bold; color: #333; margin-top: 25px;
        margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px;
        display: flex; align-items: center; gap: 8px;
    }
    .section-title i { color: #4a90e2; }
    
    /* Column Group Styling */
    .col-donor-in { background: #f0fff4; }
    .col-donor-out { background: #fff5f5; }
    .col-charity-in { background: #f0fff4; }
    .col-charity-out { background: #fff5f5; }
    
    .actual-balance-input {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
</style>

<div class="dashboard-content p-4">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Daily Balance Reconciliation</h4>
        </div>
    </section>

    <!-- ===== Summary Cards ===== -->
    <div class="section-title">
        <i class="fas fa-chart-bar"></i> Current Status (Latest)
    </div>
    <div class="row mb-4">
        <!-- Latest Donor Balance -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Total Donor Balance</div>
                <div class="card-value text-success">£{{ number_format($latestDonorBalance, 2) }}</div>
                <div class="card-note">Sum of all topups minus all donations (cumulative).</div>
            </div>
        </div>

        <!-- Latest Charity Balance -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Total Charity Balance</div>
                <div class="card-value text-primary">£{{ number_format($latestCharityBalance, 2) }}</div>
                <div class="card-note">Donations received minus auto payments sent.</div>
            </div>
        </div>

        <!-- Latest Commission -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Total Commission</div>
                <div class="card-value text-warning">£{{ number_format($latestCommission, 2) }}</div>
                <div class="card-note">All-time commission collected from topups.</div>
            </div>
        </div>

        <!-- Total Dates -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Total Active Dates</div>
                <div class="card-value text-secondary">{{ $totalDates }}</div>
                <div class="card-note">Number of dates with transactions since project start.</div>
            </div>
        </div>
    </div>

    <!-- ===== Reconciliation Table ===== -->
    <div class="section-title">
        <i class="fas fa-table"></i> Daily Reconciliation History
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="recon-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Donor IN (Topup)</th>
                            <th>Donor OUT</th>
                            <th>All Donor Total Balance</th>
                            <th>Charity IN</th>
                            <th>Charity OUT</th>
                            <th>All Charity Total Balance</th>
                            <th>Total Commission</th>
                            <th>Actual Balance</th>
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
    let table = $('#recon-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        ajax: {
            url: "{{ route('developer.reconciliation') }}",
            type: "GET"
        },
        columns: [
            {data: 'sl', name: 'sl', searchable: false, orderable: false},
            {data: 'date', name: 'date', searchable: true},
            {data: 'daily_donor_in', name: 'daily_donor_in', searchable: false, orderable: false},
            {data: 'daily_donor_out', name: 'daily_donor_out', searchable: false, orderable: false},
            {data: 'donor_balance', name: 'donor_balance', searchable: false, orderable: false},
            {data: 'daily_charity_in', name: 'daily_charity_in', searchable: false, orderable: false},
            {data: 'daily_charity_out', name: 'daily_charity_out', searchable: false, orderable: false},
            {data: 'charity_balance', name: 'charity_balance', searchable: false, orderable: false},
            {data: 'commission', name: 'commission', searchable: false, orderable: false},
            {data: 'actual_balance', name: 'actual_balance', searchable: false, orderable: false}
        ],
        order: [[1, 'desc']],
        dom: '<"row mb-3"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
        ]
    });

    // Handle Save Actual Balance
    $(document).on('click', '.save-actual-balance', function() {
        var date = $(this).data('date');
        var input = $(this).siblings('.actual-balance-input');
        var value = input.val();
        var btn = $(this);
        var originalHtml = btn.html();

        if (value === '' || isNaN(value)) {
            alert('Please enter a valid number.');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('developer.reconciliation.update_actual') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                date: date,
                actual_balance: value
            },
            success: function(res) {
                if (res.success) {
                    btn.html('<i class="fas fa-check"></i>');
                    setTimeout(function() {
                        btn.html(originalHtml);
                    }, 1500);
                }
            },
            error: function() {
                alert('Error saving actual balance.');
                btn.html(originalHtml);
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // Handle Enter key on input
    $(document).on('keypress', '.actual-balance-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).siblings('.save-actual-balance').click();
        }
    });
});
</script>
@endsection