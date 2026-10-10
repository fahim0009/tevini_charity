@extends('layouts.admin')

@section('title', 'Charity Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="dev-dashboard">
    <div class="dev-header">
        <h1>Charity Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <div class="dev-card">
        <div class="dev-card-header"><h3>All Charities</h3></div>
        <div class="dev-card-body" style="padding: 0;">
            <table class="dev-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charities as $charity)
                    <tr>
                        <td>#{{ $charity->id }}</td>
                        <td>{{ $charity->name }}</td>
                        <td>{{ $charity->email }}</td>
                        <td>£{{ number_format($charity->balance, 2) }}</td>
                        <td>
                            @if($charity->status == 1)
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
        {{ $charities->links() }}
    </div>
</div>
@endsection