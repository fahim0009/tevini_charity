@extends('layouts.admin')

@section('content')

<style>
    #voucher-table td {
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
        font-size: 14px; color: #555; font-weight: 600; margin-bottom: 5px;
    }
    .summary-card .card-value {
        font-size: 24px; font-weight: 700; color: #333;
    }
    .summary-card .card-note {
        font-size: 12px; color: #888; margin-top: 8px; font-style: italic;
    }
    .section-title {
        font-size: 18px; font-weight: bold; color: #333;
        margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px;
    }
</style>

<div class="dashboard-content p-4">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Voucher Book Orders Audit</h4>
        </div>
    </section>

        <!-- ===== Barcode Status Summary ===== -->
    <div class="section-title">Barcode Status Summary</div>
    <div class="row mb-4">
        <!-- Daily Card -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Daily Status</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <span class="text-success fw-bold fs-5">{{ $dailyWith }}</span><br>
                        <small class="text-muted">With Barcode</small>
                    </div>
                    <div class="text-end">
                        <span class="text-danger fw-bold fs-5">{{ $dailyWithout }}</span><br>
                        <small class="text-muted">Without Barcode</small>
                    </div>
                </div>
                <div class="card-note">Today's barcode processing status.</div>
            </div>
        </div>

        <!-- Weekly Card -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Weekly Status</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <span class="text-success fw-bold fs-5">{{ $weeklyWith }}</span><br>
                        <small class="text-muted">With Barcode</small>
                    </div>
                    <div class="text-end">
                        <span class="text-danger fw-bold fs-5">{{ $weeklyWithout }}</span><br>
                        <small class="text-muted">Without Barcode</small>
                    </div>
                </div>
                <div class="card-note">This week's barcode processing status.</div>
            </div>
        </div>

        <!-- Monthly Card -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Monthly Status</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <span class="text-success fw-bold fs-5">{{ $monthlyWith }}</span><br>
                        <small class="text-muted">With Barcode</small>
                    </div>
                    <div class="text-end">
                        <span class="text-danger fw-bold fs-5">{{ $monthlyWithout }}</span><br>
                        <small class="text-muted">Without Barcode</small>
                    </div>
                </div>
                <div class="card-note">This month's barcode processing status.</div>
            </div>
        </div>

        <!-- All-Time Card -->
        <div class="col-md-3 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">All-Time Status</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <span class="text-success fw-bold fs-5">{{ $allWith }}</span><br>
                        <small class="text-muted">With Barcode</small>
                    </div>
                    <div class="text-end">
                        <span class="text-danger fw-bold fs-5">{{ $allWithout }}</span><br>
                        <small class="text-muted">Without Barcode</small>
                    </div>
                </div>
                <div class="card-note">All-time barcode processing status.</div>
            </div>
        </div>
    </div>

    <!-- ===== Today's Summary ===== -->
    <div class="section-title">Today's Summary</div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Today's Orders</div>
                <div class="card-value text-success">{{ $todayOrders }}</div>
                <div class="card-note">Total number of voucher orders placed today.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Today's Revenue</div>
                <div class="card-value text-primary">£{{ number_format($todayAmount, 2) }}</div>
                <div class="card-note">Total monetary value of today's orders.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Today's Delivery Charge</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($todayDelivery, 2) }}</div>
                <div class="card-note">Total delivery fees collected today.</div>
            </div>
        </div>
    </div>

    <!-- ===== This Week's Summary ===== -->
    <div class="section-title">This Week's Summary</div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #28a745;">
                <div class="card-title">Week's Orders</div>
                <div class="card-value text-success">{{ $weekOrders }}</div>
                <div class="card-note">Total number of voucher orders this week.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #007bff;">
                <div class="card-title">Week's Revenue</div>
                <div class="card-value text-primary">£{{ number_format($weekAmount, 2) }}</div>
                <div class="card-note">Total monetary value of this week's orders.</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="summary-card" style="border-left-color: #17a2b8;">
                <div class="card-title">Week's Delivery Charge</div>
                <div class="card-value" style="color: #17a2b8;">£{{ number_format($weekDelivery, 2) }}</div>
                <div class="card-note">Total delivery fees collected this week.</div>
            </div>
        </div>
    </div>

    <!-- ===== All-Time Summary ===== -->
    <div class="section-title">All-Time Summary</div>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="border-left-color: #6c757d;">
                <div class="card-title">Total Orders</div>
                <div class="card-value text-secondary">{{ $totalOrders }}</div>
                <div class="card-note">All-time total number of voucher orders.</div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="border-left-color: #ffc107;">
                <div class="card-title">Total Revenue</div>
                <div class="card-value text-warning">£{{ number_format($totalAmount, 2) }}</div>
                <div class="card-note">All-time total monetary value of all orders.</div>
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
                <table class="table table-hover w-100" id="voucher-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Donor (Name & Email)</th>
                            <th>Order ID & Info</th>
                            <th>Amount</th>
                            <th>Barcodes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Barcodes Details Modal -->
<div class="modal fade" id="barcodesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Barcodes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Voucher Type</th>
                                <th>Barcode Number</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="barcodes-content"></tbody>
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
    let table = $('#voucher-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
        ajax: {
            url: "{{ route('developer.vouchers') }}",
            data: function (d) {
                d.filter_type = $('#filter_type').val();
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            {data: 'date', name: 'date', searchable: false},
            {data: 'donor', name: 'donor', searchable: true},
            {data: 'order_id', name: 'order_id', searchable: true},
            {data: 'amount', name: 'amount', searchable: false},
            {data: 'total_barcodes', name: 'total_barcodes', orderable: false, searchable: false},
            {data: 'status', name: 'status', searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
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

    // Handle View Barcodes Click
    $(document).on('click', '.view-barcodes', function() {
        const orderId = $(this).data('id');
        const url = "{{ route('developer.vouchers.barcodes', ':id') }}".replace(':id', orderId);

        $('#barcodes-content').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#barcodesModal').modal('show');

        $.get(url, function(response) {
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="4" class="text-center">No barcodes found for this order.</td></tr>';
            } else {
                response.forEach(item => {
                    html += `<tr>
                        <td>${item.voucher_type}</td>
                        <td><strong>${item.barcode}</strong></td>
                        <td>${item.amount}</td>
                        <td>${item.status}</td>
                    </tr>`;
                });
            }
            $('#barcodes-content').html(html);
        }).fail(function() {
            $('#barcodes-content').html('<tr><td colspan="4" class="text-center text-danger">Error loading barcodes.</td></tr>');
        });
    });

    // Handle View Details Click (Placeholder)
    $(document).on('click', '.view-order-details', function() {
        alert('Order details view will be added here later.');
    });
});
</script>
@endsection