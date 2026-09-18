@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="mdi:bank-transfer"></span> 
            <div class="mx-2">Manage Payout Transaction</div>
        </div>
    </section>

    <div class="container-fluid mt-3">
        <!-- Search Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search by Transaction ID</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dev.searchTransaction') }}" method="POST">
                    @csrf         
                    <div class="row align-items-end justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="t_id" class="text-muted small mb-1">Transaction ID (t_id)</label>
                                <input class="form-control form-control-sm" id="t_id" name="t_id" type="text" value="{{ old('t_id', $t_id ?? '') }}" placeholder="e.g., Out-1234567890-5" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary w-100" type="submit">
                                <span class="iconify" data-icon="mdi:magnify" style="margin-bottom: 2px;"></span>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Results Table Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Transaction Details</h6>
            </div>
            <div class="card-body p-0">
                @if(isset($transaction))
                    @if($transaction)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr class="text-uppercase text-muted small">
                                        <th class="py-3 pl-4">Txn ID</th>
                                        <th class="py-3">Charity</th>
                                        <th class="py-3">Type</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Created At</th>
                                        <th class="py-3 text-center pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="pl-4 font-weight-bold">{{ $transaction->t_id }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                {{ $transaction->charity->name ?? 'N/A' }}
                                            </div>
                                            <div class="small text-muted">
                                                ID: {{ $transaction->charity_id }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($transaction->t_type == 'Out')
                                                <span class="badge badge-danger">Out (Payout)</span>
                                            @else
                                                <span class="badge badge-success">{{ $transaction->t_type }}</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-primary">£{{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            @if($transaction->status == 1)
                                                <span class="badge badge-success">Success</span>
                                            @else
                                                <span class="badge badge-secondary">Pending/Failed</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M, Y - h:i A') }}</td>
                                        <td class="text-center pr-4">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Update Button -->
                                                <button type="button" class="btn btn-outline-warning d-flex align-items-center" 
                                                        data-toggle="modal" 
                                                        data-target="#updateTransactionModal"
                                                        data-id="{{ $transaction->id }}"
                                                        data-tid="{{ $transaction->t_id }}"
                                                        data-amount="{{ $transaction->amount }}">
                                                    <span class="iconify mr-1" data-icon="mdi:pencil-outline"></span>
                                                    Update
                                                </button>

                                                <!-- Delete Button Form -->
                                                <form action="{{ route('dev.deleteTransaction', $transaction->id) }}" method="POST" onsubmit="return confirm('DANGER! Are you absolutely sure? This will permanently delete the transaction, reverse the charity balance, and delete the batch record.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger d-flex align-items-center" type="submit" title="Delete Transaction">
                                                        <span class="iconify mr-1" data-icon="mdi:trash-can-outline"></span>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <span class="iconify text-danger mb-3" data-icon="mdi:alert-circle-outline" style="font-size: 3rem; opacity: 0.5;"></span>
                            <h6 class="text-danger">No transaction found with ID: "{{ $t_id }}"</h6>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <span class="iconify text-muted mb-3" data-icon="mdi:clipboard-text-search" style="font-size: 3rem; opacity: 0.5;"></span>
                        <h6 class="text-muted">Enter a Transaction ID above to search.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Update Transaction Modal -->
<div class="modal fade" id="updateTransactionModal" tabindex="-1" role="dialog" aria-labelledby="updateTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="updateTransactionModalLabel">
                    <span class="iconify mr-1" data-icon="mdi:currency-gbp"></span>
                    Update Transaction Amount
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updateTransactionForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="text-muted small">Changing the amount will automatically adjust the Charity's balance based on the difference.</p>
                    <div class="form-group">
                        <label for="txn_id_display" class="font-weight-bold">Transaction ID</label>
                        <input type="text" class="form-control form-control-sm" id="txn_id_display" disabled>
                    </div>
                    <div class="form-group">
                        <label for="amount" class="font-weight-bold">New Amount (£)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-sm btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning text-white">
                        <span class="iconify mr-1" data-icon="mdi:content-save-outline"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Handle Update Modal Data Injection
        $('#updateTransactionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id');
            var tid = button.data('tid');
            var amount = button.data('amount');
            
            var modal = $(this);
            modal.find('#txn_id_display').val(tid);
            modal.find('#amount').val(amount);
            
            // Set the form action to the correct route
            var actionUrl = '{{ route('dev.updateTransaction', ':id') }}';
            actionUrl = actionUrl.replace(':id', id);
            modal.find('#updateTransactionForm').attr('action', actionUrl);
        });
    });
</script>
@endsection