@extends('layouts.admin')

@section('title', 'Developer Dashboard')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        /* ===== MAIN CONTAINER ===== */
        .dev-dashboard-wrap {
            padding: 20px;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        .dev-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .dev-header-bar h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        .dev-back-link {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: #4a90e2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .dev-back-link:hover {
            background: #357abd;
            color: white;
            text-decoration: none;
        }

        /* ===== SECTION DIVIDER ===== */
        .dev-section {
            margin-bottom: 30px;
        }
        .dev-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
            padding-left: 12px;
            border-left: 4px solid #4a90e2;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dev-section-title i {
            color: #4a90e2;
        }

        /* ===== STAT CARDS GRID ===== */
        .stat-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border-left: 4px solid #4a90e2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .stat-card-box .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #2d3748;
        }
        .stat-card-box .stat-info p {
            font-size: 0.75rem;
            color: #718096;
            margin: 0;
        }
        .stat-card-box .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            flex-shrink: 0;
        }
        .bg-green { background: linear-gradient(135deg, #48bb78, #38a169); }
        .bg-yellow { background: linear-gradient(135deg, #ed8936, #dd6b20); }
        .bg-blue { background: linear-gradient(135deg, #4299e1, #3182ce); }
        .bg-purple { background: linear-gradient(135deg, #9f7aea, #805ad5); }
        .bg-red { background: linear-gradient(135deg, #f56565, #e53e3e); }
        .bg-teal { background: linear-gradient(135deg, #38b2ac, #319795); }
        .bg-pink { background: linear-gradient(135deg, #ed64a6, #d53f8c); }
        .bg-gray { background: linear-gradient(135deg, #718096, #4a5568); }

        /* ===== MONITORING LINKS GRID ===== */
        .monitoring-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .monitoring-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #2d3748;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .monitoring-link:hover {
            background: #f7fafc;
            border-color: #4a90e2;
            color: #4a90e2;
            transform: translateX(4px);
        }
        .monitoring-link i:first-child {
            margin-right: 8px;
            color: #4a90e2;
        }
        .monitoring-link i:last-child {
            color: #cbd5e0;
            font-size: 0.75rem;
        }

        /* ===== CARD STYLES ===== */
        .dev-panel {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .dev-panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #f7fafc;
        }
        .dev-panel-header h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dev-panel-header .table-note {
            font-size: 0.75rem;
            color: #718096;
            font-weight: 400;
            margin-top: 4px;
            display: block;
        }
        .dev-panel-body {
            padding: 0;
        }
        
        /* Alert Variants */
        .panel-alert .dev-panel-header { background: #fff5f5; border-bottom-color: #feb2b2; }
        .panel-alert .dev-panel-header h3 { color: #c53030; }
        .panel-warning .dev-panel-header { background: #fffaf0; border-bottom-color: #fbd38d; }
        .panel-warning .dev-panel-header h3 { color: #dd6b20; }
        .panel-alert { border-top: 3px solid #c53030; }
        .panel-warning { border-top: 3px solid #dd6b20; }

        /* ===== TABLE STYLES ===== */
        .table-responsive-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .dev-data-table {
            width: 100% !important;
            border-collapse: collapse;
        }
        .dev-data-table thead th {
            background: #f7fafc;
            padding: 10px 14px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .dev-data-table tbody td {
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #2d3748;
            border-bottom: 1px solid #edf2f7;
            white-space: nowrap;
        }
        .dev-data-table tbody tr:hover {
            background: #f7fafc;
        }
        .donor-info-cell strong {
            display: block;
            color: #2d3748;
        }
        .donor-meta {
            display: block;
            font-size: 0.7rem;
            color: #718096;
            margin-top: 2px;
        }

        /* ===== BADGES ===== */
        .badge-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-success { background: #c6f6d5; color: #22543d; }
        .badge-danger { background: #fed7d7; color: #742a2a; }
        .badge-warning { background: #feebc8; color: #744210; }
        .badge-info { background: #bee3f8; color: #2a4365; }
        .badge-secondary { background: #e2e8f0; color: #2d3748; }
        .badge-purple { background: #e9d8fd; color: #5b21b6; }

        .text-red { color: #c53030; font-weight: 600; }
        .text-orange { color: #dd6b20; font-weight: 600; }

        /* ===== FILTER BUTTONS ===== */
        .filter-btn-group {
            display: inline-flex;
            gap: 4px;
            background: #edf2f7;
            padding: 4px;
            border-radius: 6px;
        }
        .filter-btn {
            padding: 6px 14px;
            border: none;
            background: transparent;
            color: #4a5568;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-btn:hover {
            background: #e2e8f0;
        }
        .filter-btn.active {
            background: #4a90e2;
            color: white;
        }

        /* ===== TOPUP TABLE FOOTER ===== */
        .topup-footer {
            background: #f7fafc;
            font-weight: 700;
        }
        .topup-footer td {
            padding: 12px 14px !important;
            font-size: 0.85rem !important;
        }

        /* ===== QUICK TOOLS ===== */
        .quick-tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            padding: 20px;
        }
        .quick-tool-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #2d3748;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .quick-tool-btn:hover {
            background: #edf2f7;
            border-color: #4a90e2;
            color: #4a90e2;
            text-decoration: none;
            transform: translateX(4px);
        }
        .quick-tool-btn.alert-btn {
            border-color: #feb2b2;
            background: #fff5f5;
            color: #c53030;
        }
        .quick-tool-btn.alert-btn:hover {
            background: #fed7d7;
            color: #9b2c2c;
        }

        /* ===== CUTOFF HISTORY TABLE ===== */
        .cutoff-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cutoff-table thead th {
            background: #f7fafc;
            padding: 10px 14px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
        }
        .cutoff-table tbody td {
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #2d3748;
            border-bottom: 1px solid #edf2f7;
        }

        /* ===== LOADING SPINNER ===== */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        /* ===== DATA TABLES OVERRIDE ===== */
        .dataTables_wrapper {
            padding: 10px;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 10px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.8rem;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 10px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.8rem;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 10px;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.75rem;
            color: #718096;
            padding-top: 8px;
        }
    </style>
@endsection

@section('content')
<div class="dev-dashboard-wrap">
    
    <!-- Header -->
    <div class="dev-header-bar">
        <h1><i class="fas fa-code mr-2"></i> Developer Dashboard</h1>
        <a href="{{ route('admin.dashboard') }}" class="dev-back-link">
            <i class="fas fa-arrow-left mr-1"></i> Back to Admin Dashboard
        </a>
    </div>

    <!-- ===== NEW SECTION: TODAY'S CASHFLOW (Teal Background) ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-coins"></i> Today's Cashflow Summary
        </div>
        <div class="stat-cards-row">
            <!-- 1. Today's Total Charity IN -->
            <div class="stat-card-box" style="background: #18988B; border-left-color: #0d6e63;">
                <div class="stat-info">
                    <h3 style="color: white;">£{{ number_format($todayCharityIN, 2) }}</h3>
                    <p style="color: rgba(255,255,255,0.85);">Today's Total Charity IN</p>
                </div>
                <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>

            <!-- 2. Today's Total Charity OUT -->
            <div class="stat-card-box" style="background: #18988B; border-left-color: #0d6e63;">
                <div class="stat-info">
                    <h3 style="color: white;">£{{ number_format($todayCharityOUT, 2) }}</h3>
                    <p style="color: rgba(255,255,255,0.85);">Today's Total Charity OUT</p>
                </div>
                <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-university"></i>
                </div>
            </div>

            <!-- 3. Today's Total Donor IN -->
            <div class="stat-card-box" style="background: #18988B; border-left-color: #0d6e63;">
                <div class="stat-info">
                    <h3 style="color: white;">£{{ number_format($todayDonorIN, 2) }}</h3>
                    <p style="color: rgba(255,255,255,0.85);">Today's Total Donor IN</p>
                </div>
                <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <!-- 4. Today's Total Donor OUT -->
            <div class="stat-card-box" style="background: #18988B; border-left-color: #0d6e63;">
                <div class="stat-info">
                    <h3 style="color: white;">£{{ number_format($todayDonorOUT, 2) }}</h3>
                    <p style="color: rgba(255,255,255,0.85);">Today's Total Donor OUT</p>
                </div>
                <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>

            <!-- 5. Today's Total Commission -->
            <div class="stat-card-box" style="background: #18988B; border-left-color: #0d6e63;">
                <div class="stat-info">
                    <h3 style="color: white;">£{{ number_format($todayCommission, 2) }}</h3>
                    <p style="color: rgba(255,255,255,0.85);">Today's Total Commission</p>
                </div>
                <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 1: TRANSACTION SUMMARY ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-chart-line"></i> Transaction Summary
        </div>
        <div class="stat-cards-row">
            <div class="stat-card-box" style="border-left-color: #48bb78;">
                <div class="stat-info">
                    <h3 style="color: #38a169;">{{ number_format($successfulThisMonth) }}</h3>
                    <p>Successful Trans. (This Month)</p>
                </div>
                <div class="stat-icon bg-green"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-card-box" style="border-left-color: #ed8936;">
                <div class="stat-info">
                    <h3 style="color: #dd6b20;">{{ number_format($unsuccessfulThisMonth) }}</h3>
                    <p>Failed Trans. (This Month)</p>
                </div>
                <div class="stat-icon bg-yellow"><i class="fas fa-times-circle"></i></div>
            </div>
            <div class="stat-card-box" style="border-left-color: #4299e1;">
                <div class="stat-info">
                    <h3 style="color: #3182ce;">{{ number_format($successfulLast30Days) }}</h3>
                    <p>Successful Trans. (Last 30 Days)</p>
                </div>
                <div class="stat-icon bg-blue"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="stat-card-box" style="border-left-color: #9f7aea;">
                <div class="stat-info">
                    <h3 style="color: #805ad5;">{{ number_format($unsuccessfulLast30Days) }}</h3>
                    <p>Failed Trans. (Last 30 Days)</p>
                </div>
                <div class="stat-icon bg-purple"><i class="fas fa-calendar-times"></i></div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 2: DONATION SUMMARY ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-hand-holding-heart"></i> Donation Summary
        </div>
        <div class="stat-cards-row">
            <div class="stat-card-box" style="border-left-color: #4299e1;">
                <div class="stat-info">
                    <h3 style="color: #3182ce;">£{{ number_format($todaysOnlineDonation, 2) }}</h3>
                    <p>Today's Online Donation</p>
                </div>
                <div class="stat-icon bg-blue"><i class="fas fa-donate"></i></div>
            </div>
            <div class="stat-card-box" style="border-left-color: #ed8936;">
                <div class="stat-info">
                    <h3 style="color: #dd6b20;">£{{ number_format($todaysStandingDonation, 2) }}</h3>
                    <p>Today's Standing Donation</p>
                </div>
                <div class="stat-icon bg-yellow"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-card-box" style="border-left-color: #9f7aea;">
                <div class="stat-info">
                    <h3 style="color: #805ad5;">£{{ number_format($tomorrowStandingAmount, 2) }}</h3>
                    <p>Tomorrow's Scheduled</p>
                </div>
                <div class="stat-icon bg-purple"><i class="fas fa-forward"></i></div>
            </div>
            <div class="stat-card-box" style="border-left-color: #48bb78;">
                <div class="stat-info">
                    <h3 style="color: #38a169;">£{{ number_format($next7DaysStandingAmount, 2) }}</h3>
                    <p>Next 7 Days Scheduled</p>
                </div>
                <div class="stat-icon bg-green"><i class="fas fa-calendar-week"></i></div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 3: MONITORING PAGES ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-external-link-alt"></i> Monitoring Pages
        </div>
        <div class="monitoring-grid">
            <a href="{{ route('admin.audit.balances') }}" class="monitoring-link">
                <span><i class="fas fa-donate"></i> Charity Audit</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('dev.auditDonors') }}" class="monitoring-link">
                <span><i class="fas fa-users"></i> Donor Audit</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('developer.online_donations') }}" class="monitoring-link">
                <span><i class="fas fa-globe"></i> Online Donations</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('developer.standing_donations') }}" class="monitoring-link">
                <span><i class="fas fa-redo"></i> Standing Donations</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('developer.donors') }}" class="monitoring-link">
                <span><i class="fas fa-user-friends"></i> Donors</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('developer.charities') }}" class="monitoring-link">
                <span><i class="fas fa-hand-holding-heart"></i> Charities</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('developer.process_voucher_monitoring') }}" class="monitoring-link">
                <span><i class="fas fa-book"></i> Voucher Process</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('developer.vouchers') }}" class="monitoring-link">
                <span><i class="fas fa-ticket-alt"></i> Vouchers Books</span>
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- ===== SECTION 4: ALERTS & CRITICAL ISSUES ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-exclamation-triangle"></i> Alerts & Critical Issues
        </div>

        <!-- Stuck Standing Donations -->  
        <div class="dev-panel panel-warning">
            <div class="dev-panel-header">
                <h3>
                    <i class="fas fa-history"></i> Stuck Standing Donations
                    <span class="table-note">Active standing donations where the installment date has passed but payment was not processed.</span>
                </h3>
            </div>
            <div class="dev-panel-body">
                <div class="table-responsive-wrap">
                    <table class="dev-data-table table table-striped" style="width:100%" id="stuckTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Info</th>
                                <th>Charity</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Interval</th>
                                <th>Start Date</th>
                                <th>Last Payment</th>
                                <th>Expected Date</th>
                                <th>Days Overdue</th>
                                <th>Paid / Skipped</th>
                            </tr>
                        </thead>
                        <tbody id="stuckTableBody">
                            <tr>
                                <td colspan="11" class="loading-spinner">
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Will Fail: Insufficient Balance -->  
        <div class="dev-panel panel-alert">
            <div class="dev-panel-header">
                <h3>
                    <i class="fas fa-money-bill-wave"></i> Will Fail: Insufficient Balance
                    <span class="table-note">Active standing donations where the donor's available balance is less than the donation amount.</span>
                </h3>
            </div>
            <div class="dev-panel-body">
                <div class="table-responsive-wrap">
                    <table class="dev-data-table table table-striped" style="width:100%" id="failingTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Info</th>
                                <th>Charity</th>
                                <th>Amount</th>
                                <th>Avail. Limit</th>
                                <th>Shortfall</th>
                                <th>Type</th>
                                <th>Next Payment</th>
                                <th>Interval</th>
                            </tr>
                        </thead>
                        <tbody id="failingTableBody">
                            <tr>
                                <td colspan="9" class="loading-spinner">
                                    <div class="spinner-border text-danger" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 5: PAYMENT MONITORING ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-credit-card"></i> Payment Monitoring
        </div>

        <!-- Stripe Sync Issues -->
        <div class="dev-panel panel-alert">
            <div class="dev-panel-header">
                <h3>
                    <i class="fas fa-exclamation-circle"></i> Stripe Sync Issues
                    <span class="table-note">Donations where Stripe payment succeeded but database status is still pending (0). Needs manual verification.</span>
                </h3>
            </div>
            <div class="dev-panel-body">
                <div class="table-responsive-wrap">
                    <table class="dev-data-table dev-datatable table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor Info</th>
                                <th>Charity</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stripeSyncIssues as $donation)
                            <tr>
                                <td><strong>#{{ $donation->id }}</strong></td>
                                <td class="donor-info-cell">
                                    <strong>{{ $donation->user->name ?? 'Guest' }}</strong>
                                    @if($donation->user)
                                        <span class="donor-meta">Acct: {{ $donation->user->accountno ?? 'N/A' }}</span>
                                        <span class="donor-meta">{{ $donation->user->email ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>{{ $donation->charity->name ?? 'N/A' }}</td>
                                <td><strong>£{{ number_format($donation->amount, 2) }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('d M, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Donor TopUp Records -->
        <div class="dev-panel">
            <div class="dev-panel-header">
                <h3>
                    <i class="fas fa-wallet"></i> Donor TopUp Records (with Commission)
                </h3>
                <div class="filter-btn-group">
                    <button type="button" class="filter-btn active" data-filter="today">Today</button>
                    <button type="button" class="filter-btn" data-filter="week">This Week</button>
                    <button type="button" class="filter-btn" data-filter="month">This Month</button>
                    <button type="button" class="filter-btn" data-filter="year">This Year</button>
                    <button type="button" class="filter-btn" data-filter="all">All</button>
                </div>
            </div>
            <div class="dev-panel-body">
                <div class="table-responsive-wrap">
                    <table class="dev-data-table table table-striped" style="width:100%" id="topupTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Info</th>
                                <th>Gross Amount</th>
                                <th>Commission</th>
                                <th>Net Added</th>
                                <th>Source</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="topupTableBody">
                            <tr>
                                <td colspan="7" class="loading-spinner">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="topup-footer">
                                <td colspan="2" class="text-right">TOTAL:</td>
                                <td id="totalGross">£0.00</td>
                                <td id="totalCommission">£0.00</td>
                                <td id="totalNet">£0.00</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 6: QUICK TOOLS & HISTORY ===== -->
    <div class="dev-section">
        <div class="dev-section-title">
            <i class="fas fa-tools"></i> Quick Tools & History
        </div>

        <div class="dev-panel">
            <div class="dev-panel-header">
                <h3><i class="fas fa-bolt"></i> Developer Quick Tools</h3>
            </div>
            <div class="quick-tools-grid">
                <a href="{{ route('dev.searchTransaction') }}" class="quick-tool-btn">
                    <span><i class="fas fa-search"></i> Transaction Check</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('admin.transactionDelete') }}" class="quick-tool-btn">
                    <span><i class="fas fa-trash"></i> Transaction Delete Tool</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('dev.fixOldDates') }}" class="quick-tool-btn alert-btn" onclick="return confirm('This will recalculate all business dates. Continue?')">
                    <span><i class="fas fa-wrench"></i> Fix Old Dates</span>
                    <i class="fas fa-exclamation-triangle"></i>
                </a>
            </div>
        </div>

        <div class="dev-panel">
            <div class="dev-panel-header">
                <h3><i class="fas fa-history"></i> Cut-off History</h3>
            </div>
            <div class="dev-panel-body">
                <div class="table-responsive-wrap">
                    <table class="cutoff-table">
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
                                <td><span class="badge-pill badge-info">{{ $history->cutoff_time }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables for static tables only (not AJAX loaded)
        if ($.fn.DataTable) {
            $('.dev-datatable').not('#stuckTable, #failingTable').DataTable({
                "pageLength": 10,
                "order": [[ 0, "desc" ]],
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                "language": {
                    "search": "Filter:",
                    "emptyTable": "No data available",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "lengthMenu": "Show _MENU_ entries"
                },
                "scrollX": true
            });
        }

        // ============================================================
        // AJAX: Stuck Standing Donations
        // ============================================================
        function loadStuckDonations() {
            $.ajax({
                url: "{{ route('dev.getStuckStandingDonations') }}",
                type: "GET",
                beforeSend: function() {
                    $('#stuckTableBody').html('<tr><td colspan="11" class="loading-spinner"><div class="spinner-border text-warning" role="status"></div></td></tr>');
                },
                success: function(res) {
                    $('#stuckTableBody').empty();
                    
                    if(res.data.length > 0) {
                        res.data.forEach(function(s) {
                            var lastPaymentHtml = s.last_payment === 'Never' 
                                ? '<span class="text-muted">Never</span>' 
                                : s.last_payment;
                            
                            $('#stuckTableBody').append(`
                                <tr>
                                    <td><strong>#${s.id}</strong></td>
                                    <td class="donor-info-cell">
                                        <strong>${s.donor_name}</strong>
                                        <span class="donor-meta">Acct: ${s.donor_acct}</span>
                                        <span class="donor-meta">${s.donor_email}</span>
                                    </td>
                                    <td>${s.charity}</td>
                                    <td><strong>£${s.amount}</strong></td>
                                    <td><span class="badge-pill badge-${s.type === 'Fixed' ? 'warning' : 'success'}">${s.type}</span></td>
                                    <td>${s.interval}</td>
                                    <td>${s.start_date}</td>
                                    <td>${lastPaymentHtml}</td>
                                    <td class="text-orange">${s.expected_date}</td>
                                    <td><span class="badge-pill badge-danger">${s.days_overdue} days</span></td>
                                    <td>
                                        <span class="badge-pill badge-success">${s.payments_made}</span>
                                        <span class="badge-pill badge-danger">${s.skipped_payments}</span>
                                    </td>
                                </tr>
                            `);
                        });

                        // Initialize DataTable after data load
                        $('#stuckTable').DataTable({
                            "pageLength": 10,
                            "order": [[ 0, "desc" ]],
                            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                            "language": {
                                "search": "Filter:",
                                "emptyTable": "No data available",
                                "infoEmpty": "Showing 0 to 0 of 0 entries"
                            },
                            "scrollX": true,
                            "destroy": true
                        });
                    } else {
                        $('#stuckTableBody').html('<tr><td colspan="11" style="text-align:center; padding:30px; color:#718096;">No stuck donations found.</td></tr>');
                    }
                },
                error: function() {
                    $('#stuckTableBody').html('<tr><td colspan="11" style="text-align:center; padding:30px; color:#c53030;">Failed to load data.</td></tr>');
                }
            });
        }

        // ============================================================
        // AJAX: Failing Standing Donations
        // ============================================================
        function loadFailingDonations() {
            $.ajax({
                url: "{{ route('dev.getFailingStandingDonations') }}",
                type: "GET",
                beforeSend: function() {
                    $('#failingTableBody').html('<tr><td colspan="9" class="loading-spinner"><div class="spinner-border text-danger" role="status"></div></td></tr>');
                },
                success: function(res) {
                    $('#failingTableBody').empty();
                    
                    if(res.data.length > 0) {
                        res.data.forEach(function(s) {
                            $('#failingTableBody').append(`
                                <tr>
                                    <td><strong>#${s.id}</strong></td>
                                    <td class="donor-info-cell">
                                        <strong>${s.donor_name}</strong>
                                        <span class="donor-meta">Acct: ${s.donor_acct}</span>
                                        <span class="donor-meta">${s.donor_email}</span>
                                    </td>
                                    <td>${s.charity}</td>
                                    <td><strong>£${s.amount}</strong></td>
                                    <td class="text-red">£${s.available_limit}</td>
                                    <td class="text-red">£${s.shortfall}</td>
                                    <td><span class="badge-pill badge-${s.type === 'Fixed' ? 'warning' : 'success'}">${s.type}</span></td>
                                    <td class="text-orange">${s.next_payment}</td>
                                    <td>${s.interval}</td>
                                </tr>
                            `);
                        });

                        // Initialize DataTable after data load
                        $('#failingTable').DataTable({
                            "pageLength": 10,
                            "order": [[ 0, "desc" ]],
                            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                            "language": {
                                "search": "Filter:",
                                "emptyTable": "No data available",
                                "infoEmpty": "Showing 0 to 0 of 0 entries"
                            },
                            "scrollX": true,
                            "destroy": true
                        });
                    } else {
                        $('#failingTableBody').html('<tr><td colspan="9" style="text-align:center; padding:30px; color:#718096;">No failing donations found.</td></tr>');
                    }
                },
                error: function() {
                    $('#failingTableBody').html('<tr><td colspan="9" style="text-align:center; padding:30px; color:#c53030;">Failed to load data.</td></tr>');
                }
            });
        }

        // ============================================================
        // TOPUP RECORDS
        // ============================================================
        function loadTopupRecords(filter = 'today') {
            $.ajax({
                url: "{{ route('dev.getTopupRecords') }}",
                type: "GET",
                data: { filter: filter },
                beforeSend: function() {
                    $('#topupTableBody').html('<tr><td colspan="7" class="loading-spinner"><i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; color: #4a90e2;"></i></td></tr>');
                },
                success: function(res) {
                    $('#topupTableBody').empty();
                    
                    if(res.data.length > 0) {
                        res.data.forEach(function(t) {
                            $('#topupTableBody').append(`
                                <tr>
                                    <td><strong>#${t.id}</strong></td>
                                    <td class="donor-info-cell">
                                        <strong>${t.user_name}</strong>
                                        <span class="donor-meta">${t.user_email}</span>
                                    </td>
                                    <td><strong>£${t.gross}</strong></td>
                                    <td>£${t.commission}</td>
                                    <td>£${t.net}</td>
                                    <td>${t.source}</td>
                                    <td>${t.date}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#topupTableBody').html('<tr><td colspan="7" style="text-align:center; padding:30px; color:#718096;">No records found for this period.</td></tr>');
                    }

                    $('#totalGross').text('£' + res.totals.gross);
                    $('#totalCommission').text('£' + res.totals.commission);
                    $('#totalNet').text('£' + res.totals.net);
                },
                error: function() {
                    $('#topupTableBody').html('<tr><td colspan="7" style="text-align:center; padding:30px; color:#c53030;">Failed to load data.</td></tr>');
                }
            });
        }

        // Initial Load all 3 tables
        loadStuckDonations();
        loadFailingDonations();
        loadTopupRecords('today');

        // Filter Button Click (Topup)
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            var filter = $(this).data('filter');
            loadTopupRecords(filter);
        });
    });
</script>
@endsection