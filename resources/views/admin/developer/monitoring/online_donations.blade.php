@extends('layouts.admin')

@section('title', 'Online Donation Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="dev-dashboard">
    <div class="dev-header">
        <h1>Online Donation Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <div class="dev-card" style="margin-bottom: 20px;">
        <div class="dev-card-header"><h3>All Online Donations</h3></div>
        <div class="dev-card-body" style="padding: 0;">
            <table class="dev-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Charity</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                    <tr>
                        <td>#{{ $donation->id }}</td>
                        <td>{{ $donation->user->name ?? 'Guest' }}</td>
                        <td>{{ $donation->charity->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($donation->amount, 2) }}</td>
                        <td>{{ $donation->payment_method }}</td>
                        <td>
                            @if($donation->status == 1)
                                <span class="dev-badge status-badge-success">Success</span>
                            @else
                                <span class="dev-badge status-badge-danger">Pending</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('d M, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $donations->links() }}
    </div>
</div>
@endsection