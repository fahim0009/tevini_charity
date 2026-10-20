@extends('layouts.admin')

@section('content')

<style>
    #pv-table td { vertical-align: middle; }
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
            <h4 class="mb-0 mx-2">Process Voucher Audit</h4>
        </div>
    </section>

    <!-- ===== Summary Cards ===== -->
    <div class="section-title">Summary</div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Today's Vouchers</div>
                <div class="card-value text-success">{{ $todayCount }}</div>
                <div class="card-note">Total vouchers processed today. (£{{ number_format($todayAmount, 2) }})</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">This Week's Vouchers</div>
                <div class="card-value text-primary">{{ $weekCount }}</div>
                <div class="card-note">Total vouchers processed this week. (£{{ number_format($weekAmount, 2) }})</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">All-Time Vouchers</div>
                <div class="card-value text-secondary">{{ $allCount }}</div>
                <div class="card-note">Total vouchers processed all-time. (£{{ number_format($allAmount, 2) }})</div>
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
                <table class="table table-hover w-100" id="pv-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Processed</th>
                                <th>Complete</th>
                                <th>Pending</th>
                                <th>Waiting</th>
                                <th>Expired</th>
                                <th>Multiple</th>
                                <th>Cancel</th>
                                <th>Voucher State</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="pvDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voucher Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Charity</th>
                                <th>Charity Balance</th>
                                <th>Donor</th>
                                <th>Balance Limit.</th>
                                <th>Std Amount</th>
                                <th>Voucher</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Checked By Dev</th>
                                <th>Voucher State</th>
                                <th>Dev Note</th>
                            </tr>
                        </thead>
                        <tbody id="pv-details-content"></tbody>
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
    let table = $('#pv-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('developer.process_voucher_monitoring') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            {data: 'date', name: 'date', searchable: false},
            {data: 'total_processed', name: 'total_processed', orderable: false, searchable: false},
            {data: 'complete_count', name: 'complete_count', orderable: false, searchable: false},
            {data: 'pending_count', name: 'pending_count', orderable: false, searchable: false},
            {data: 'waiting_count', name: 'waiting_count', orderable: false, searchable: false},
            {data: 'expired_count', name: 'expired_count', orderable: false, searchable: false},
            {data: 'multiple_count', name: 'multiple_count', orderable: false, searchable: false},
            {data: 'cancel_count', name: 'cancel_count', orderable: false, searchable: false},
            {data: 'voucher_state', name: 'voucher_state', orderable: false, searchable: false},
            {data: 'total_amount', name: 'total_amount', searchable: false}
        ],
        order: [[0, 'desc']],
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

    // Handle View Details Click
    $(document).on('click', '.view-pv-details', function() {
        const data = {
            type: $(this).data('type'),
            date: $(this).data('date')
        };

        $('#pv-details-content').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');
        $('#pvDetailsModal').modal('show');

        $.get("{{ route('developer.process_voucher_monitoring.details') }}", data, function(response) {
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="11" class="text-center">No vouchers found</td></tr>';
            } else {
                response.forEach(item => {
                    html += `<tr>
                        <td>${item.charity}</td>
                        <td>${item.charity_balance}</td>
                        <td>${item.donor}</td>
                        <td>${item.donor_balance}</td>
                        <td>${item.standard_amount}</td>
                        <td>${item.voucher}</td>
                        <td>${item.amount}</td>
                        <td>${item.status}</td>
                        <td>${item.check_btn}</td>
                        <td>${item.voucher_state}</td>
                        <td style="min-width: 200px;">${item.dev_note_input}</td>
                    </tr>`;
                });
            }
            $('#pv-details-content').html(html);
        }).fail(function() {
            $('#pv-details-content').html('<tr><td colspan="11" class="text-center text-danger">Error loading details.</td></tr>');
        });
    });

    // Handle Check Voucher
    $(document).on('click', '.check-voucher', function() {
        var id = $(this).data('id');
        var btn = $(this);
        $.ajax({
            url: `/admin/dev-team/process-voucher-monitoring/check/${id}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if(res.success) {
                    btn.removeClass('btn-outline-primary check-voucher').addClass('btn-success uncheck-voucher').html('<i class="fas fa-check"></i> Checked');
                }
            },
            error: function() {
                alert('Error checking voucher.');
            }
        });
    });

    // Handle Uncheck Voucher
    $(document).on('click', '.uncheck-voucher', function() {
        var id = $(this).data('id');
        var btn = $(this);
        $.ajax({
            url: `/admin/dev-team/process-voucher-monitoring/uncheck/${id}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if(res.success) {
                    btn.removeClass('btn-success uncheck-voucher').addClass('btn-outline-primary check-voucher').html('<i class="fas fa-check"></i> Check');
                }
            },
            error: function() {
                alert('Error unchecking voucher.');
            }
        });
    });


    // Handle Note Save on change (when clicking outside or pressing Enter)
    $(document).on('change', '.pv-note-input', function() {
        var id = $(this).data('id');
        var note = $(this).val();
        var input = $(this);

        $.ajax({
            url: `/admin/dev-team/process-voucher-monitoring/update-note/${id}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', dev_note: note },
            success: function(res) {
                input.css('border-color', '#28a745');
                setTimeout(function() { input.css('border-color', ''); }, 1000);
            },
            error: function() {
                input.css('border-color', '#dc3545');
                alert('Error saving dev note.');
            }
        });
    });


});
</script>
@endsection