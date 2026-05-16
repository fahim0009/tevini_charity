@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('donor.profile', $user->id) }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Profile
        </a>
        <div>
            <h4 class="fw-bold mb-0">OneGiv Card Orders</h4>
            <small class="text-muted">For: {{ $user->name }} {{ $user->surname }} (ID: {{ $user->id }})</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Order History</h6>
            <a href="{{ route('admin.onegiv.ordercard.form', $user->id) }}" 
               class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> New Order
            </a>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Order #</th>
                                <th>Card Holder</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Ordered By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    <span class="fw-bold">#{{ $order->order_number }}</span>
                                </td>
                                <td>{{ $order->card_holder }}</td>
                                <td>
                                    @if($order->fixed_amount)
                                        <span class="fw-bold">£{{ number_format($order->amount / 100, 2) }}</span>
                                    @else
                                        <span class="text-muted">Variable</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge"
                                          style="background: {{ $order->fixed_amount ? '#0f3460' : '#6c757d' }}; color:white;">
                                        {{ $order->fixed_amount ? 'Fixed' : 'Variable' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status == 'processed' ? 'success' : 'warning text-dark' }}">
                                        {{ ucfirst($order->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->ordered_by_admin)
                                        <span class="text-primary">
                                            <i class="fas fa-user-shield me-1"></i>Admin
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-user me-1"></i>Self
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div style="font-size:60px;">📭</div>
                    <h5 class="text-muted mt-3">No Orders Found</h5>
                    <p class="text-muted small">This user hasn't ordered any OneGiv cards yet.</p>
                    <a href="{{ route('admin.onegiv.ordercard.form', $user->id) }}" 
                       class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Place First Order
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection