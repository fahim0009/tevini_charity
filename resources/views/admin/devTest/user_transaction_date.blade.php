@extends('layouts.admin')

@section('content')
<div class="dashboard-content">

    {{-- Page Header --}}
    <section class="profile purchase-status">
        <div class="title-section d-flex align-items-center">
            <span class="iconify" data-icon="mdi:clock-edit-outline" data-width="25"></span>
            <h4 class="mx-2 mb-0">Update User Transaction Date</h4>
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

        {{-- Search Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white py-3">
                <span class="fw-semibold">
                    <span class="iconify me-1" data-icon="mdi:magnify" data-width="18"></span>
                    Search by Transaction ID
                </span>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('userTransactionDate') }}" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Transaction ID (t_id)</label>
                        <input type="text" name="t_id" class="form-control"
                            placeholder="e.g. Out-1771518602-182"
                            value="{{ $t_id ?? '' }}" autofocus>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <span class="iconify me-1" data-icon="mdi:magnify" data-width="16"></span>
                            Search
                        </button>
                        @if($t_id)
                            <a href="{{ route('userTransactionDate') }}" class="btn btn-outline-secondary">
                                <span class="iconify me-1" data-icon="mdi:close" data-width="16"></span>
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Results --}}
        @if($t_id)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold">
                        <span class="iconify me-1" data-icon="mdi:table" data-width="18"></span>
                        Results for: <code class="text-warning">{{ $t_id }}</code>
                    </span>
                    <span class="badge bg-primary">{{ $results->count() }} record(s) found</span>
                </div>
                <div class="card-body p-0">
                    @if($results->isEmpty())
                        <div class="text-center text-muted py-5">
                            <span class="iconify d-block mx-auto mb-2"
                                data-icon="mdi:database-off-outline" data-width="40"></span>
                            No transactions found for <strong>{{ $t_id }}</strong>.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="resultTable" class="table table-striped table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">#</th>
                                        <th>ID</th>
                                        <th>T_ID</th>
                                        <th>Charity</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                        <th>Created At</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $index => $txn)
                                        <tr>
                                            <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                            <td><span class="badge bg-secondary">{{ $txn->id }}</span></td>
                                            <td><code class="text-primary">{{ $txn->t_id }}</code></td>
                                            <td>{{ $txn->charity->name ?? $txn->charity_id }}</td>
                                            <td><span class="badge bg-danger">{{ $txn->t_type }}</span></td>
                                            <td>
                                                @if($txn->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold text-danger">
                                                {{ number_format($txn->amount, 2) }}
                                            </td>
                                            <td class="small text-muted">
                                                {{ \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d H:i:s') }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-warning btn-sm edit-btn"
                                                    data-id="{{ $txn->id }}"
                                                    data-tid="{{ $txn->t_id }}"
                                                    data-datetime="{{ \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d\TH:i') }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editDateModal">
                                                    <span class="iconify me-1" data-icon="mdi:clock-edit-outline" data-width="14"></span>
                                                    Edit Date
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Edit Date Modal --}}
<div class="modal fade" id="editDateModal" tabindex="-1" aria-labelledby="editDateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" action="{{ route('updateUserTransactionDate') }}" id="updateDateForm">
                @csrf
                <input type="hidden" name="transaction_id" id="modal_transaction_id">
                <input type="hidden" name="t_id_ref" id="modal_t_id_ref">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="editDateModalLabel">
                        <span class="iconify me-1" data-icon="mdi:clock-edit-outline" data-width="20"></span>
                        Edit Transaction Date
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Transaction ID</label>
                        <input type="text" id="modal_t_id_display" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">New Date & Time</label>
                        <input type="datetime-local" name="new_datetime" id="modal_new_datetime"
                            class="form-control" required>
                        <div class="form-text text-muted">Current value is pre-filled. Change date/time as needed.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" onclick="return confirmModalUpdate()">
                        <span class="iconify me-1" data-icon="mdi:content-save-outline" data-width="16"></span>
                        Update Date & Time
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function () {

    // ── DataTable ──────────────────────────────────────────────────
    @if($results->count() > 0)
    $('#resultTable').DataTable({
        pageLength: 25,
        order: [[7, 'desc']],
        language: {
            search: '',
            searchPlaceholder: '🔍 Filter results...',
            info: 'Showing _START_ to _END_ of _TOTAL_ records',
            paginate: { previous: '‹', next: '›' }
        }
    });
    @endif

    // ── Populate Modal on Edit button click ────────────────────────
    document.querySelectorAll('.edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('modal_transaction_id').value  = this.dataset.id;
            document.getElementById('modal_t_id_ref').value        = this.dataset.tid;
            document.getElementById('modal_t_id_display').value    = this.dataset.tid;
            document.getElementById('modal_new_datetime').value    = this.dataset.datetime;
        });
    });

});

function confirmModalUpdate() {
    const datetime = document.getElementById('modal_new_datetime').value;
    const tid      = document.getElementById('modal_t_id_display').value;
    if (!datetime) {
        alert('Please select a date & time.');
        return false;
    }
    return confirm(`Update created_at to "${datetime.replace('T', ' ')}" for transaction:\n${tid}\n\nThis cannot be undone.`);
}
</script>
@endsection