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
                    <button class="nav-link active" data-type="Summary">Summary Report</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-type="Out">Transactions Out</button>
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
                    </table>

                    
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    let currentType = 'Summary'; // Default tab

    // Definition for Standard Tabs (In, Out, All)
    const standardColumns = [
        {data: 'created_at', name: 'created_at', title: 'Date'},
        {data: 't_id', name: 't_id', title: 'Transaction ID'},
        {data: 'donor', name: 'user.name', title: 'Donor'},
        {data: 'beneficiary', name: 'charity.name', title: 'Beneficiary'},
        {data: 'amount', name: 'amount', title: 'Amount', className: 'fw-bold'}
    ];

    // Definition for Summary Tab
    const summaryColumns = [
        {data: 'date_group', name: 'date_group', title: 'Date'},
        {data: 'charity_name', name: 'charity.name', title: 'Charity'},
        {data: 'online_sum', name: 'online_sum', title: 'Online'},
        {data: 'standing_sum', name: 'standing_sum', title: 'Standing'},
        {data: 'voucher_sum', name: 'voucher_sum', title: 'Voucher'},
        {data: 'campaign_sum', name: 'campaign_sum', title: 'Campaign'},
        {data: 'paid_sum', name: 'paid_sum', title: 'Paid', className: 'text-success'},
        {data: 'balance', name: 'balance', title: 'Balance', className: 'fw-bold text-primary'},
        {data: 'action', name: 'action', title: 'Action', orderable: false, searchable: false}
    ];

    function initTable(cols) {
        // We use 'destroy: true' and 'columns' with 'title' to rebuild the header
        return $('#transaction-table').DataTable({
            processing: true,
            serverSide: true,
            destroy: true, 
            scrollX: true, // Useful for the wide Summary table
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            ajax: {
                url: "{{ route('transaction') }}",
                data: function (d) {
                    d.t_type = currentType;
                    d.fromDate = $('#fromDate').val();
                    d.toDate = $('#toDate').val();
                }
            },
            columns: cols,
            order: [[0, 'desc']],
            dom: '<"row mb-3"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
            buttons: [
                { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
                { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
            ]
        });
    }

    // First initialization
    let table = initTable(summaryColumns);

    // Tab Switching Logic
    $('#transactionTabs .nav-link').on('click', function() {
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        currentType = $(this).data('type');
        
        // 1. Completely destroy the instance
        if ($.fn.DataTable.isDataTable('#transaction-table')) {
            $('#transaction-table').DataTable().destroy();
        }

        // 2. Wipe the HTML inside the table (clears the thead/tbody)
        $('#transaction-table').empty(); 

        // 3. Decide which columns to use
        let cols = (currentType === 'Summary') ? summaryColumns : standardColumns;

        // 4. Re-initialize (this will create new headers based on the 'title' key in the objects)
        table = initTable(cols);
    });

    // Filter Handlers
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#reset-filter').on('click', function() {
        $('#fromDate, #toDate').val('');
        table.draw();
    });

    // Listener for the Status Switch
    $(document).on('change', '.status-switch', function() {
        const isChecked = $(this).is(':checked');
        const charityId = $(this).data('charity-id');
        const date = $(this).data('date');
        const total = $(this).data('total');

        if (isChecked) {
            // Example: Logic to mark as Paid/Settled
            console.log(`Setting Charity ${charityId} as Settled for date ${date}`);
        } else {
            // Example: Logic to revert or mark as Pending
            console.log(`Setting Charity ${charityId} as Pending for date ${date}`);
        }
        
        // You can call an AJAX function here to update your database
    });


});
</script>
@endsection