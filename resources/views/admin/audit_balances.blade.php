@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Summary Report</h4>
        </div>
    </section>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form id="filter-form" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm" value="2026-02-09">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm" value="2026-04-22">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm px-4">Apply Filter</button>
                    <button type="button" id="reset-filter" class="btn btn-light btn-sm px-4">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="summary-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Charity</th>
                            <th>Online</th>
                            <th>Standing</th>
                            <th>Voucher</th>
                            <th>Campaign</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Keep your existing Modal code here --}}


<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                                <th>Donor/Charity</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="details-content">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="adjustActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Adjust Transaction</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="adj_tran_id">
                <input type="hidden" id="adj_charity_id">
                <input type="hidden" id="adj_tran_type">
                
                <div class="mb-3">
                    <label class="small fw-bold">Adjustment Type</label>
                    <select id="adj_type" class="form-select form-select-sm">
                        <option value="increment">Increment (+)</option>
                        <option value="decrement">Decrement (-)</option>
                    </select>
                </div>

                
                <div class="mb-3">
                    <label class="small fw-bold">Table</label>
                    <select id="table_name" class="form-select form-select-sm">
                        <option value="Charity">Charity</option>
                        <option value="Transaction">Transaction</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Adjustment Amount</label>
                    <input type="number" id="adj_amount" class="form-control form-control-sm" step="0.01" placeholder="0.00">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submitAdjustment" class="btn btn-primary btn-sm w-100">Process Adjustment</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
$(function() {
    const table = $('#summary-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        paging: false, // THIS REMOVES PAGINATION
        info: true,    // Shows "Showing X of X entries"
        ajax: {
            url: "{{ route('admin.audit.balances') }}",
            data: function (d) {
                d.t_type = 'Summary';
                // Pick up values from the inputs
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            {data: 'date_group', name: 'date_group', searchable: false}, // Dates are usually filtered, not searched
            {data: 'charity_name', name: 'charity.name', searchable: true}, // Keep this TRUE
            {data: 'online_sum', name: 'online_sum', searchable: false},
            {data: 'standing_sum', name: 'standing_sum', searchable: false},
            {data: 'voucher_sum', name: 'voucher_sum', searchable: false},
            {data: 'campaign_sum', name: 'campaign_sum', searchable: false},
            {data: 'paid_sum', name: 'paid_sum', className: 'text-success', searchable: false},
            {data: 'balance', name: 'balance', className: 'fw-bold text-primary', searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        dom: '<"row mb-3"<"col-md-4"B><"col-md-8"f>>rt<"row"<"col-md-12"i>>',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
        ]
    });

    $('#filter-form').on('submit', function(e) { 
        e.preventDefault(); 
        table.draw(); 
    });

    $('#reset-filter').on('click', function() { 
        $('#fromDate').val('2026-02-09'); // Reset to your specific start
        $('#toDate').val('2026-04-22');   // Reset to your specific end
        table.draw(); 
    });
});

    $(document).on('click', '.view-details', function() {

        const data = {
            type: $(this).data('type'),
            charity_id: $(this).data('charity'),
            date: $(this).data('date')
        };

        $('#details-content').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#detailsModal').modal('show');

        $.get("{{ route('audit.dailytransaction.details') }}", data, function(response) {
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="4" class="text-center">No transactions found</td></tr>';
            } else {
                response.forEach(item => {
                    html += `<tr>
                        <td>${item.donor}</td>
                        <td>${item.amount}</td>
                        <td>
                            ${item.ref} 
                            <button class="btn btn-sm btn-outline-secondary copy-btn" data-ref="${item.ref}" title="Copy ID">
                                <i class="fa fa-copy"></i> 
                            </button>
                        </td>
                        <td>${item.date} - (${item.status})</td>
                        <td>${item.adjustBtn}</td>
                    </tr>`;
                });
            }
            $('#details-content').html(html);
        });
    });


    // 1. When the Adjust button in the table is clicked
    $(document).on('click', '.adjust-transaction', function() {
        const btn = $(this);
        // Set hidden fields in the adjustment modal
        $('#adj_tran_id').val(btn.data('tid'));
        $('#adj_charity_id').val(btn.data('charity'));
        $('#adj_tran_type').val(btn.data('type'));
        
        // Reset inputs
        $('#adj_amount').val('');
        $('#adj_type').val('');

        // Show the adjustment modal
        $('#adjustActionModal').modal('show');
    });

    // 2. When the "Process Adjustment" button inside the modal is clicked
    $('#submitAdjustment').on('click', function() {
        const btn = $(this);
        const data = {
            _token: "{{ csrf_token() }}",
            transaction_id: $('#adj_tran_id').val(),
            charity_id: $('#adj_charity_id').val(),
            amount: $('#adj_amount').val(),
            type: $('#adj_type').val(),
            tran_type: $('#adj_tran_type').val(),
            table_name: $('#table_name').val()
        };

        if (!data.amount || data.amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            url: "{{ route('admin.audit.adjust') }}",
            method: 'POST',
            data: data,
            success: function(response) {
                if(response.success) {
                    alert('Success: ' + response.message);
                    $('#adjustActionModal').modal('hide');
                    $('#detailsModal').modal('hide'); // Close the parent details modal
                    if (typeof table !== 'undefined') table.draw(); // Refresh main dashboard
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error processing adjustment.');
                console.error(xhr.responseText);
            },
            complete: function() {
                btn.prop('disabled', false).text('Process Adjustment');
            }
        });
    });



</script>
@endsection