@extends('layouts.admin')

@section('content')

<style>
    .hover-underline:hover {
        text-decoration: underline !important;
        color: #0056b3 !important; 
    }
    #transaction-table td {
        vertical-align: middle;
    }
    .text-muted {
        opacity: 0.5;
        font-weight: normal;
    }
    /* Card Styles */
    .summary-card {
        border-left: 4px solid #4a90e2;
        background: #fff;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        height: 100%;
    }
    .summary-card .card-title {
        font-size: 14px;
        color: #555;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .summary-card .card-value {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }
    .summary-card .card-note {
        font-size: 12px;
        color: #888;
        margin-top: 8px;
        font-style: italic;
    }
    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-top: 20px;
        margin-bottom: 15px;
        border-bottom: 2px solid #eee;
        padding-bottom: 5px;
    }
</style>

<div class="dashboard-content p-4">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Donor Transactions Audit</h4>
        </div>
    </section>

    <!-- ===== Today's Summary ===== -->
    <div class="section-title">Today's Summary</div>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Today's Donation</div>
                <div class="card-value text-success">£{{ number_format($todayDonation, 2) }}</div>
                <div class="card-note">Total of all donations made today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Today's Online</div>
                <div class="card-value text-primary">£{{ number_format($todayOnline, 2) }}</div>
                <div class="card-note">Sum of online donations today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Today's Standing</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($todayStanding, 2) }}</div>
                <div class="card-note">Sum of standing order donations today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #dc3545;">
                <div class="card-title">Today's Voucher</div>
                <div class="card-value text-danger">£{{ number_format($todayVoucher, 2) }}</div>
                <div class="card-note">Sum of voucher transactions today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #e83e8c;">
                <div class="card-title">Today's Card</div>
                <div class="card-value" style="color: #e83e8c;">£{{ number_format($todayCard, 2) }}</div>
                <div class="card-note">Sum of card-based donations today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Today's Campaign</div>
                <div class="card-value text-secondary">£{{ number_format($todayCampaign, 2) }}</div>
                <div class="card-note">Sum of campaign donations today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Today's Topup</div>
                <div class="card-value text-warning">£{{ number_format($todayTopup, 2) }}</div>
                <div class="card-note">Total amount added by donors today.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6f42c1;">
                <div class="card-title">Today's Commission</div>
                <div class="card-value" style="color: #6f42c1;">£{{ number_format($todayCommission, 2) }}</div>
                <div class="card-note">Total commission collected today.</div>
            </div>
        </div>
    </div>

    <!-- ===== This Week's Summary ===== -->
    <div class="section-title">This Week's Summary</div>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Week's Donation</div>
                <div class="card-value text-success">£{{ number_format($weekDonation, 2) }}</div>
                <div class="card-note">Total of all donations this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Week's Online</div>
                <div class="card-value text-primary">£{{ number_format($weekOnline, 2) }}</div>
                <div class="card-note">Sum of online donations this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Week's Standing</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($weekStanding, 2) }}</div>
                <div class="card-note">Sum of standing orders this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #dc3545;">
                <div class="card-title">Week's Voucher</div>
                <div class="card-value text-danger">£{{ number_format($weekVoucher, 2) }}</div>
                <div class="card-note">Sum of voucher transactions this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #e83e8c;">
                <div class="card-title">Week's Card</div>
                <div class="card-value" style="color: #e83e8c;">£{{ number_format($weekCard, 2) }}</div>
                <div class="card-note">Sum of card donations this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Week's Campaign</div>
                <div class="card-value text-secondary">£{{ number_format($weekCampaign, 2) }}</div>
                <div class="card-note">Sum of campaign donations this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Week's Topup</div>
                <div class="card-value text-warning">£{{ number_format($weekTopup, 2) }}</div>
                <div class="card-note">Total amount added by donors this week.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6f42c1;">
                <div class="card-title">Week's Commission</div>
                <div class="card-value" style="color: #6f42c1;">£{{ number_format($weekCommission, 2) }}</div>
                <div class="card-note">Total commission collected this week.</div>
            </div>
        </div>
    </div>

    <!-- ===== All-Time Category Totals ===== -->
    <div class="section-title">All-Time Category Totals</div>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Total Online</div>
                <div class="card-value text-primary">£{{ number_format($totalOnline, 2) }}</div>
                <div class="card-note">All-time total sum of online donations.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Total Standing</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($totalStanding, 2) }}</div>
                <div class="card-note">All-time total sum of standing orders.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #dc3545;">
                <div class="card-title">Total Voucher</div>
                <div class="card-value text-danger">£{{ number_format($totalVoucher, 2) }}</div>
                <div class="card-note">All-time total sum of voucher transactions.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #e83e8c;">
                <div class="card-title">Total Card</div>
                <div class="card-value" style="color: #e83e8c;">£{{ number_format($totalCard, 2) }}</div>
                <div class="card-note">All-time total sum of card donations.</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Total Campaign</div>
                <div class="card-value text-secondary">£{{ number_format($totalCampaign, 2) }}</div>
                <div class="card-note">All-time total sum of campaign donations.</div>
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

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="transaction-table"></table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="details-table">
                        <thead>
                            <tr>
                                <th>Charity</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="details-content"></tbody>
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
    const summaryColumns = [
        {data: 'date_group', name: 'date_group', title: 'Date', searchable: true},
        {data: 'donor_name', name: 'user.name', title: 'Donor', searchable: true},
        {data: 'online_sum', name: 'online_sum', title: 'Online', searchable: false},
        {data: 'standing_sum', name: 'standing_sum', title: 'Standing', searchable: false},
        {data: 'voucher_sum', name: 'voucher_sum', title: 'Voucher', searchable: false},
        {data: 'campaign_sum', name: 'campaign_sum', title: 'Campaign', searchable: false},
        {data: 'card_sum', name: 'card_sum', title: 'Card', searchable: false},
        {data: 'topup_sum', name: 'topup_sum', title: 'Topup', searchable: false},
        {data: 'commission_sum', name: 'commission_sum', title: 'Commission', searchable: false},
        {data: 'total_sum', name: 'total_sum', title: 'Total', className: 'fw-bold text-primary', searchable: false},
        {data: 'balance_sum', name: 'balance_sum', title: 'Balance', className: 'fw-bold', searchable: false}
    ];

    let table = $('#transaction-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('dev.auditDonors') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: summaryColumns,
        order: [[0, 'desc']],
        dom: '<"row mb-3"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
        ]
    });

    $('#filter_type').on('change', function() {
        if ($(this).val() == 'custom') {
            $('.date-range-wrap').show();
        } else {
            $('.date-range-wrap').hide();
        }
    });

    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#reset-filter').on('click', function() {
        $('#filter_type').val('monthly').trigger('change');
        $('#fromDate, #toDate').val('');
        table.draw();
    });

    $(document).on('click', '.view-details', function() {
        const data = {
            type: $(this).data('type'),
            donor_id: $(this).data('donor'),
            date: $(this).data('date')
        };

        $('#details-content').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#detailsModal').modal('show');

        $.get("{{ route('dev.auditDonors.details') }}", data, function(response) {
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="4" class="text-center">No transactions found</td></tr>';
            } else {
                response.forEach(item => {
                    html += `<tr>
                        <td>${item.charity}</td>
                        <td>${item.amount}</td>
                        <td>${item.ref}</td>
                        <td>${item.date}</td>
                    </tr>`;
                });
            }
            $('#details-content').html(html);
        });
    });
});
</script>
@endsection