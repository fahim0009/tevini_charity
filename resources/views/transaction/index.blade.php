@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status mb-4">
        <div class="title-section d-flex align-items-center">
            <span class="iconify fs-3" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mb-0 mx-2">Transaction Management</h4>
        </div>
    </section>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form id="filter-form" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm px-4">Apply Filter</button>
                    <button type="button" id="reset-filter" class="btn btn-light btn-sm px-4">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white pt-3">
            <ul class="nav nav-pills card-header-pills" id="transactionTabs">
                <li class="nav-item">
                    <button class="nav-link active" data-type="Out">Transactions Out</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-type="In">Transactions In</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-type="All">All Transactions</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="transaction-table">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Donor</th>
                            <th>Beneficiary</th>
                            <th>Source</th>
                            <th>Note</th>
                            <th>Amount</th>
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
    let currentType = 'Out';

    let table = $('#transaction-table').DataTable({
        processing: true,
        serverSide: true,
        // lengthMenu configuration
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 25,
        // Updated DOM: 
        // l = lengthMenu, B = Buttons, f = filter (search)
        // r = processing, t = table, i = info, p = pagination
        dom: '<"row mb-3"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4 text-center"B><"col-sm-12 col-md-4"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        ajax: {
            url: "{{ route('transaction') }}",
            data: function (d) {
                d.t_type = currentType;
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            {data: 'created_at', name: 'created_at'},
            {data: 't_id', name: 't_id'},
            {data: 'donor', name: 'user.name'},
            {data: 'beneficiary', name: 'charity.name'},
            {data: 'name', name: 'name'},
            {data: 'note', name: 'note'},
            {data: 'amount', name: 'amount', className: 'fw-bold text-dark'}
        ],
        order: [[0, 'desc']],
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-outline-secondary',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-outline-success',
                title: "Transaction_Report",
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-sm btn-outline-danger',
                title: "Transaction Report",
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':visible' },
                customize: function(doc) {
                    doc.styles.tableHeader = {
                        bold: true, fontSize: 10, fillColor: '#4d617e', color: 'white', alignment: 'center'
                    };
                    doc.defaultStyle.alignment = 'center';
                    // Adjusting widths for 7 columns
                    doc.content[1].table.widths = ['14%', '14%', '14%', '14%', '14%', '15%', '15%'];
                }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-outline-info',
                title: "Transaction Report",
                exportOptions: { columns: ':visible' }
            }
        ]
    });

    // Tab Switching Logic
    $('#transactionTabs .nav-link').on('click', function() {
        $('#transactionTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        currentType = $(this).data('type');
        table.draw();
    });

    // Filter Submission
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#reset-filter').on('click', function() {
        $('#fromDate, #toDate').val('');
        table.draw();
    });
});
</script>
@endsection