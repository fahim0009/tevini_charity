@extends('layouts.admin')

@section('title', 'Standing Donation Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="dev-dashboard">
    <div class="dev-header">
        <h1>Standing Donation Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <div class="dev-card" style="margin-bottom: 20px;">
        <div class="dev-card-header"><h3>All Standing Donations</h3></div>
        <div class="dev-card-body" style="padding: 0;">
            <table class="dev-table">
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
    
    <div style="margin-top: 20px;">
        {{ $standingDonations->links() }}
    </div>
</div>
@endsection