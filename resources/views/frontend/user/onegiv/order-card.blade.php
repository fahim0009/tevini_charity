@extends('frontend.layouts.user')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <h4 class="fw-bold">Order a OneGiv Card</h4>
        <p class="text-muted">Order your donation card below. Once processed, your card details will be sent to you.</p>
    </div>
</div>

<div class="row justify-content-center">

    {{-- Card Preview --}}
    <div class="col-lg-5 mb-4">
        <div class="onegiv-card-preview p-4 rounded-4 text-white"
             style="background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
                    min-height: 200px; position: relative; overflow: hidden;">

            <div style="position:absolute; top:-30px; right:-30px; width:150px; height:150px;
                        border-radius:50%; background:rgba(255,255,255,0.05);"></div>
            <div style="position:absolute; bottom:-40px; left:-20px; width:180px; height:180px;
                        border-radius:50%; background:rgba(255,255,255,0.04);"></div>

            <div class="d-flex justify-content-between align-items-start mb-4">
                <span class="fw-bold fs-5" style="letter-spacing:2px;">ONEGIV</span>
                <span class="badge" style="background:rgba(255,255,255,0.15); font-size:11px;">Donation Card</span>
            </div>

            <div class="mb-3">
                <span style="letter-spacing:3px; font-size:16px;">**** **** **** ****</span>
            </div>

            <div class="d-flex justify-content-between align-items-end">
                <div>
                    <small style="opacity:0.6; font-size:10px;">CARD HOLDER</small>
                    <p class="mb-0 fw-semibold" id="preview_name">YOUR NAME</p>
                </div>
                <div class="text-end">
                    <small style="opacity:0.6; font-size:10px;">AMOUNT</small>
                    <p class="mb-0 fw-semibold" id="preview_amount">£0.00</p>
                </div>
            </div>
        </div>

        <div class="mt-3 p-3 rounded-3" style="background:#f8f9fa; border-left: 4px solid #0f3460;">
            <small class="text-muted">
                <strong>Note:</strong> After ordering, OneGiv will process and send your card details
                (serial number, PIN, expiry) back to the system automatically.
            </small>
        </div>
    </div>

    {{-- Order Form --}}
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <form action="{{ route('onegiv.ordercard.store') }}" method="POST">
                    @csrf

                    {{-- Card Holder --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Card Holder Name</label>
                        <input type="text"
                               name="card_holder"
                               id="card_holder"
                               class="form-control @error('card_holder') is-invalid @enderror"
                               placeholder="Full name on card"
                               value="{{ old('card_holder') }}"
                               oninput="document.getElementById('preview_name').innerText = this.value || 'YOUR NAME'">
                        @error('card_holder')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Card Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Card Type</label>
                        <div class="d-flex gap-3">

                            <input type="radio" class="btn-check" name="fixed_amount" id="fixed_yes" value="1"
                                {{ old('fixed_amount', '1') == '1' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary flex-fill text-center py-2" for="fixed_yes">
                                <span style="font-size:20px;">💳</span><br>
                                <span class="fw-semibold">Fixed Amount</span><br>
                                <small class="text-muted">Set a specific amount</small>
                            </label>

                            <input type="radio" class="btn-check" name="fixed_amount" id="fixed_no" value="0"
                                {{ old('fixed_amount') == '0' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary flex-fill text-center py-2" for="fixed_no">
                                <span style="font-size:20px;">🔄</span><br>
                                <span class="fw-semibold">Variable Amount</span><br>
                                <small class="text-muted">Donor chooses amount</small>
                            </label>

                        </div>
                        @error('fixed_amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (£)</label>
                        <div class="input-group">
                            <span class="input-group-text">£</span>
                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   placeholder="e.g. 10"
                                   min="1"
                                   step="0.01"
                                   value="{{ old('amount') }}"
                                   oninput="document.getElementById('preview_amount').innerText = '£'+(parseFloat(this.value)||0).toFixed(2)">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Enter amount in pounds. e.g. 10 = £10.00</small>
                    </div>

                    {{-- PIN --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">4-Digit PIN</label>
                        <input type="password"
                               name="pin"
                               class="form-control @error('pin') is-invalid @enderror"
                               placeholder="Enter 4-digit PIN"
                               maxlength="4"
                               value="{{ old('pin') }}">
                        @error('pin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn w-100 text-white fw-semibold py-2"
                            style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                        Place Card Order
                    </button>

                </form>

            </div>
        </div>
    </div>

</div>
@endsection