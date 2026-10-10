@extends('layouts.admin')

@section('title', 'Donor Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="dev-dashboard">
    <div class="dev-header">
        <h1>Donor Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <div class="dev-card">
        <div class="dev-card-header"><h3>All Donors</h3></div>
        <div class="dev-card-body" style="padding: 0;">
            <table class="dev-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Available Limit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donors as $donor)
                    <tr>
                        <td>#{{ $donor->id }}</td>
                        <td>{{ $donor->name }}</td>
                        <td>{{ $donor->email }}</td>
                        <td>£{{ number_format($donor->balance, 2) }}</td>
                        <td>£{{ number_format($donor->getAvailableLimit(), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $donors->links() }}
    </div>
</div>
@endsection