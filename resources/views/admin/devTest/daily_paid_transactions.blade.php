@extends('layouts.admin')

@section('content')
<div class="dashboard-content">

    {{-- Page Header --}}
    <section class="profile purchase-status">
        <div class="title-section d-flex align-items-center">
            <span class="iconify" data-icon="mdi:bank-transfer-out" data-width="25"></span>
            <h4 class="mx-2 mb-0">Daily Paid Transactions</h4>
        </div>
    </section>

    <div class="container-fluid mt-4">

        {{-- Success Alert --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="iconify me-1" data-icon="mdi:check-circle-outline" data-width="16"></span>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">
                            <span class="iconify text-warning" data-icon="mdi:bank-outline" data-width="24"></span>
                        </div>
                        <div>
                            <div class="text-muted small">Pending Bank Payment (Total)</div>
                            <div class="fw-bold fs-5">{{ number_format($totalAmount, 2) }} BDT</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">
                            <span class="iconify text-primary" data-icon="mdi:format-list-numbered" data-width="24"></span>
                        </div>
                        <div>
                            <div class="text-muted small">Records Found</div>
                            <div class="fw-bold fs-5">{{ $count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('dailyPaidTransaction') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">From Date</label>
                        <input type="date" name="fromDate" class="form-control" value="{{ $fromDate ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">To Date</label>
                        <input type="date" name="toDate" class="form-control" value="{{ $toDate ?? '' }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <span class="iconify me-1" data-icon="mdi:filter-outline" data-width="16"></span>
                            Filter
                        </button>
                        @if($fromDate || $toDate)
                            <a href="{{ route('dailyPaidTransaction') }}" class="btn btn-outline-secondary">
                                <span class="iconify me-1" data-icon="mdi:close-circle-outline" data-width="16"></span>
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Active filter notice --}}
        @if($fromDate && $toDate)
            <div class="alert alert-info py-2 small mb-3">
                <span class="iconify me-1" data-icon="mdi:information-outline" data-width="16"></span>
                Showing results from <strong>{{ $fromDate }}</strong> to <strong>{{ $toDate }}</strong>.
            </div>
        @endif

        {{-- Bulk Update Form wraps the table --}}
        <form method="POST" action="{{ route('updateTransactionDates') }}" id="bulkUpdateForm">
            @csrf

            {{-- Sticky Action Bar (hidden until a checkbox is checked) --}}
            <div id="bulkActionBar" class="card border-warning border shadow-sm mb-3 d-none">
                <div class="card-body d-flex align-items-center gap-3 flex-wrap bg-warning bg-opacity-10">
                    <span class="fw-semibold text-dark">
                        <span class="iconify me-1" data-icon="mdi:checkbox-marked-outline" data-width="18"></span>
                        <span id="selectedCount">0</span> row(s) selected
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-semibold small text-muted">New Date:</label>
                        <input type="datetime-local" name="new_date" id="new_date" class="form-control form-control-sm" style="width:220px;" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirmUpdate()">
                        <span class="iconify me-1" data-icon="mdi:calendar-edit" data-width="16"></span>
                        Update Selected Dates
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAll()">
                        <span class="iconify me-1" data-icon="mdi:close" data-width="16"></span>
                        Clear Selection
                    </button>
                </div>
            </div>

            {{-- Transactions Table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold">
                        <span class="iconify me-1" data-icon="mdi:table" data-width="18"></span>
                        Pending Bank Transactions
                    </span>
                    <span class="badge bg-warning text-dark">bank_payment_status = Pending</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="transactionsTable" class="table table-striped table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:42px;">
                                        <input type="checkbox" id="selectAll"
                                            class="form-check-input" title="Select / Deselect All">
                                    </th>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Charity</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th>Note</th>
                                    <th>Bank Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $index => $txn)
                                    <tr class="row-item">
                                        <td class="ps-3">
                                            <input type="checkbox" name="selected_ids[]"
                                                value="{{ $txn->id }}"
                                                class="form-check-input row-checkbox">
                                        </td>
                                        <td class="text-muted small">{{ $index + 1 }}</td>
                                        <td><code class="text-primary">{{ $txn->t_id }}</code></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $txn->charity->name ?? $txn->charity_id }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $txn->t_type }}</span>
                                        </td>
                                        <td class="text-end fw-semibold text-danger">
                                            {{ number_format($txn->amount, 2) }}
                                        </td>
                                        <td class="text-muted small">{{ $txn->note ?? '—' }}</td>
                                        <td>
                                            @if($txn->bank_payment_status == 0)
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-success">Paid</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <span class="iconify d-block mx-auto mb-2"
                                                data-icon="mdi:database-off-outline" data-width="40"></span>
                                            No transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </form>{{-- end bulk form --}}

    </div>
</div>


@endsection

@section('script')

<script>
    // ── DataTable ──────────────────────────────────────────────────
    $(document).ready(function () {
        $('#transactionsTable').DataTable({
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100, { label: 'All', value: -1 }],
            order: [[8, 'desc']], // default sort: Created At descending
            columnDefs: [
                { orderable: false, searchable: false, targets: 0 } // checkbox column
            ],
            language: {
                search: '',
                searchPlaceholder: '🔍 Search by charity, transaction ID, date...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ transactions',
                paginate: {
                    previous: '‹',
                    next:     '›'
                }
            },
            // Re-bind checkboxes after DataTables redraws (pagination/search)
            drawCallback: function () {
                bindCheckboxes();
            }
        });
    });

    // ── Checkbox Logic ─────────────────────────────────────────────
    const selectAll     = document.getElementById('selectAll');
    const bulkBar       = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        selectedCount.textContent = checked.length;
        bulkBar.classList.toggle('d-none', checked.length === 0);

        document.querySelectorAll('.row-item').forEach(row => {
            const cb = row.querySelector('.row-checkbox');
            if (cb) row.classList.toggle('table-primary', cb.checked);
        });
    }

    function bindCheckboxes() {
        // Re-query after every DataTable redraw
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.removeEventListener('change', onRowCheckboxChange);
            cb.addEventListener('change', onRowCheckboxChange);
        });
    }

    function onRowCheckboxChange() {
        const all = document.querySelectorAll('.row-checkbox');
        selectAll.checked = [...all].every(c => c.checked);
        updateBar();
    }

    selectAll.addEventListener('change', function () {
        // Only check visible (current page) rows
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBar();
    });

    function clearAll() {
        selectAll.checked = false;
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        updateBar();
    }

    function confirmUpdate() {
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        const date    = document.getElementById('new_date').value;
        if (!date) {
            alert('Please select a new date & time first.');
            return false;
        }
        return confirm(`Update created_at to "${date}" for ${checked} transaction(s)?\n\nThis cannot be undone.`);
    }

    // Initial bind
    bindCheckboxes();
</script>
@endsection