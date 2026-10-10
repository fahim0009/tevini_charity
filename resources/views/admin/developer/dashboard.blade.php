@extends('layouts.admin')

@section('title', 'Developer Dashboard')

@section('css')

    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">

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

    <!-- Stat Cards -->
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

    <!-- Main Content Grid -->
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
                            <td>${{ number_format($txn->amount, 2) }}</td>
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