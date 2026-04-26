@extends('layouts.admin')

@section('content')

<style>
    .form-check-input {
        cursor: pointer;
        width: 2.5em;
        height: 1.25em;
    }

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
</style>

<style>
    .row-checkbox {
        cursor: pointer;
        width: 1.2em;
        height: 1.2em;
    }

    #select-all {
        cursor: pointer;
        width: 1.2em;
        height: 1.2em;
    }
</style>

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
                    <button type="button" id="export-csv-btn" class="btn btn-outline-success btn-sm px-4 ms-2">
                        <i class="fas fa-file-csv me-1"></i> Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white pt-3">
            <ul class="nav nav-pills card-header-pills" id="transactionTabs">
                <li class="nav-item">
                    <button class="nav-link active" data-type="Summary">New Summary</button>
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
                <li class="nav-item">
                    <button class="nav-link" data-type="PreviousSummary">Previous Summary</button>
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
    let currentType = 'Summary';

    // Definition for Standard Tabs (In, Out, All)
    const standardColumns = [
        {data: 'created_at', name: 'created_at', title: 'Date'},
        {data: 't_id', name: 't_id', title: 'Transaction ID'},
        {data: 'donor', name: 'user.name', title: 'Donor'},
        {data: 'beneficiary', name: 'charity.name', title: 'Beneficiary'},
        {data: 'amount', name: 'amount', title: 'Amount', className: 'fw-bold'}
    ];

    // Definition for Summary Tab (with checkbox column)
    const summaryColumns = [
        {data: 'checkbox', name: 'checkbox', title: '<input type="checkbox" id="select-all" class="form-check-input">', orderable: false, searchable: false, className: 'text-center', width: '50px'},
        {data: 'date_group', name: 'date_group', title: 'Date', searchable: true},
        {data: 'charity_name', name: 'charity.name', title: 'Charity', searchable: true},
        {data: 'online_sum', name: 'online_sum', title: 'Online', searchable: false},
        {data: 'standing_sum', name: 'standing_sum', title: 'Standing', searchable: false},
        {data: 'voucher_sum', name: 'voucher_sum', title: 'Voucher', searchable: false},
        {data: 'campaign_sum', name: 'campaign_sum', title: 'Campaign', searchable: false},
        {data: 'paid_sum', name: 'paid_sum', title: 'Paid', className: 'text-success', searchable: false},
        {data: 'balance', name: 'balance', title: 'Summary', className: 'fw-bold text-primary', searchable: false},
        {data: 'action', name: 'action', title: 'Action', orderable: false, searchable: false}
    ];

    function initTable(cols) {
        return $('#transaction-table').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
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
            order: [[1, 'desc']], // Order by Date column (index 1 because checkbox is 0)
            columnDefs: [
                {
                    targets: 0, // Checkbox column
                    render: function(data, type, row, meta) {
                        // Only render for summary types
                        if (currentType === 'Summary' || currentType === 'PreviousSummary') {
                            return `<input type="checkbox" class="row-checkbox form-check-input" 
                                data-date="${row.raw_date}" 
                                data-charity-id="${row.charity_id}" 
                                data-amount="${row.raw_total}">`;
                        }
                        return '';
                    }
                }
            ],
            dom: '<"row mb-3"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
            buttons: [
                { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
                { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', orientation: 'landscape' }
            ],
            drawCallback: function() {
                // Reset select-all checkbox on page change
                $('#select-all').prop('checked', false);
                updateExportBtn();
            }
        });
    }

    // First initialization
    let table = initTable(summaryColumns);

    // Show/hide export button based on tab type
    function updateExportBtnVisibility() {
        if (currentType === 'Summary' || currentType === 'PreviousSummary') {
            $('#export-csv-btn').show();
        } else {
            $('#export-csv-btn').hide();
        }
    }

    // Update export button state
    function updateExportBtn() {
        const checkedCount = $('.row-checkbox:checked').length;
        const btn = $('#export-csv-btn');
        
        if (checkedCount > 0) {
            btn.html(`<i class="fas fa-file-csv me-1"></i> Export CSV (${checkedCount})`);
            btn.removeClass('btn-outline-success').addClass('btn-success');
        } else {
            btn.html(`<i class="fas fa-file-csv me-1"></i> Export CSV`);
            btn.removeClass('btn-success').addClass('btn-outline-success');
        }
    }

    // Tab Switching Logic
    $('#transactionTabs .nav-link').on('click', function() {
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        currentType = $(this).data('type');
        
        if ($.fn.DataTable.isDataTable('#transaction-table')) {
            $('#transaction-table').DataTable().destroy();
        }

        $('#transaction-table').empty();

        let cols = (currentType === 'Summary' || currentType === 'PreviousSummary') 
                ? summaryColumns 
                : standardColumns;

        table = initTable(cols);
        updateExportBtnVisibility();
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

    // Select All Checkbox
    $(document).on('change', '#select-all', function() {
        const isChecked = $(this).is(':checked');
        // Only select checkboxes on current page
        $('.row-checkbox').prop('checked', isChecked);
        updateExportBtn();
    });

    // Individual Row Checkbox
    $(document).on('change', '.row-checkbox', function() {
        // Update select-all state
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#select-all').prop('checked', false).prop('indeterminate', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all').prop('checked', true).prop('indeterminate', false);
        } else {
            $('#select-all').prop('checked', false).prop('indeterminate', true);
        }
        
        updateExportBtn();
    });

    // Export CSV Button Click
    $(document).on('click', '#export-csv-btn', function() {
        const selectedItems = [];
        
        $('.row-checkbox:checked').each(function() {
            selectedItems.push({
                date: $(this).data('date'),
                charity_id: $(this).data('charity-id'),
                amount: $(this).data('amount')
            });
        });
        
        if (selectedItems.length === 0) {
            alert('Please select at least one row to export.');
            return;
        }
        
        console.log('Selected Items for CSV Export:', selectedItems);
        
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Exporting...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('transaction.export-csv') }}",
            method: 'POST',
            data: {
                items: selectedItems,
                _token: "{{ csrf_token() }}"
            },
            success: function(response, status, xhr) {
                const blob = new Blob([xhr.responseText], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'summary-export.csv';
                a.click();
                window.URL.revokeObjectURL(url);
            },
            error: function(xhr) {
                console.error('Export Error:', xhr.responseText);
                alert('Error occurred during export.');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Listener for the Status Switch
    $(document).on('change', '.status-switch', function() {
        const el = $(this);
        const isChecked = el.is(':checked');
        const data = {
            charity_id: el.data('charity-id'),
            date: el.data('date'),
            total: el.data('total'),
            status: isChecked,
            _token: "{{ csrf_token() }}"
        };

        const actionText = isChecked ? "mark this as PAID?" : "REVERT this payment? (This will restore charity balance)";

        if (confirm(`Are you sure you want to ${actionText}`)) {
            $.ajax({
                url: "{{ route('transaction.toggle') }}",
                method: "POST",
                data: data,
                success: function(response) {
                    if(response.success) {
                        $('#transaction-table').DataTable().ajax.reload(null, false);
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                        el.prop('checked', !isChecked);
                    }
                },
                error: function() {
                    alert('Server error occurred.');
                    el.prop('checked', !isChecked);
                }
            });
        } else {
            el.prop('checked', !isChecked);
        }
    });

    // View Details Modal
    $(document).on('click', '.view-details', function() {
        const data = {
            type: $(this).data('type'),
            charity_id: $(this).data('charity'),
            date: $(this).data('date')
        };

        $('#details-content').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#detailsModal').modal('show');

        $.get("{{ route('dailytransaction.details') }}", data, function(response) {
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

    // Copy Button
    $(document).on('click', '.copy-btn', function() {
        const refId = $(this).data('ref');
        const $btn = $(this);
        
        navigator.clipboard.writeText(refId).then(() => {
            const originalText = $btn.html();
            $btn.addClass('btn-success').removeClass('btn-outline-secondary').text('Copied!');
            
            setTimeout(() => {
                $btn.removeClass('btn-success').addClass('btn-outline-secondary').html(originalText);
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy text.');
        });
    });

    // Initial visibility
    updateExportBtnVisibility();
});
</script>
@endsection