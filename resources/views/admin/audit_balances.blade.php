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
                    <input type="date" id="toDate" class="form-control form-control-sm" value="2026-02-22">
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
                                <th>Donor/Charity</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Date</th>
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
            {data: 'date_group', name: 'date_group'},
            {data: 'charity_name', name: 'charity.name'},
            {data: 'online_sum', name: 'online_sum'},
            {data: 'standing_sum', name: 'standing_sum'},
            {data: 'voucher_sum', name: 'voucher_sum'},
            {data: 'campaign_sum', name: 'campaign_sum'},
            {data: 'paid_sum', name: 'paid_sum', className: 'text-success'},
            {data: 'balance', name: 'balance', className: 'fw-bold text-primary'},
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
        $('#toDate').val('2026-02-22');   // Reset to your specific end
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
                    </tr>`;
                });
            }
            $('#details-content').html(html);
        });
    });
</script>
@endsection