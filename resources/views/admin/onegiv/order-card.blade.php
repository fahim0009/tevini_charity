@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('donor.profile', $user->id) }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Profile
            </a>
            <div>
                <h4 class="fw-bold mb-0">Order OneGiv Card</h4>
                <small class="text-muted">For: {{ $user->name }} {{ $user->surname }} (ID: {{ $user->id }})</small>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-primary fs-6">Available: £{{ number_format($user->getAvailableLimit(), 2) }}</span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.onegiv.ordercard.store', $user->id) }}" method="POST">
    @csrf

    <div class="row g-4">

        {{-- LEFT — Card Preview + Card Settings --}}
        <div class="col-lg-5">

            {{-- Card Visual Preview --}}
            <div class="p-4 rounded-4 text-white mb-3"
                 style="background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
                        min-height: 200px; position: relative; overflow: hidden;">

                <div style="position:absolute; top:-30px; right:-30px; width:150px; height:150px;
                            border-radius:50%; background:rgba(255,255,255,0.05);"></div>
                <div style="position:absolute; bottom:-40px; left:-20px; width:180px; height:180px;
                            border-radius:50%; background:rgba(255,255,255,0.04);"></div>

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <span class="fw-bold fs-5" style="letter-spacing:2px;">ONEGIV</span>
                    <span class="badge" style="background:rgba(255,255,255,0.15); font-size:11px;">
                        Donation Card
                    </span>
                </div>

                <div class="mb-3">
                    <span style="letter-spacing:4px; font-size:15px; opacity:0.8;">
                        **** **** **** ****
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <small style="opacity:0.5; font-size:10px; letter-spacing:1px;">CARD HOLDER</small>
                        <p class="mb-0 fw-semibold" id="preview_name">
                            {{ old('card_holder', $user->name . ' ' . $user->surname) }}
                        </p>
                    </div>
                    <div class="text-end">
                        <small style="opacity:0.5; font-size:10px; letter-spacing:1px;">AMOUNT</small>
                        <p class="mb-0 fw-semibold" id="preview_amount">£0.00</p>
                    </div>
                </div>
            </div>

            {{-- Card Settings --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-4">
                    <p class="fw-bold mb-3" style="color:#0f3460;">
                        💳 Card Settings
                    </p>

                    {{-- Card Holder Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Card Holder Name</label>
                        <input type="text" name="card_holder" id="card_holder"
                               class="form-control @error('card_holder') is-invalid @enderror"
                               value="{{ old('card_holder', $user->name . ' ' . $user->surname) }}"
                               oninput="document.getElementById('preview_name').innerText = this.value || 'YOUR NAME'">
                        @error('card_holder')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Card Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Card Type</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="fixed_amount"
                                   id="fixed_yes" value="1"
                                   {{ old('fixed_amount', '1') == '1' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary flex-fill text-center py-2" for="fixed_yes">
                                <span style="font-size:18px;">💳</span><br>
                                <span class="fw-semibold small">Fixed Amount</span><br>
                                <small class="text-muted" style="font-size:10px;">Set a specific amount</small>
                            </label>

                            <input type="radio" class="btn-check" name="fixed_amount"
                                   id="fixed_no" value="0"
                                   {{ old('fixed_amount') == '0' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary flex-fill text-center py-2" for="fixed_no">
                                <span style="font-size:18px;">🔄</span><br>
                                <span class="fw-semibold small">Variable</span><br>
                                <small class="text-muted" style="font-size:10px;">Donor chooses amount</small>
                            </label>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="mb-3" id="amount_field">
                        <label class="form-label fw-semibold">Amount (£)</label>
                        <input type="number" name="amount" id="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            placeholder="e.g. 1.50"
                            min="0.50"
                            step="0.01"
                            value="{{ old('amount') }}"
                            oninput="validateAmount(this)">
                        <small class="text-muted">Minimum amount is £0.50</small>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PIN --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">4-Digit PIN</label>
                        <input type="password" name="pin"
                               class="form-control @error('pin') is-invalid @enderror"
                               placeholder="Enter 4-digit PIN"
                               maxlength="4" inputmode="numeric">
                        <small class="text-muted">You'll use this PIN to make donations.</small>
                        @error('pin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Admin Note --}}
            <div class="p-3 rounded-3" style="background:#fff3cd; border-left: 4px solid #ffc107;">
                <small class="text-muted">
                    <strong>⚠️ Admin Note:</strong> You are ordering this card on behalf of the donor.
                    £5.00 will be deducted from their available balance.
                </small>
            </div>

        </div>

        {{-- RIGHT — Personal & Address Details --}}
        <div class="col-lg-7">

            {{-- Personal Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="fw-bold mb-0" style="color:#0f3460;">
                            👤 Personal Information
                        </p>
                        <small class="text-muted">Pre-filled from profile</small>
                    </div>

                    <div class="row g-3">

                        {{-- Name --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" class="form-control bg-light"
                                   value="{{ $user->name }}" readonly>
                        </div>

                        {{-- Surname --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Surname</label>
                            <input type="text" class="form-control bg-light"
                                   value="{{ $user->surname }}" readonly>
                        </div>

                        {{-- Email --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mobile --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mobile Number</label>
                            <input type="text" name="mobile"
                                   class="form-control @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', $user->phone ?? '') }}"
                                   placeholder="e.g. 07412555927">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Address Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="fw-bold mb-0" style="color:#0f3460;">
                            📍 Address Details
                        </p>
                        <small class="text-muted">Pre-filled from profile</small>
                    </div>

                    <div class="row g-3">

                        {{-- House Number --}}
                        <div class="col-4">
                            <label class="form-label fw-semibold">House No.</label>
                            <input type="text" name="house_number"
                                   class="form-control @error('house_number') is-invalid @enderror"
                                   value="{{ old('house_number', $user->houseno ?? '') }}"
                                   placeholder="32">
                            @error('house_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Street --}}
                        <div class="col-8">
                            <label class="form-label fw-semibold">Street</label>
                            <input type="text" name="street"
                                   class="form-control @error('street') is-invalid @enderror"
                                   value="{{ old('street', $user->street ?? '') }}"
                                   placeholder="Sunshine Lane">
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Address 2 --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Address Line 2
                                <small class="text-muted fw-normal">(optional)</small>
                            </label>
                            <input type="text" name="address2"
                                   class="form-control"
                                   value="{{ old('address2', $user->address_third_line ?? '') }}"
                                   placeholder="Apartment, suite, etc.">
                        </div>

                        {{-- Town/City --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Town / City</label>
                            <input type="text" name="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $user->town ?? '') }}"
                                   placeholder="Manchester">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Postcode --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Postcode</label>
                            <input type="text" name="postcode"
                                   class="form-control @error('postcode') is-invalid @enderror"
                                   value="{{ old('postcode', $user->postcode ?? '') }}"
                                   placeholder="M25 6GY">
                            @error('postcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Country --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Country</label>
                            <select name="country"
                                    class="form-control @error('country') is-invalid @enderror">
                                <option value="England"
                                    {{ old('country', 'England') == 'England' ? 'selected' : '' }}>
                                    England
                                </option>
                                <option value="Scotland"
                                    {{ old('country') == 'Scotland' ? 'selected' : '' }}>
                                    Scotland
                                </option>
                                <option value="Wales"
                                    {{ old('country') == 'Wales' ? 'selected' : '' }}>
                                    Wales
                                </option>
                                <option value="Northern Ireland"
                                    {{ old('country') == 'Northern Ireland' ? 'selected' : '' }}>
                                    Northern Ireland
                                </option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="d-flex gap-3 mb-4">
                <a href="{{ route('donor.profile', $user->id) }}" 
                   class="btn btn-outline-secondary px-4 py-3">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary flex-fill text-white fw-semibold py-3 rounded-3"
                        style="font-size:16px;">
                    <i class="fas fa-credit-card me-2"></i>
                    Place Card Order →
                </button>
            </div>

        </div>

    </div>

    </form>
</div>
@endsection

@section('script')
<script>
// Fixed/Variable toggle
document.querySelectorAll('input[name="fixed_amount"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var amountField = document.getElementById('amount_field');
        if (this.value == '1') {
            amountField.style.display = 'block';
        } else {
            amountField.style.display = 'none';
            document.getElementById('amount').value = '';
            document.getElementById('preview_amount').innerText = '£0.00';
        }
    });
});

// Page load check
window.onload = function() {
    var checked = document.querySelector('input[name="fixed_amount"]:checked');
    if (checked && checked.value == '0') {
        document.getElementById('amount_field').style.display = 'none';
    }
};

// Amount validation
function validateAmount(input) {
    var value = parseFloat(input.value);

    document.getElementById('preview_amount').innerText =
        '£' + (value > 0 ? value.toFixed(2) : '0.00');

    if (value < 0.50) {
        input.setCustomValidity('Minimum amount is £0.50');
        input.reportValidity();
    } else {
        input.setCustomValidity('');
    }
}
</script>
@endsection