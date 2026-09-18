@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> 
            <div class="mx-2">Standing Order Detail Record</div>
        </div>
    </section>

    <div class="container-fluid mt-3">
        <!-- Search Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search Filters</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dev.checkStandingDonation') }}" method="POST">
                    @csrf         
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="fromDate" class="text-muted small mb-1">From Date</label>
                                <!-- Updated value to check request() so it refills after redirect -->
                                <input class="form-control form-control-sm" id="fromDate" name="fromDate" type="date" value="{{ old('fromDate', request('fromDate', $fromDate ?? '')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="toDate" class="text-muted small mb-1">To Date</label>
                                <!-- Updated value to check request() so it refills after redirect -->
                                <input class="form-control form-control-sm" id="toDate" name="toDate" type="date" value="{{ old('toDate', request('toDate', $toDate ?? '')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary w-100" type="submit">
                                <span class="iconify" data-icon="mdi:magnify" style="margin-bottom: 2px;"></span>
                                Search Records
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

        <!-- Results Table Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Donation Details</h6>
                @if(isset($chktran) && $chktran->isNotEmpty())
                    <span class="badge badge-pill badge-secondary">Total Records: {{ $chktran->count() }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if(isset($chktran))
                    @if($chktran->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr class="text-uppercase text-muted small">
                                        <th class="py-3 pl-4">ID</th>
                                        <th class="py-3">User Details</th>
                                        <th class="py-3">Charity Details</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Instalment Date</th>
                                        <th class="py-3">Created At</th>
                                        <th class="py-3 text-center pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chktran as $item)
                                        <tr>
                                            <td class="pl-4">{{ $item->id }}</td>
                                            
                                            <!-- User Details Column -->
                                            <td>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                    {{ $item->user->name ?? 'N/A' }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $item->user->email ?? 'N/A' }}
                                                </div>
                                                <div class="small text-muted">
                                                    Acc: {{ $item->user->accountno ?? 'N/A' }}
                                                </div>
                                            </td>

                                            <!-- Charity Details Column -->
                                            <td>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                    {{ $item->charity->name ?? 'N/A' }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $item->charity->email ?? 'N/A' }}
                                                </div>
                                            </td>

                                            <td class="font-weight-bold text-success">£{{ number_format($item->amount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->instalment_date)->format('d M, Y') }}</td>
                                            <td class="small text-muted">{{ \Carbon\Carbon::parse($item->created_at)->format('d M, Y - h:i A') }}</td>
                                            <td class="text-center pr-4">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- View Button -->
                                                    <button class="btn btn-outline-info d-flex align-items-center view-tran-btn" 
                                                            data-toggle="modal" 
                                                            data-target="#transactionModal"
                                                            data-id="{{ $item->matched_transaction->id ?? 'N/A' }}"
                                                            data-tranid="{{ $item->matched_transaction->t_id ?? 'N/A' }}"
                                                            data-amount="{{ $item->matched_transaction->amount ?? 'N/A' }}"
                                                            data-type="{{ $item->matched_transaction->t_type ?? 'N/A' }}"
                                                            data-title="{{ $item->matched_transaction->title ?? 'N/A' }}"
                                                            data-status="{{ $item->matched_transaction->status ?? 'N/A' }}"
                                                            data-date="{{ isset($item->matched_transaction->created_at) ? \Carbon\Carbon::parse($item->matched_transaction->created_at)->format('d M, Y - h:i A') : 'N/A' }}"
                                                            title="View Transaction">
                                                        <span class="iconify" data-icon="mdi:eye-outline"></span>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('dev.deleteStandingDonation', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure? This will delete the standing donation detail AND its associated transaction.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        
                                                        <!-- HIDDEN INPUTS ADDED HERE: So controller remembers the dates -->
                                                        <input type="hidden" name="fromDate" value="{{ request('fromDate', $fromDate ?? '') }}">
                                                        <input type="hidden" name="toDate" value="{{ request('toDate', $toDate ?? '') }}">

                                                        <button class="btn btn-outline-danger d-flex align-items-center" type="submit" title="Delete Record">
                                                            <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <span class="iconify text-muted mb-3" data-icon="mdi:database-search-outline" style="font-size: 3rem; opacity: 0.5;"></span>
                            <h6 class="text-muted">No standing orders found between these dates.</h6>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <span class="iconify text-muted mb-3" data-icon="mdi:calendar-search" style="font-size: 3rem; opacity: 0.5;"></span>
                        <h6 class="text-muted">Please select a date range and search to view records.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transactionModalLabel">
                    <span class="iconify mr-1" data-icon="mdi:receipt-text-outline"></span>
                    User Transaction Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted text-right pr-3" style="width: 40%;">ID</th>
                            <td id="tran_id" class="font-weight-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted text-right pr-3" style="width: 40%;">Transaction ID</th>
                            <td id="transaction_id" class="font-weight-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted text-right pr-3">Title</th>
                            <td id="tran_title"></td>
                        </tr>
                        <tr>
                            <th class="text-muted text-right pr-3">Type</th>
                            <td id="tran_type"></td>
                        </tr>
                        <tr>
                            <th class="text-muted text-right pr-3">Amount</th>
                            <td id="tran_amount" class="text-success font-weight-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted text-right pr-3">Status</th>
                            <td id="tran_status">
                                <span class="badge badge-success">Success (1)</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted text-right pr-3">Date</th>
                            <td id="tran_date" class="small"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-sm btn-light border" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Handle View Button Click
        $('.view-tran-btn').on('click', function() {
            var id = $(this).data('id');
            var tranid = $(this).data('tranid');
            var title = $(this).data('title');
            var type = $(this).data('type');
            var amount = $(this).data('amount');
            var status = $(this).data('status');
            var date = $(this).data('date');

            // Format Amount
            if(amount !== 'N/A') {
                $('#tran_amount').text('£' + parseFloat(amount).toFixed(2));
            } else {
                $('#tran_amount').text('N/A');
            }

            $('#tran_id').text(id);
            $('#transaction_id').text(tranid);
            $('#tran_title').text(title);
            $('#tran_type').text(type);
            
            // Convert status to badge with proper styling
            if (status == 1) {
                $('#tran_status').html('<span class="badge badge-success">Success (1)</span>');
            } else if (status === 0 || status === '0') {
                $('#tran_status').html('<span class="badge badge-danger">Failed/Cancelled (0)</span>');
            } else {
                $('#tran_status').html('<span class="badge badge-secondary">' + status + '</span>');
            }
            
            $('#tran_date').text(date);
        });
    });
</script>
@endsection