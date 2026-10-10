@extends('layouts.admin')

@section('title', 'Voucher Book Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="dev-dashboard">
    <div class="dev-header">
        <h1>Voucher Book Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <div class="dev-card">
        <div class="dev-card-header"><h3>All Voucher Book Orders</h3></div>
        <div class="dev-card-body" style="padding: 0;">
            <table class="dev-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Voucher Type</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voucherBooks as $book)
                    <tr>
                        <td>#{{ $book->id }}</td>
                        <td>{{ $book->user->name ?? 'Guest/N/A' }}</td>
                        <td>{{ $book->voucher_type ?? 'N/A' }}</td>
                        <td>
                            @if($book->status == 1)
                                <span class="dev-badge status-badge-success">Completed</span>
                            @elseif($book->status == 0)
                                <span class="dev-badge status-badge-warning">Pending</span>
                            @else
                                <span class="dev-badge status-badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($book->created_at)->format('d M, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $voucherBooks->links() }}
    </div>
</div>
@endsection