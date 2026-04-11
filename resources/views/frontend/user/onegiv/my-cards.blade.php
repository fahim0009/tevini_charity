@extends('frontend.layouts.user')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>
</div>

<div class="row align-items-center mb-4">
    <div class="col">
        <h4 class="fw-bold mb-0">My OneGiv Cards</h4>
        <p class="text-muted mb-0">All your donation cards</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('onegiv.ordercard.form') }}"
           class="btn text-white"
           style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
            + Order New Card
        </a>
    </div>
</div>

@if($cards->isEmpty())
    <div class="row">
        <div class="col-md-12">
            <div class="text-center py-5" style="background:#f8f9fa; border-radius:16px;">
                <div style="font-size:48px;">💳</div>
                <h5 class="mt-3 fw-semibold">No Cards Yet</h5>
                <p class="text-muted">You haven't ordered any OneGiv cards yet.</p>
                <a href="{{ route('onegiv.ordercard.form') }}"
                   class="btn text-white px-4"
                   style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                    Order Your First Card
                </a>
            </div>
        </div>
    </div>
@else
    <div class="row">
        @foreach($cards as $card)
        <div class="col-lg-4 col-md-6 mb-4">

            {{-- Card Visual --}}
            <div class="p-4 rounded-4 text-white mb-3"
                 style="background: {{ $card->status == 'active'
                    ? 'linear-gradient(135deg, #1a1a2e, #16213e, #0f3460)'
                    : 'linear-gradient(135deg, #555, #333)' }};
                        min-height: 170px; position: relative; overflow: hidden;">

                <div style="position:absolute; top:-20px; right:-20px; width:120px; height:120px;
                            border-radius:50%; background:rgba(255,255,255,0.05);"></div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold" style="letter-spacing:2px;">ONEGIV</span>
                    <span class="badge"
                          style="background:{{ $card->status == 'active' ? 'rgba(0,255,100,0.2)' : 'rgba(255,50,50,0.2)' }};
                                 color:{{ $card->status == 'active' ? '#00ff64' : '#ff5555' }};">
                        {{ strtoupper($card->status) }}
                    </span>
                </div>

                <p class="mb-2" style="letter-spacing:3px; font-size:13px;">
                    {{ implode(' ', str_split(str_pad(substr($card->display_number ?? '****************', 0, 16), 16, '*'), 4)) }}
                </p>

                <div class="d-flex justify-content-between">
                    <div>
                        <small style="opacity:0.5; font-size:10px;">EXPIRES</small>
                        <p class="mb-0 fw-semibold" style="font-size:13px;">
                            {{ substr($card->expiry_date ?? 'MMYY', 0, 2) }}/{{ substr($card->expiry_date ?? 'MMYY', 2) }}
                        </p>
                    </div>
                    <div class="text-end">
                        <small style="opacity:0.5; font-size:10px;">SERIAL</small>
                        <p class="mb-0 fw-semibold" style="font-size:13px;">{{ $card->serial_number }}</p>
                    </div>
                </div>
            </div>

            {{-- Card Actions --}}
            <div class="d-flex gap-2">
                @if($card->status == 'active')
                    <a href="{{ route('onegiv.changepin.form', $card->serial_number) }}"
                       class="btn btn-sm btn-outline-secondary flex-fill">
                        🔑 Change PIN
                    </a>
                @endif
                <a href="{{ route('onegiv.transactions') }}"
                   class="btn btn-sm flex-fill text-white"
                   style="background:#0f3460;">
                    📋 Transactions
                </a>
            </div>

        </div>
        @endforeach
    </div>
@endif

@endsection