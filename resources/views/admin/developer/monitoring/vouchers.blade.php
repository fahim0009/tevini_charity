@extends('layouts.admin')

@section('title', 'Voucher Monitoring')

@section('css')
    <link href="{{URL::to('/css/dev.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="dev-dashboard">
    <div class="dev-header">
        <h1>Voucher Monitoring</h1>
        <a href="{{ route('developer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dev Dashboard
        </a>
    </div>

    <div class="dev-card">
        <div class="dev-card-header"><h3>All Vouchers (Barcodes)</h3></div>
        <div class="dev-card-body" style="padding: 0;">
            <table class="dev-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barcode</th>
                        <th>Status</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vouchers as $voucher)
                    <tr>
                        <td>#{{ $voucher->id }}</td>
                        <td>{{ $voucher->barcode }}</td>
                        <td>
                            @if($voucher->status == 1)
                                <span class="dev-badge status-badge-success">Used/Active</span>
                            @else
                                <span class="dev-badge status-badge-warning">Unused</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($voucher->created_at)->format('d M, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $vouchers->links() }}
    </div>
</div>
@endsection