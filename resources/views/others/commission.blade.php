@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section d-flex align-items-center mb-4">
            <span class="iconify fs-2" data-icon="icon-park-outline:transaction"></span> 
            <h4 class="mx-2 mb-0">Financial Overview</h4>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <small class="text-muted">Total Commissions</small>
                    <h3 class="mb-0 text-primary">£{{ number_format($totalCommission ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills mb-3 custom-tabs" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-commission-tab" data-bs-toggle="pill" data-bs-target="#pills-commission" type="button" role="tab">
                    Commissions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-transactions-tab" data-bs-toggle="pill" data-bs-target="#pills-transactions" type="button" role="tab">
                    All Transactions
                </button>
            </li>
        </ul>

        <div class="tab-content card border-0 shadow-sm p-4" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-commission" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="commissionTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Donor Name</th>
                                <th>Commission</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="pills-transactions" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="transactionTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tran Id</th>
                                <th>Reference</th>
                                <th>User</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Professional styling tweaks */
    .custom-tabs .nav-link {
        color: #6c757d;
        background: #f8f9fa;
        margin-right: 10px;
        border-radius: 8px;
        font-weight: 500;
        padding: 10px 20px;
    }
    .custom-tabs .nav-link.active {
        background-color: #4d617e !important;
        color: white !important;
    }
    .dt-buttons .btn {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        font-size: 12px;
        margin-bottom: 15px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 5px 15px;
        border: 1px solid #dee2e6;
    }
</style>
@endsection

@section('script')
<script>

$(function () {
    // Shared Export Configuration
    const buttonCommon = {
        exportOptions: {
            format: {
                body: function (data, row, column, node) {
                    // Strip HTML tags (like <strong> or <span>) for Excel/PDF exports
                    return node.innerText || node.textContent;
                }
            }
        }
    };

    // 1. Commissions Table
    $('#commissionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('commission') }}",
            data: { type: 'commission' } // 🔥 Tells controller which data to send
        },
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 'donor', name: 'user.name' },
            { data: 'amount', name: 'commission' }
        ],
        pageLength: 100, 
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: 'Bfrtip',
        buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ]
    });

    // 2. All Transactions Table
    $('#transactionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('commission') }}",
            data: { type: 'all_transactions' } // 🔥 Tells controller which data to send
        },
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 't_id', name: 't_id' },
            { data: 'user', name: 'user.name' },
            { data: 'charity', name: 'charity.name' },
            { data: 'amount', name: 'amount' }
        ],
        pageLength: 100, 
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: 'Bfrtip',
        buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ]
    });
});



</script>
@endsection