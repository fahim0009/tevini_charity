@extends('frontend.layouts.user')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">

        <div class="mb-4">
            <h4 class="fw-bold">Change Card PIN</h4>
            <p class="text-muted">Update your 4-digit PIN for card ending
                <strong>{{ substr($card->display_number ?? $card->serial_number, -4) }}</strong>
            </p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <form action="{{ route('onegiv.changepin.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="serial_number" value="{{ $card->serial_number }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New PIN</label>
                        <input type="password"
                               name="pin"
                               class="form-control @error('pin') is-invalid @enderror"
                               placeholder="Enter new 4-digit PIN"
                               maxlength="4">
                        @error('pin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm PIN</label>
                        <input type="password"
                               name="pin_confirm"
                               class="form-control @error('pin_confirm') is-invalid @enderror"
                               placeholder="Re-enter new 4-digit PIN"
                               maxlength="4">
                        @error('pin_confirm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="btn w-100 text-white fw-semibold py-2"
                            style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                        Update PIN
                    </button>

                    <a href="{{ route('onegiv.mycards') }}"
                       class="btn w-100 btn-outline-secondary mt-2">
                        Cancel
                    </a>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection