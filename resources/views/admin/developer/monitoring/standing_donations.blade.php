@extends('layouts.admin')

@section('title', 'Standing Donation Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .status-badge-danger { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .status-badge-warning { background: #fffaf0; color: #dd6b20; border: 1px solid #fbd38d; }
        .status-badge-success { background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; }
        .status-badge-secondary { background: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; }
        
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
        <h1>Standing Donation Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="dev-stats-grid">
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ $totalStanding }}</h3>
                <p>Total Standing Orders</p>
            </div>
            <div class="icon-box bg-blue"><i class="fas fa-database"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ $activeStanding }}</h3>
                <p>Active Standing Orders</p>
            </div>
            <div class="icon-box bg-green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ $inactiveStanding }}</h3>
                <p>Inactive Standing Orders</p>
            </div>
            <div class="icon-box bg-yellow"><i class="fas fa-pause-circle"></i></div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ $completeStanding }}</h3>
                <p>Complete Standing Orders</p>
            </div>
            <div class="icon-box bg-purple"><i class="fas fa-flag-checkered"></i></div>
        </div>
    </div>

    <!-- Today's Standing Transactions -->
    <div class="dev-card" style="margin-top: 25px;">
        <div class="dev-card-header">
            <h3>Today's Standing Order Transactions</h3>
        </div>
        <div class="dev-card-body p-3" style="padding: 0;">
            <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Trans ID</th>
                        <th>Donor</th>
                        <th>Charity</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todaysStandingTransactions as $trans)
                    <tr>
                        <td>#{{ $trans->id }}</td>
                        <td>{{ $trans->user->name ?? 'N/A' }}</td>
                        <td>{{ $trans->charity->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($trans->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($trans->created_at)->format('d M, Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming 7 Days Standing Donations -->
    <div class="dev-card" style="margin-top: 25px;">
        <div class="dev-card-header">
            <h3>Upcoming 7 Days Standing Donations (Expected)</h3>
        </div>
        <div class="dev-card-body p-3" style="padding: 0;">
            <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Charity</th>
                        <th>Amount</th>
                        <th>Expected Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcoming7DaysDonations as $standing)
                    <tr>
                        <td>#{{ $standing->id }}</td>
                        <td>{{ $standing->user->name ?? 'N/A' }}</td>
                        <td>{{ $standing->charity->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($standing->amount, 2) }}</td>
                        <td>{{ $standing->expected_date }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Standing Donations -->
    <div class="dev-card" style="margin-top: 25px;">
        <div class="dev-card-header"><h3>All Standing Donations</h3></div>
        <div class="dev-card-body p-3" style="padding: 0;">
            <table class="dev-table dev-datatable table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Charity</th>
                        <th>Amount</th>
                        <th>Start Date</th>
                        <th>Interval</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($standingDonations as $standing)
                    <tr>
                        <td>#{{ $standing->id }}</td>
                        <td>{{ $standing->user->name ?? 'N/A' }}</td>
                        <td>{{ $standing->charity->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($standing->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($standing->starting)->format('d M, Y') }}</td>
                        <td>{{ $standing->interval }}</td>
                        <td>
                            @if($standing->status == 1)
                                <span class="dev-badge status-badge-success">Active</span>
                            @elseif($standing->status == 2)
                                <span class="dev-badge status-badge-secondary">Complete</span>
                            @else
                                <span class="dev-badge status-badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('.dev-datatable').DataTable({
            "pageLength": 10,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "language": {
                "search": "Filter records:",
                "emptyTable": "No data available in table",
                "infoEmpty": "Showing 0 to 0 of 0 entries"
            }
        });
    });
</script>
@endsection