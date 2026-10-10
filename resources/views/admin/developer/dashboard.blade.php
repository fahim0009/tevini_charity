@extends('layouts.admin')

@section('title', 'Developer Dashboard')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
    <style>
        /* Additional inline styles for monitoring status colors */
        .status-badge-danger { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .status-badge-warning { background: #fffaf0; color: #dd6b20; border: 1px solid #fbd38d; }
        .status-badge-success { background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; }
        .status-badge-info { background: #ebf8ff; color: #3182ce; border: 1px solid #90cdf4; }
        .text-danger-dev { color: #c53030; font-weight: 600; }
        .dev-alert-card { border-left: 4px solid #c53030; }
        .dev-warning-card { border-left: 4px solid #dd6b20; }
        .dev-success-card { border-left: 4px solid #38a169; }
    </style>
@endsection

@section('content')
<div class="dev-dashboard">
    
    <!-- Header -->
    <div class="dev-header">
        <h1>Developer Dashboard</h1>
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Admin Dashboard
        </a>
    </div>

    <!-- Basic Stat Cards -->
    <div class="dev-stats-grid">
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($totalTransactions) }}</h3>
                <p>Total Transactions</p>
            </div>
            <div class="icon-box bg-blue">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($totalUserTransactions) }}</h3>
                <p>User Transactions</p>
            </div>
            <div class="icon-box bg-green">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($pendingStandingDonations) }}</h3>
                <p>Pending Standing Dons</p>
            </div>
            <div class="icon-box bg-yellow">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($totalDonors) }}</h3>
                <p>Donor</p>
            </div>
            <div class="icon-box bg-purple">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>{{ number_format($totalCharities) }}</h3>
                <p>Charities</p>
            </div>
            <div class="icon-box bg-purple">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
        </div>
    </div>

    <!-- Donation & Standing Donation Amount Cards -->
    <div class="dev-stats-grid" style="margin-top: 25px; margin-bottom: 25px;">
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($todaysOnlineDonation, 2) }}</h3>
                <p>Today's Online Donation</p>
            </div>
            <div class="icon-box bg-blue">
                <i class="fas fa-donate"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($weeklyOnlineDonation, 2) }}</h3>
                <p>This Week Online Donation</p>
            </div>
            <div class="icon-box bg-green">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($todaysStandingDonation, 2) }}</h3>
                <p>Today's Standing Donation</p>
            </div>
            <div class="icon-box bg-yellow">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($weeklyStandingDonation, 2) }}</h3>
                <p>This Week Standing Donation</p>
            </div>
            <div class="icon-box bg-purple">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="dev-stat-card">
            <div class="info">
                <h3>£{{ number_format($tomorrowStandingAmount, 2) }}</h3>
                <p>Tomorrow's Scheduled Standing</p>
            </div>
            <div class="icon-box bg-purple">
                <i class="fas fa-forward"></i>
            </div>
        </div>
    </div>

    <!-- Monitoring Section Grid 1 -->
    <div class="dev-content-grid" style="margin-top: 25px; margin-bottom: 25px;">
        
        <!-- Left Column: Failure & Sync Monitoring -->
        <div>
            <!-- Stripe Sync Issues -->
            <div class="dev-card dev-alert-card">
                <div class="dev-card-header" style="background: #fff5f5; border-color: #feb2b2;">
                    <h3 style="color: #c53030;">
                        <i class="fas fa-exclamation-circle mr-2"></i> Stripe Sync Issues
                        <small style="font-size: 0.8rem; font-weight: 400;">(Payment Success, DB Pending)</small>
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stripeSyncIssues as $donation)
                            <tr>
                                <td>#{{ $donation->id }}</td>
                                <td>{{ $donation->user->name ?? 'Guest' }}</td>
                                <td>£{{ number_format($donation->amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('d M, Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 20px;">No sync issues found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Will Fail Due To Insufficient Balance -->
            <div class="dev-card dev-alert-card">
                <div class="dev-card-header" style="background: #fff5f5; border-color: #feb2b2;">
                    <h3 style="color: #c53030;">
                        <i class="fas fa-money-bill-wave mr-2"></i> Will Fail: Insufficient Balance
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Avail. Limit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($failingStandingDonations as $standing)
                            <tr>
                                <td>#{{ $standing->id }}</td>
                                <td>{{ $standing->user->name ?? 'N/A' }}</td>
                                <td>£{{ number_format($standing->amount, 2) }}</td>
                                <td class="text-danger-dev">£{{ number_format($standing->user->getAvailableLimit(), 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 20px;">No failing donations predicted</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Pending & Stuck Monitoring -->
        <div>
            <!-- Stuck Standing Donations -->
            <div class="dev-card dev-warning-card">
                <div class="dev-card-header" style="background: #fffaf0; border-color: #fbd38d;">
                    <h3 style="color: #dd6b20;">
                        <i class="fas fa-history mr-2"></i> Stuck Standing Donations
                        <small style="font-size: 0.8rem; font-weight: 400;">(Date Passed, Not Processed)</small>
                    </h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stuckStandingDonations as $standing)
                            <tr>
                                <td>#{{ $standing->id }}</td>
                                <td>{{ $standing->user->name ?? 'N/A' }}</td>
                                <td>£{{ number_format($standing->amount, 2) }}</td>
                                <td>
                                    @if($standing->payments == 1)
                                        <span class="dev-badge status-badge-warning">Fixed</span>
                                    @else
                                        <span class="dev-badge status-badge-info">Continuous</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 20px;">No stuck standing orders</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Donations -->
            <div class="dev-card">
                <div class="dev-card-header">
                    <h3>Pending Donations <small style="font-size: 0.8rem; font-weight: 400;">(Status: 0)</small></h3>
                </div>
                <div class="dev-card-body" style="padding: 0;">
                    <table class="dev-table">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor</th>
                                <th>Charity</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingDonations as $donation)
                            <tr>
                                <td>#{{ $donation->id }}</td>
                                <td>{{ $donation->user->name ?? 'Guest' }}</td>
                                <td>{{ $donation->charity->name ?? 'N/A' }}</td>
                                <td>£{{ number_format($donation->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 20px;">No pending donations</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Monitoring Section Grid 2: Weekly Data Tables -->
    <div class="dev-content-grid" style="margin-top: 25px; margin-bottom: 25px;">
        
        <!-- Left Column: This Week's Online Donations -->
        <div class="dev-card">
            <div class="dev-card-header">
                <h3>This Week's Online Donations</h3>
            </div>
            <div class="dev-card-body" style="padding: 0;">
                <table class="dev-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($weeklyDonationsData as $donation)
                        <tr>
                            <td>#{{ $donation->id }}</td>
                            <td>{{ $donation->user->name ?? 'Guest' }}</td>
                            <td>{{ $donation->charity->name ?? 'N/A' }}</td>
                            <td>£{{ number_format($donation->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('d M, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No donations this week</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: This Week's Standing Donations -->
        <div class="dev-card">
            <div class="dev-card-header">
                <h3>This Week's Standing Donations</h3>
            </div>
            <div class="dev-card-body" style="padding: 0;">
                <table class="dev-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($weeklyStandingData as $standing)
                        <tr>
                            <td>#{{ $standing->id }}</td>
                            <td>{{ $standing->user->name ?? 'N/A' }}</td>
                            <td>{{ $standing->charity->name ?? 'N/A' }}</td>
                            <td>£{{ number_format($standing->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($standing->created_at)->format('d M, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No standing donations this week</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Monitoring Section Grid 3: Next Week Schedule -->
    <div class="dev-content-grid" style="margin-top: 25px; margin-bottom: 25px;">
        
        <div class="dev-card dev-success-card">
            <div class="dev-card-header" style="background: #f0fff4; border-color: #9ae6b4;">
                <h3 style="color: #38a169;">
                    <i class="fas fa-calendar-alt mr-2"></i> Next Week's Scheduled Standing Donations
                </h3>
            </div>
            <div class="dev-card-body" style="padding: 0;">
                <table class="dev-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Interval</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nextWeekStandingDonations as $standing)
                        <tr>
                            <td>#{{ $standing->id }}</td>
                            <td>{{ $standing->user->name ?? 'N/A' }}</td>
                            <td>{{ $standing->charity->name ?? 'N/A' }}</td>
                            <td>£{{ number_format($standing->amount, 2) }}</td>
                            <td>
                                @if($standing->payments == 1)
                                    <span class="dev-badge status-badge-warning">Fixed</span>
                                @else
                                    <span class="dev-badge status-badge-info">Continuous</span>
                                @endif
                            </td>
                            <td>{{ $standing->interval }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align: center; padding: 20px;">No scheduled standing donations for next week</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Main Content Grid (Recent Transactions & Tools) -->
    <div class="dev-content-grid">
        
        <!-- Left Column: Recent Transactions -->
        <div class="dev-card">
            <div class="dev-card-header">
                <h3>Recent Transactions</h3>
            </div>
            <div class="dev-card-body" style="padding: 0;">
                <table class="dev-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Charity</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $txn)
                        <tr>
                            <td>#{{ $txn->id }}</td>
                            <td>{{ $txn->user->name ?? 'Guest/N/A' }}</td>
                            <td>{{ $txn->charity->name ?? 'N/A' }}</td>
                            <td><span class="dev-badge">{{ $txn->t_type }}</span></td>
                            <td>£{{ number_format($txn->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('d M, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Quick Tools & History -->
        <div>
            <!-- Quick Tools -->
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
                        <a href="{{ route('dev.checkStandingDonation') }}" class="dev-btn">
                            <span><i class="fas fa-redo mr-2"></i> Standing Donation Check</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('admin.transactionDelete') }}" class="dev-btn">
                            <span><i class="fas fa-trash mr-2"></i> Transaction Delete Tool</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('dailyPaidTransaction') }}" class="dev-btn">
                            <span><i class="fas fa-calendar-day mr-2"></i> Daily Paid Transactions</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('userTransactionDate') }}" class="dev-btn">
                            <span><i class="fas fa-calendar-alt mr-2"></i> User Transaction Date</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('charityPaymentCreate') }}" class="dev-btn">
                            <span><i class="fas fa-money-bill mr-2"></i> Charity Payment</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('dev.fixOldDates') }}" class="dev-btn dev-alert-btn" 
                           onclick="return confirm('This will recalculate all business dates. Continue?')">
                            <span><i class="fas fa-wrench mr-2"></i> Fix Old Dates</span>
                            <i class="fas fa-exclamation-triangle"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cutoff History -->
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
                            @if($cutoffHistory->isEmpty())
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px;">No history found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection