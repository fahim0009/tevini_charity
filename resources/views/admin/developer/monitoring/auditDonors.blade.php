@extends('layouts.admin')

@section('content')

<style>
    .hover-underline:hover {
        text-decoration: underline !important;
        color: #0056b3 !important; 
    }
    
    /* Table Font Size Reduce (Fixed for DataTables ScrollX) */
    #transaction-table {
        font-size: 0.75rem !important; 
    }
    #transaction-table tbody td {
        padding: 8px 10px !important;
        white-space: nowrap;
    }
    
    /* Target the hidden duplicate header created by DataTables scrollX */
    .dataTables_scrollHead thead th {
        font-size: 0.75rem !important;
        padding: 8px 10px !important;
        white-space: nowrap;
    }
    
    .text-muted {
        opacity: 0.5;
        font-weight: normal;
    }

    /* Compact 8-Column Card Layout */
    .cards-row-8 {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    .card-col-8 {
        flex: 1 0 11%; 
        min-width: 130px;
    }
    
    .mini-card {
        background: #fff;
        padding: 12px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 3px solid #4a90e2;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .mini-card .m-title {
        font-size: 0.7rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .mini-card .m-value {
        font-size: 1.1rem; 
        font-weight: 700;
        color: #333;
        line-height: 1.2;
        margin-bottom: 6px;
    }
    .mini-card .m-note {
        font-size: 0.6rem; 
        color: #a0aec0;
        margin-top: auto;
        font-style: italic;
        line-height: 1.3;
    }

    .section-title {
        font-size: 1rem; 
        font-weight: bold; 
        color: #2d3748;
        margin-top: 20px; 
        margin-bottom: 15px; 
        border-bottom: 2px solid #e2e8f0; 
        padding-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title i { color: #4a90e2; font-size: 1rem; }
</style>


<div class="dashboard-content p-4">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Donor Transactions Audit</h4>
        </div>
    </section>

    <!-- ===== Today's Summary ===== -->
    <div class="section-title">
        <i class="fas fa-calendar-day"></i> Today's Summary
    </div>
    <div class="cards-row-8">
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #28a745;">
                <div class="m-title">Total</div>
                <div class="m-value text-success">£{{ number_format($todayDonation, 2) }}</div>
                <div class="m-note">Total of all donations made today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #007bff;">
                <div class="m-title">Online</div>
                <div class="m-value text-primary">£{{ number_format($todayOnline, 2) }}</div>
                <div class="m-note">Sum of online donations today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #17a2b8;">
                <div class="m-title">Standing</div>
                <div class="m-value" style="color: #17a2b8;">£{{ number_format($todayStanding, 2) }}</div>
                <div class="m-note">Sum of standing order donations today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #dc3545;">
                <div class="m-title">Voucher</div>
                <div class="m-value text-danger">£{{ number_format($todayVoucher, 2) }}</div>
                <div class="m-note">Sum of voucher transactions today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #e83e8c;">
                <div class="m-title">Card</div>
                <div class="m-value" style="color: #e83e8c;">£{{ number_format($todayCard, 2) }}</div>
                <div class="m-note">Sum of card-based donations today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #6c757d;">
                <div class="m-title">Campaign</div>
                <div class="m-value text-secondary">£{{ number_format($todayCampaign, 2) }}</div>
                <div class="m-note">Sum of campaign donations today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #ffc107;">
                <div class="m-title">Topup</div>
                <div class="m-value text-warning">£{{ number_format($todayTopup, 2) }}</div>
                <div class="m-note">Total amount added by donors today.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #6f42c1;">
                <div class="m-title">Commission</div>
                <div class="m-value" style="color: #6f42c1;">£{{ number_format($todayCommission, 2) }}</div>
                <div class="m-note">Total commission collected today.</div>
            </div>
        </div>
    </div>

    <!-- ===== This Week's Summary ===== -->
    <div class="section-title">
        <i class="fas fa-calendar-week"></i> This Week's Summary
    </div>
    <div class="cards-row-8">
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #28a745;">
                <div class="m-title">Total</div>
                <div class="m-value text-success">£{{ number_format($weekDonation, 2) }}</div>
                <div class="m-note">Total of all donations this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #007bff;">
                <div class="m-title">Online</div>
                <div class="m-value text-primary">£{{ number_format($weekOnline, 2) }}</div>
                <div class="m-note">Sum of online donations this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #17a2b8;">
                <div class="m-title">Standing</div>
                <div class="m-value" style="color: #17a2b8;">£{{ number_format($weekStanding, 2) }}</div>
                <div class="m-note">Sum of standing orders this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #dc3545;">
                <div class="m-title">Voucher</div>
                <div class="m-value text-danger">£{{ number_format($weekVoucher, 2) }}</div>
                <div class="m-note">Sum of voucher transactions this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #e83e8c;">
                <div class="m-title">Card</div>
                <div class="m-value" style="color: #e83e8c;">£{{ number_format($weekCard, 2) }}</div>
                <div class="m-note">Sum of card donations this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #6c757d;">
                <div class="m-title">Campaign</div>
                <div class="m-value text-secondary">£{{ number_format($weekCampaign, 2) }}</div>
                <div class="m-note">Sum of campaign donations this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #ffc107;">
                <div class="m-title">Topup</div>
                <div class="m-value text-warning">£{{ number_format($weekTopup, 2) }}</div>
                <div class="m-note">Total amount added by donors this week.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #6f42c1;">
                <div class="m-title">Commission</div>
                <div class="m-value" style="color: #6f42c1;">£{{ number_format($weekCommission, 2) }}</div>
                <div class="m-note">Total commission collected this week.</div>
            </div>
        </div>
    </div>

    <!-- ===== All-Time Category Totals ===== -->
    <div class="section-title">
        <i class="fas fa-database"></i> All-Time Category Totals
    </div>
    <div class="cards-row-8">
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #007bff;">
                <div class="m-title">Online</div>
                <div class="m-value text-primary">£{{ number_format($totalOnline, 2) }}</div>
                <div class="m-note">All-time total sum of online donations.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #17a2b8;">
                <div class="m-title">Standing</div>
                <div class="m-value" style="color: #17a2b8;">£{{ number_format($totalStanding, 2) }}</div>
                <div class="m-note">All-time total sum of standing orders.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #dc3545;">
                <div class="m-title">Voucher</div>
                <div class="m-value text-danger">£{{ number_format($totalVoucher, 2) }}</div>
                <div class="m-note">All-time total sum of voucher transactions.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #e83e8c;">
                <div class="m-title">Card</div>
                <div class="m-value" style="color: #e83e8c;">£{{ number_format($totalCard, 2) }}</div>
                <div class="m-note">All-time total sum of card donations.</div>
            </div>
        </div>
        <div class="card-col-8">
            <div class="mini-card" style="border-left-color: #6c757d;">
                <div class="m-title">Campaign</div>
                <div class="m-value text-secondary">£{{ number_format($totalCampaign, 2) }}</div>
                <div class="m-note">All-time total sum of campaign donations.</div>
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
                        <option value="monthly" @if(request('filter_type') == 'monthly') selected @endif>Monthly</option>
                        <option value="daily" @if(request('filter_type') == 'daily') selected @endif>Daily</option>
                        <option value="weekly" @if(request('filter_type') == 'weekly') selected @endif>Weekly</option>
                        <option value="last7" @if(request('filter_type') == 'last7') selected @endif>Last 7 days</option>
                        <option value="custom" @if(request('filter_type') == 'custom') selected @endif>Date to Date</option>
                        <option value="all" @if(request('filter_type') == 'all') selected @endif>ALL</option>
                    </select>
                </div>
                <div class="col-md-2 date-range-wrap" style="display: {{ request('filter_type') == 'custom' ? 'block' : 'none' }};">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm" value="{{ request('fromDate') }}">
                </div>
                <div class="col-md-2 date-range-wrap" style="display: {{ request('filter_type') == 'custom' ? 'block' : 'none' }};">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm" value="{{ request('toDate') }}">
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

        // --- NEW COLUMNS ---
        {data: 'book_order_sum', name: 'book_order_sum', title: 'Book Order', searchable: false},
        {data: 'delivery_cost_sum', name: 'delivery_cost_sum', title: 'Delivery Cost', searchable: false},
        {data: 'tdf_sum', name: 'tdf_sum', title: 'TDF', searchable: false},
        {data: 'gift_aid_sum', name: 'gift_aid_sum', title: 'Gift AID', searchable: false},
        // -------------------
        
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

    if (window.location.search.includes('filter_type=custom')) {
        // Ensure date range fields are visible
        $('.date-range-wrap').show();
        // Trigger a draw to load the specific date data immediately
        table.draw();
    }

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
@endsection>