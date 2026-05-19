@extends('frontend.layouts.user')

@section('content')

<style>
    .card {
        background-color: #FDF3EE
    }
</style>


{{-- Page Header --}}
<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="fw-bold mb-0">Order a OneGiv Card</h4>
        <p class="text-muted mb-0">Fill in the details below to order your donation card.</p>
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

<form action="{{ route('onegiv.ordercard.store') }}" method="POST" id="orderForm">
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
                        {{ Auth::user()->name }} {{ Auth::user()->surname }}
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
                           value="{{ old('card_holder', Auth::user()->name . ' ' . Auth::user()->surname) }}"
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
                <div id="amount_field" class="mb-3">
                    <label class="form-label fw-semibold">Amount</label>
                    <input type="number" name="amount" id="amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        placeholder="e.g. 1.50"
                        min="0.50"
                        step="0.01"
                        value="{{ old('amount') }}"
                        oninput="validateAmount(this)">
                    <small class="text-muted">Minimum amount is £0.50 (e.g. 0.50, 1, 1.50, 10)</small>
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

        {{-- Note --}}
        <div class="p-3 rounded-3" style="background:#f0f4ff; border-left: 4px solid #0f3460;">
            <small class="text-muted">
                <strong>ℹ️ Note:</strong> After ordering, OneGiv will process your card and
                automatically send the card details (serial number, expiry) back to the system.
            </small>
        </div>

    </div>

    {{-- RIGHT — Personal & Address Details --}}
    <div class="col-lg-7">

        {{-- Personal Info --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <p class="fw-bold mb-3" style="color:#0f3460;">
                    👤 Personal Information
                </p>

                <div class="row g-3">

                    {{-- Name --}}
                    <div class="col-6">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ Auth::user()->name }}" readonly>
                        <small class="text-muted">From your profile</small>
                    </div>

                    {{-- Surname --}}
                    <div class="col-6">
                        <label class="form-label fw-semibold">Surname</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ Auth::user()->surname }}" readonly>
                        <small class="text-muted">From your profile</small>
                    </div>

                    {{-- Email --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', Auth::user()->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mobile --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Mobile Number</label>
                        <input type="text" name="mobile"
                               class="form-control @error('mobile') is-invalid @enderror"
                               value="{{ old('mobile', Auth::user()->phone ?? '') }}"
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
                    <small class="text-muted">
                        Pre-filled from your
                        <a href="{{ route('user.profile') }}" target="_blank">profile</a>
                    </small>
                </div>

                <div class="row g-3">

                    {{-- House Number --}}
                    <div class="col-4">
                        <label class="form-label fw-semibold">House No.</label>
                        <input type="text" name="house_number"
                               class="form-control @error('house_number') is-invalid @enderror"
                               value="{{ old('house_number', Auth::user()->houseno ?? '') }}"
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
                               value="{{ old('street', Auth::user()->street ?? '') }}"
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
                               value="{{ old('address2', Auth::user()->address_third_line ?? '') }}"
                               placeholder="Apartment, suite, etc.">
                    </div>

                    {{-- Town/City --}}
                    <div class="col-6">
                        <label class="form-label fw-semibold">Town / City</label>
                        <input type="text" name="city"
                               class="form-control @error('city') is-invalid @enderror"
                               value="{{ old('city', Auth::user()->town ?? '') }}"
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
                               value="{{ old('postcode', Auth::user()->postcode ?? '') }}"
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

        {{-- Submit Button --}}
        <button type="submit" id="submitBtn" class="btn w-100 text-white fw-semibold py-3 rounded-3 mb-4"
                style="background: linear-gradient(135deg, #1a1a2e, #0f3460); font-size:16px;">
            Place Card Order →
        </button>

        {{-- View Orders --}}
        <div class="text-center mt-3 d-none">
            <button type="button" class="btn btn-outline-secondary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#ordersModal">
                📋 View Previous Orders
            </button>
        </div>

    </div>

</div>

</form>

{{-- LOADING SPINNER OVERLAY - MOVED OUTSIDE FORM --}}
<div id="loadingOverlay" style="display:none !important; position:fixed !important; top:0 !important; left:0 !important; width:100% !important; height:100% !important; background:rgba(255,255,255,0.9) !important; z-index:999999 !important; justify-content:center !important; align-items:center !important; flex-direction:column !important;">
    <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem; border-width: 0.4em;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-3 fw-bold text-dark" style="font-size: 18px;">Placing your order...</p>
    <small class="text-muted">Please do not close this window</small>
</div>

{{-- CONFIRMATION MODAL --}}
<div class="modal fade" id="confirmModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div style="font-size: 50px; margin-bottom: 15px;">🛒</div>
                <h5 class="fw-bold mb-2">Confirm Card Order</h5>
                <p class="text-muted mb-0">Are you sure you want to place this order? Please review the details before confirming.</p>
                <div id="confirmDetails" class="mt-3 p-3 rounded-3 bg-light text-start" style="font-size: 14px;"></div>
            </div>
            <div class="modal-footer border-0 justify-content-center p-4 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal" id="cancelBtn">
                    Cancel
                </button>
                <button type="button" class="btn text-white px-4 rounded-3" id="confirmBtn"
                        style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                    ✅ Yes, Place Order
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Orders Modal --}}
<div class="modal fade" id="ordersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">📋 Your Card Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(isset($orders) && $orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th>Order #</th>
                                    <th>Card Holder</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number ?? $order->id }}</td>
                                    <td>{{ $order->card_holder }}</td>
                                    <td>
                                        @if($order->fixed_amount)
                                            £{{ number_format($order->amount / 100, 2) }}
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
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div style="font-size:40px;">📭</div>
                        <p class="text-muted mt-2">No orders found.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// 1. FORM SUBMIT WITH CONFIRMATION
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    var cardHolder = document.getElementById('card_holder').value;
    var fixedAmount = document.querySelector('input[name="fixed_amount"]:checked').value;
    var amount = document.getElementById('amount').value;
    var email = document.querySelector('input[name="email"]').value;
    var pin = document.querySelector('input[name="pin"]').value;
    
    // Build confirmation details
    var detailsHtml = '<div class="row g-2">';
    detailsHtml += '<div class="col-6"><strong>Card Holder:</strong><br>' + cardHolder + '</div>';
    detailsHtml += '<div class="col-6"><strong>Type:</strong><br>' + (fixedAmount == 1 ? 'Fixed Amount' : 'Variable') + '</div>';
    if (fixedAmount == 1 && amount) {
        detailsHtml += '<div class="col-6"><strong>Amount:</strong><br>£' + parseFloat(amount).toFixed(2) + '</div>';
    }
    detailsHtml += '<div class="col-6"><strong>Email:</strong><br>' + email + '</div>';
    detailsHtml += '<div class="col-6"><strong>PIN:</strong><br>' + '****' + '</div>';
    detailsHtml += '</div>';
    
    document.getElementById('confirmDetails').innerHTML = detailsHtml;
    
    // Show confirmation modal
    var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
});

// 2. CONFIRM BUTTON CLICK - SHOW SPINNER AND SUBMIT
document.getElementById('confirmBtn').addEventListener('click', function() {
    // Hide confirmation modal
    var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    confirmModal.hide();
    
    // Show the spinner
    var overlay = document.getElementById('loadingOverlay');
    overlay.style.display = 'flex';
    overlay.style.setProperty('display', 'flex', 'important');
    
    // Disable the submit button
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerText = 'Processing...';
    document.getElementById('submitBtn').style.opacity = '0.7';
    
    // Wait a bit then submit
    setTimeout(() => {
        document.getElementById('orderForm').submit();
    }, 300);
});

// 3. Fixed/Variable toggle
document.querySelectorAll('input[name="fixed_amount"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var amountField = document.getElementById('amount_field');
        if (this.value == '1') {
            amountField.style.display = 'block';
            if(!document.getElementById('amount').value) {
                document.getElementById('preview_amount').innerText = '£0.00';
            }
        } else {
            amountField.style.display = 'none';
            document.getElementById('amount').value = '';
            document.getElementById('preview_amount').innerText = 'Variable';
        }
    });
});

// 4. Page load check
window.onload = function() {
    var checked = document.querySelector('input[name="fixed_amount"]:checked');
    if (checked && checked.value == '0') {
        document.getElementById('amount_field').style.display = 'none';
        document.getElementById('preview_amount').innerText = 'Variable';
    }
};

// 5. Amount validation
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