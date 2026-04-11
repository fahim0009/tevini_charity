@extends('frontend.layouts.user')

@section('content')

<div class="row align-items-center mb-4">
    <div class="col">
        <h4 class="fw-bold mb-0">Card Transactions</h4>
        <p class="text-muted mb-0">All donation transactions made with your OneGiv cards</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('onegiv.mycards') }}" class="btn btn-outline-secondary btn-sm">
            ← My Cards
        </a>
    </div>
</div>

@if($transactions->isEmpty())
    <div class="text-center py-5" style="background:#f8f9fa; border-radius:16px;">
        <div style="font-size:48px;">📋</div>
        <h5 class="mt-3 fw-semibold">No Transactions Yet</h5>
        <p class="text-muted">No donations have been made with your cards yet.</p>
    </div>
@else
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="example">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Transaction ID</th>
                            <th class="px-4 py-3">Card Serial</th>
                            <th class="px-4 py-3">Charity</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $txn)
                        <tr>
                            <td class="px-4 py-3">
                                {{ $txn->created_at->format('d M Y') }}
                                <br>
                                <small class="text-muted">{{ $txn->created_at->format('H:i') }}</small>
                            </td>
                            <td class="px-4 py-3">
                                <small class="text-muted">{{ $txn->card_issuer_transaction_id }}</small>
                            </td>
                            <td class="px-4 py-3">{{ $txn->card_serial_number }}</td>
                            <td class="px-4 py-3">
                                {{ $txn->charity_number }}
                                @if($txn->reference)
                                    <br><small class="text-muted">{{ $txn->reference }}</small>
                                @endif
                            </td>
                            <td class="px-4 py-3 fw-semibold">
                                £{{ number_format($txn->amount / 100, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($txn->status == 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($txn->status == 'refunded')
                                    <span class="badge bg-warning text-dark">Refunded</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($txn->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $transactions->links() }}
    </div>
@endif

@endsection