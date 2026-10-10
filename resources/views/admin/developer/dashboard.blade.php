@extends('layouts.admin')

@section('title', 'Developer Dashboard')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .status-badge-danger { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .status-badge-warning { background: #fffaf0; color: #dd6b20; border: 1px solid #fbd38d; }
        .status-badge-success { background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; }
        .text-danger-dev { color: #c53030; font-weight: 600; }
        .dev-alert-card { border-left: 4px solid #c53030; }
        .dev-warning-card { border-left: 4px solid #dd6b20; }
        .table-note { font-size: 0.8rem; color: #718096; margin-top: 5px; font-weight: 400; }
        .donor-meta { display: block; font-size: 0.75rem; color: #718096; margin-top: 2px; }
        
        .dev-datatable thead th { 
            text-align: left; 
            padding: 12px 16px; 
            border-bottom: 1px solid #e2e8f0; 
        }
        .dev-datatable tbody td { 
            padding: 12px 16px; 
            border-bottom: 1px solid #e2e8f0; 
        }
        .dataTables_wrapper .dataTables_filter { float: right; }
        .dataTables_wrapper .dataTables_length { float: left; }
    </style>
@endsection

@section('content')
<div class="dev-dashboard">
    
    <div class="dev-header">
        <h1>Developer Dashboard</h1>
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Admin Dashboard
        </a>
    </div>

    <!-- Stat Cards: Transaction Summary -->
    <div class="dev-stats-grid">
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($successfulThisMonth) }}</h3>
                <p>Successful Trans. (This Month)</p>
            </div>
            <div class="icon-box bg-green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($unsuccessfulThisMonth) }}</h3>
                <p>Failed Trans. (This Month)</p>
            </div>
            <div class="icon-box bg-yellow"><i class="fas fa-times-circle"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($successfulLast30Days) }}</h3>
                <p>Successful Trans. (Last 30 Days)</p>
            </div>
            <div class="icon-box bg-blue"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($unsuccessfulLast30Days) }}</h3>
                <p>Failed Trans. (Last 30 Days)</p>
            </div>
            <div class="icon-box bg-purple"><i class="fas fa-calendar-times"></i></div>
        </div>
    </div>

    <!-- Stat Cards: Donation Summary -->
    <div class="dev-stats-grid" style="margin-top: 20px;">
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($todaysOnlineDonation, 2) }}</h3>
                <p>Today's Online Donation</p>
            </div>
            <div class="icon-box bg-blue"><i class="fas fa-donate"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($todaysStandingDonation, 2) }}</h3>
                <p>Today's Standing Donation</p>
            </div>
            <div class="icon-box bg-yellow"><i class="fas fa-clock"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($tomorrowStandingAmount, 2) }}</h3>
                <p>Tomorrow's Scheduled</p>
            </div>
            <div class="icon-box bg-purple"><i class="fas fa-forward"></i></div>
        </div>
    </div>

    <!-- Monitoring Pages Links -->
    <div class="dev-card" style="margin-top: 25px; margin-bottom: 25px;">
        <div class="dev-card-header">
            <h3>Monitoring Pages</h3>
        </div>
        <div class="dev-card-body">
            <div class="dev-btn-group" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                <a href="{{ route('developer.online_donations') }}" class="dev-btn">
                    <span><i class="fas fa-donate mr-2"></i> Online Donations</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('developer.standing_donations') }}" class="dev-btn">
                    <span><i class="fas fa-redo mr-2"></i> Standing Donations</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('developer.donors') }}" class="dev-btn">
                    <span><i class="fas fa-users mr-2"></i> Donors</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('developer.charities') }}" class="dev-btn">
                    <span><i class="fas fa-hand-holding-heart mr-2"></i> Charities</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('developer.voucher_books') }}" class="dev-btn">
                    <span><i class="fas fa-book mr-2"></i> Voucher Books</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('developer.vouchers') }}" class="dev-btn">
                    <span><i class="fas fa-ticket-alt mr-2"></i> Vouchers</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Issues & Monitoring Summary Grid -->
    <div class="dev-content-grid" style="margin-bottom: 25px;">
        
        <!-- Left Column: Failure & Sync Monitoring -->
        <div>
            <!-- Stripe Sync Issues -->
            <div class="dev-card dev-alert-card">
                <div class="dev-card-header" style="background: #fff5f5; border-color: #feb2b2;">
                    <h3 style="color: #c53030;">
                        <i class="fas fa-exclamation-circle mr-2"></i> Stripe Sync Issues
                        <p class="table-note">Note: Donations where Stripe payment succeeded but database status is still pending (0). Needs manual verification.</p>
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor Info</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stripeSyncIssues as $donation)
                            <tr>
                                <td>#{{ $donation->id }}</td>
                                <td>
                                    {{ $donation->user->name ?? 'Guest' }}
                                    @if($donation->user)
                                        <span class="donor-meta">Acct: {{ $donation->user->accountno ?? 'N/A' }}</span>
                                        <span class="donor-meta">{{ $donation->user->email ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>£{{ number_format($donation->amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('d M, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Will Fail Due To Insufficient Balance -->
            <div class="dev-card dev-alert-card">
                <div class="dev-card-header" style="background: #fff5f5; border-color: #feb2b2;">
                    <h3 style="color: #c53030;">
                        <i class="fas fa-money-bill-wave mr-2"></i> Will Fail: Insufficient Balance
                        <p class="table-note">Note: Active standing donations where the donor's available balance is less than the donation amount.</p>
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Info</th>
                                <th>Amount</th>
                                <th>Avail. Limit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($failingStandingDonations as $standing)
                            <tr>
                                <td>#{{ $standing->id }}</td>
                                <td>
                                    {{ $standing->user->name ?? 'N/A' }}
                                    @if($standing->user)
                                        <span class="donor-meta">Acct: {{ $standing->user->accountno ?? 'N/A' }}</span>
                                        <span class="donor-meta">{{ $standing->user->email ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>£{{ number_format($standing->amount, 2) }}</td>
                                <td class="text-danger-dev">£{{ number_format($standing->user->getAvailableLimit(), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Stuck & Pending -->
        <div>
            <!-- Stuck Standing Donations -->
            <div class="dev-card dev-warning-card">
                <div class="dev-card-header" style="background: #fffaf0; border-color: #fbd38d;">
                    <h3 style="color: #dd6b20;">
                        <i class="fas fa-history mr-2"></i> Stuck Standing Donations
                        <p class="table-note">Note: Active standing donations where the installment date has passed but payment was not processed.</p>
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Info</th>
                                <th>Amount</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stuckStandingDonations as $standing)
                            <tr>
                                <td>#{{ $standing->id }}</td>
                                <td>
                                    {{ $standing->user->name ?? 'N/A' }}
                                    @if($standing->user)
                                        <span class="donor-meta">Acct: {{ $standing->user->accountno ?? 'N/A' }}</span>
                                        <span class="donor-meta">{{ $standing->user->email ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>£{{ number_format($standing->amount, 2) }}</td>
                                <td>
                                    @if($standing->payments == 1)
                                        <span class="dev-badge status-badge-warning">Fixed</span>
                                    @else
                                        <span class="dev-badge status-badge-success">Continuous</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Negative Balance Users -->
            <div class="dev-card dev-alert-card">
                <div class="dev-card-header" style="background: #fff5f5; border-color: #feb2b2;">
                    <h3 style="color: #c53030;">
                        <i class="fas fa-money-bill-wave mr-2"></i> Negative Balance Users
                        <p class="table-note">Note: Users whose account balance has fallen below zero. Indicates data inconsistency or over-deduction.</p>
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Donor Info</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($negativeBalanceUsers as $user)
                            <tr>
                                <td>#{{ $user->id }}</td>
                                <td>
                                    {{ $user->name }}
                                    <span class="donor-meta">Acct: {{ $user->accountno ?? 'N/A' }}</span>
                                    <span class="donor-meta">{{ $user->email }}</span>
                                </td>
                                <td class="text-danger-dev">£{{ number_format($user->balance, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Tools & Cut-off History -->
    <div class="dev-content-grid">
        <div class="dev-card">
            <div class="dev-card-header">
                <h3>Developer Quick Tools</h3>
            </div>
            <div class="dev-card-body">
                <div class="dev-btn-group">
                    <a href="{{ route('dev.searchTransaction') }}" class="dev-btn">
                        <span><i class="fas fa-search mr-2"></i> Transaction Check</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="{{ route('admin.transactionDelete') }}" class="dev-btn">
                        <span><i class="fas fa-trash mr-2"></i> Transaction Delete Tool</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="{{ route('dev.fixOldDates') }}" class="dev-btn dev-alert-btn" onclick="return confirm('This will recalculate all business dates. Continue?')">
                        <span><i class="fas fa-wrench mr-2"></i> Fix Old Dates</span>
                        <i class="fas fa-exclamation-triangle"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="dev-card">
            <div class="dev-card-header">
                <h3>Cut-off History</h3>
            </div>
            <div class="dev-card-body" style="padding: 0;">
                <table class="dev-table dev-history-table">
                    <thead>
                        <tr>
                            <th>Effective Date</th>
                            <th>Cut-off Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cutoffHistory as $history)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($history->effective_date)->format('d M, Y') }}</td>
                            <td>{{ $history->cutoff_time }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('.dev-datatable').DataTable({
                "pageLength": 20,
                "order": [[ 0, "desc" ]],
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                "language": {
                    "search": "Filter records:",
                    "emptyTable": "No data available in table",
                    "infoEmpty": "Showing 0 to 0 of 0 entries"
                }
            });
        } else {
            console.error("jQuery or DataTables library is not loaded properly.");
        }
    });
</script>
@endsection