@extends('frontend.layouts.home')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/front/css/custom.css') }}">
@endsection
@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>

<section class="donation-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                <div class="donation-page-title">Make a donation</div>
                <div class="donation-page-subtitle">Fill in the details below to complete your donation</div>

                {{-- Error / Success Messages --}}
                <div class="ermsg"></div>

                @if(session()->has('success'))
                    <div class="alert alert-success" id="successMessage">{{ session()->get('success') }}</div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
                @endif

                @if (isset($errors) && $errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="donation-card">
                    <form action="{{ route('front.onlinedonation.store') }}" method="POST" enctype="multipart/form-data" id="DonationForm">
                        @csrf

                        <div class="row">
                            {{-- ===== LEFT COLUMN ===== --}}
                            <div class="col-lg-6">

                                @auth
                                    {{-- Account Balance --}}
                                    <div class="balance-box">
                                        <div>
                                            <div class="balance-label">Account Balance</div>
                                            <div>
                                                <span class="balance-amount">{{ Auth::user()->getLiveBalance() }}</span>
                                                <span class="balance-currency">GBP</span>
                                            </div>
                                        </div>
                                        <div class="balance-badge" id="balanceBadge">
                                            <i class="fas fa-check-circle"></i> Will pay from balance
                                        </div>
                                    </div>
                                @else
                                    {{-- Auth Prompt --}}
                                    <div class="auth-prompt">
                                        <p>Already have an account? Donate directly from your balance.</p>
                                        <div class="auth-links">
                                            <a href="{{ route('login') }}">Log In</a>
                                            <a href="{{ route('register') }}" class="btn-outline-light">Register</a>
                                        </div>
                                    </div>
                                @endauth

                                {{-- Donor Information Section --}}
                                <div class="section-divider">Donor Information</div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name <span class="required-star">*</span></label>
                                            <input type="text" class="form-control" name="first_name" id="first_name"
                                                placeholder="First name" required
                                                value="@auth{{ Auth::user()->name ?? '' }}@else{{ old('first_name') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <span class="required-star">*</span></label>
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                placeholder="Last name" required
                                                value="@auth{{ Auth::user()->surname ?? '' }}@else{{ old('last_name') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address <span class="required-star">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="your@email.com" required
                                        value="@auth{{ Auth::user()->email ?? '' }}@else{{ old('email') }}@endauth"
                                        @auth readonly @endif>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Contact Number <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        placeholder="e.g. 441234567890" required maxlength="13"
                                        value="@auth{{ Auth::user()->phone ?? '' }}@else{{ old('phone') }}@endauth"
                                        @auth readonly @endif>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address_line_1">Address Line 1 <span class="required-star">*</span></label>
                                            <input type="text" class="form-control" name="address_line_1" id="address_line_1"
                                                placeholder="House number/name" required
                                                value="@auth{{ Auth::user()->houseno ?? '' }}@else{{ old('address_line_1') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address_line_2">Address Line 2</label>
                                            <input type="text" class="form-control" name="address_line_2" id="address_line_2"
                                                placeholder="Street name"
                                                value="@auth{{ Auth::user()->streetname ?? '' }}@else{{ old('address_line_2') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address_line_3">Address Line 3</label>
                                            <input type="text" class="form-control" name="address_line_3" id="address_line_3"
                                                placeholder="Street name"
                                                value="@auth{{ Auth::user()->address_third_line ?? '' }}@else{{ old('address_line_3') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="town">Town/City <span class="required-star">*</span></label>
                                            <input type="text" class="form-control" name="town" id="town"
                                                placeholder="Town" required
                                                value="@auth{{ Auth::user()->town ?? '' }}@else{{ old('town') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="postcode">Post Code <span class="required-star">*</span></label>
                                            <input type="text" class="form-control" name="postcode" id="postcode"
                                                placeholder="Post code" required
                                                value="@auth{{ Auth::user()->postcode ?? '' }}@else{{ old('postcode') }}@endauth"
                                                @auth readonly @endif>
                                        </div>
                                    </div>
                                </div>


                                {{-- ===== FEE SECTION ===== --}}
                                <div class="charges-section" style="margin-top: 20px;">

                                    {{-- Fee note --}}
                                    <div style="background: #D5D4CA; border: 1px solid rgba(24, 152, 139, 0.15); border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; font-size: 15px; color: #003057; line-height: 1.5;font-weight: 600;">
                                        <i class="fas fa-info-circle" style="margin-right: 6px; color: #D5D4CA;"></i>
                                        <strong>Note:</strong> A <strong>6% platform fee</strong> will be added to your donation amount.
                                    </div>


                                    {{-- Live breakdown --}}
                                    <div id="amountBreakdown" style="display: none; background: #f0f4f8; border: 1px solid #d0d9e4; border-radius: 8px; padding: 14px 16px; margin-top: 10px; font-size: 14px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                            <span style="color: #555;">Donation Amount</span>
                                            <span id="breakdownBase" style="font-weight: 500;">£0.00</span>
                                        </div>
                                        <div id="breakdownFeeRow" style="display: none;">
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #b45309;">
                                                <span>Platform Fee (6%)</span>
                                                <span id="breakdownFee" style="font-weight: 500;">£0.00</span>
                                            </div>
                                        </div>
                                        <hr style="margin: 8px 0; border-color: #c8d3df;">
                                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 16px; color: #003057;">
                                            <span>Total</span>
                                            <span id="breakdownTotal">£0.00</span>
                                        </div>
                                    </div>

                                </div>
                                {{-- ===== END FEE SECTION ===== --}}




                            </div>

                            {{-- ===== RIGHT COLUMN ===== --}}
                            <div class="col-lg-6 donation-right-col">

                                {{-- Donation Details Section --}}
                                <div class="section-divider">Donation Details</div>

                                <div class="form-group">
                                    <label for="charity_id">Beneficiary <span class="required-star">*</span></label>
                                    <select id="charity_id" name="charity_id" required class="form-control">
                                        <option value="">Select a charity</option>
                                        @foreach (App\Models\Charity::all() as $charity)
                                            <option value="{{ $charity->id }}|{{ $charity->name }}"
                                                {{ old('charity_id') == $charity->id ? 'selected' : '' }}
                                            >{{ $charity->name }} - ({{ $charity->acc_no }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="amount">Amount <span class="required-star">*</span></label>
                                    <div class="amount-group">
                                        <input type="text" class="form-control" name="amount" id="amount"
                                            placeholder="0.00" required
                                            value="{{ $amount ?? old('amount') }}">
                                        <span class="currency-badge">GBP</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="donation-check">
                                        <input type="checkbox" name="ano_donation" id="ano_donation"
                                            {{ old('ano_donation') == 'on' ? 'checked' : '' }}>
                                        <span>Make this an anonymous donation</span>
                                    </label>
                                </div>

                                {{-- Notes Section --}}
                                <div class="section-divider" style="margin-top: 24px;">Notes</div>

                                <div class="form-group">
                                    <label for="charitynote">Notes to charity</label>
                                    <textarea id="charitynote" name="charitynote"
                                        placeholder="Add a message for the charity (optional)...">{{ old('charitynote') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="mynote">My Notes</label>
                                    <textarea id="mynote" name="mynote"
                                        placeholder="Add a personal note (optional)...">{{ old('mynote') }}</textarea>
                                </div>

                                {{-- Confirm Checkbox --}}
                                <div class="form-group">
                                    <div class="confirm-check">
                                        <label class="donation-check mb-0">
                                            <input type="checkbox" name="confirm_donation" id="confirm_donation" required
                                                {{ old('confirm_donation') == 'on' ? 'checked' : '' }}>
                                            <span class="confirm-text">I confirm that this donation is for charitable purposes only, I will not benefit directly or indirectly by way of goods or services from the donation.</span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            {{-- ===== SUBMIT ===== --}}
                            <div class="col-12 mt-3">
                                <input type="hidden" id="userid" name="userid" value="@auth{{ Auth::user()->id }}@endauth">
                                <button type="button" id="donatemodal" class="btn-donate">
                                    Make Donation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ============================================
     STRIPE PAYMENT MODAL
     ============================================ --}}
<div id="stripeModal" class="stripe-modal-overlay">
    <div class="stripe-modal-box">
        <div class="stripe-modal-header">
            <h3>Payment Details</h3>
            <button type="button" id="closeStripeModal" class="stripe-close-btn">&times;</button>
        </div>

        <div class="stripe-modal-body">
            <div class="stripe-summary">
                <div class="stripe-summary-row">
                    <span>Charity</span>
                    <strong id="stripeCharityName">—</strong>
                </div>
                <div class="stripe-summary-row">
                    <span>Amount</span>
                    <strong class="stripe-amount-highlight" id="stripeAmountDisplay">£0.00</strong>
                </div>
            </div>

            <div class="stripe-card-section">
                <label class="stripe-card-label">Card Details</label>
                <div id="card-element" class="stripe-card-element"></div>
                <div id="card-errors" class="stripe-card-errors" role="alert"></div>
            </div>
        </div>

        <div class="stripe-modal-footer">
            <button type="button" id="cancelStripeBtn" class="stripe-btn-cancel">Cancel</button>
            <button type="button" id="payWithStripeBtn" class="stripe-btn-pay" disabled>
                <span class="pay-btn-text">Pay £0.00</span>
                <span class="pay-btn-loading" style="display:none;">
                    <span class="spinner-border spinner-border-sm" role="status"></span> Processing...
                </span>
            </button>
        </div>
    </div>
</div>

{{-- Page Loader --}}
<div class="page-loader" id="pageLoader">
    <div class="spinner-box">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>



@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@ideal-postcodes/address-finder-bundled@4"></script>
<script src="https://js.stripe.com/v3/"></script>

<script>
(function ($) {
    'use strict';

    /* ─── Config ──────────────────────────────────────────────── */
    var STRIPE_PK            = '{{ config("services.stripe.key") }}';
    var CHECK_BALANCE_URL    = '{{ route("front.onlinedonation.check.balance") }}';
    var CREATE_INTENT_URL    = '{{ route("front.onlinedonation.create.intent") }}';
    var STORE_DONATION_URL   = '{{ route("front.onlinedonation.store") }}';
    var IS_LOGGED_IN         = {{ auth()->check() ? 'true' : 'false' }};

    /* ─── Fee Config ──────────────────────────────────────────── */
    var FEE_PERCENT = 6;

    /* ─── Stripe State ────────────────────────────────────────── */
    var stripe, elements, cardElement, currentClientSecret;

    /* ─── DOM ─────────────────────────────────────────────────── */
    var dom = {
        form:                $('#DonationForm'),
        charityId:           $('#charity_id'),
        amount:              $('#amount'),
        anoDonation:         $('#ano_donation'),
        confirmDonation:     $('#confirm_donation'),
        charityNote:         $('#charitynote'),
        myNote:              $('#mynote'),
        userId:              $('#userid'),
        btnDonate:           $('#donatemodal'),
        errorBox:            $('.ermsg'),
        pageLoader:          $('#pageLoader'),
        balanceBadge:        $('#balanceBadge'),
        amountBreakdown:     $('#amountBreakdown'),
        breakdownBase:       $('#breakdownBase'),
        breakdownFee:        $('#breakdownFee'),
        breakdownFeeRow:     $('#breakdownFeeRow'),
        breakdownTotal:      $('#breakdownTotal'),
        modal:               $('#stripeModal'),
        closeBtn:            $('#closeStripeModal'),
        cancelBtn:           $('#cancelStripeBtn'),
        payBtn:              $('#payWithStripeBtn'),
        payBtnText:          $('#payWithStripeBtn .pay-btn-text'),
        payBtnLoading:       $('#payWithStripeBtn .pay-btn-loading'),
        cardErrors:          $('#card-errors'),
        stripeCharity:       $('#stripeCharityName'),
        stripeAmount:        $('#stripeAmountDisplay'),
    };

    /* ─── Charge Calculation Helpers ──────────────────────────── */

    function getBaseAmount() {
        return parseFloat(dom.amount.val()) || 0;
    }

        function calcFee(base) {
        return Math.round(base * (FEE_PERCENT / 100) * 100) / 100;
    }

    function getTotal() {
        var base = getBaseAmount();
        var fee  = calcFee(base);
        return Math.round((base + fee) * 100) / 100;
    }

    /* ─── Update Amount Breakdown Display ─────────────────────── */

    function updateAmountBreakdown() {
        var base = getBaseAmount();

        if (base <= 0) {
            dom.amountBreakdown.hide();
            return;
        }

        var fee   = calcFee(base);
        var total = Math.round((base + fee) * 100) / 100;

        dom.breakdownBase.text(formatGBP(base));

        if (fee > 0) {
            dom.breakdownFeeRow.show();
            dom.breakdownFee.text('+' + formatGBP(fee));
        } else {
            dom.breakdownFeeRow.hide();
        }

        dom.breakdownTotal.text(formatGBP(total));
        dom.amountBreakdown.slideDown(200);
    }

    /* ─── Other Helpers ───────────────────────────────────────── */

    function parseCharity() {
        var raw = dom.charityId.val();
        if (!raw) return null;
        var parts = raw.split('|');
        return { id: parts[0], name: parts[1] || '' };
    }

    function formatGBP(n) {
        return '£' + n.toFixed(2);
    }

    function showError(msg) {
        dom.errorBox.html('<div class="alert alert-danger">' + msg + '</div>');
        $('html, body').animate({ scrollTop: dom.errorBox.offset().top - 100 }, 400);
    }

    function showSuccess(msg) {
        dom.errorBox.html('<div class="alert alert-success">' + msg + '</div>');
        $('html, body').animate({ scrollTop: dom.errorBox.offset().top - 100 }, 400);
    }

    function clearErrors() {
        dom.errorBox.html('');
    }

    function setLoading(on) {
        if (on) {
            dom.pageLoader.show();
            dom.btnDonate.prop('disabled', true).css('opacity', 0.6);
        } else {
            dom.pageLoader.hide();
            dom.btnDonate.prop('disabled', false).css('opacity', 1);
        }
    }

    function resetDonateButton() {
        dom.btnDonate.prop('disabled', false).html('Make Donation');
    }

    /* ─── Form Validation ─────────────────────────────────────── */

    function validateForm() {
        var errors = [];

        if (!dom.charityId.val()) errors.push('Please select a charity.');

        var amt = getBaseAmount();
        if (!amt || amt <= 0) errors.push('Please enter a valid donation amount.');
        if (amt < 0.50)      errors.push('Minimum donation amount is £0.50.');

        if (!dom.confirmDonation.is(':checked')) {
            errors.push('Please confirm that this donation is for charitable purposes only.');
        }

        if (!IS_LOGGED_IN) {
            if (!$('#first_name').val().trim())     errors.push('First name is required.');
            if (!$('#last_name').val().trim())      errors.push('Last name is required.');
            if (!$('#email').val().trim())          errors.push('Email is required.');
            if (!$('#phone').val().trim())          errors.push('Phone number is required.');
            if (!$('#address_line_1').val().trim()) errors.push('Address line 1 is required.');
            if (!$('#town').val().trim())           errors.push('Town/City is required.');
            if (!$('#postcode').val().trim())       errors.push('Post code is required.');
        }

        return errors;
    }

    /* ─── Balance Badge ───────────────────────────────────────── */

    function updateBalanceBadge() {
        if (!IS_LOGGED_IN) { dom.balanceBadge.hide(); return; }

        var total = getTotal();
        if (total <= 0) { dom.balanceBadge.hide(); return; }

        $.ajax({
            url: CHECK_BALANCE_URL,
            method: 'POST',
            data: { amount: total },
            success: function (res) {
                if (res.has_balance) {
                    dom.balanceBadge.removeClass('use-stripe')
                        .html('<i class="fas fa-check-circle"></i> Will pay from balance')
                        .addClass('show');
                } else {
                    dom.balanceBadge.addClass('use-stripe')
                        .html('<i class="fas fa-exclamation-triangle"></i> Insufficient balance — card payment required')
                        .addClass('show');
                }
            }
        });
    }

    /* ─── Stripe Modal ────────────────────────────────────────── */

    function initStripe() {
        if (stripe) return;
        stripe = Stripe(STRIPE_PK);
    }

    function openStripeModal(clientSecret, charityName, totalAmount) {
        currentClientSecret = clientSecret;

        dom.stripeCharity.text(charityName || '—');
        dom.stripeAmount.text(formatGBP(totalAmount));
        dom.payBtnText.text('Pay ' + formatGBP(totalAmount));
        dom.cardErrors.text('');
        dom.payBtn.prop('disabled', true);

        if (cardElement) {
            cardElement.destroy();
            cardElement = null;
        }
        elements = null;

        elements = stripe.elements();

        cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#003057',
                    fontFamily: '"Roboto", sans-serif',
                    fontSize: '14px',
                    iconColor: '#18988B',
                    '::placeholder': { color: '#9e978d' },
                },
                invalid: {
                    color: '#d45273',
                    iconColor: '#d45273',
                },
            }
        });

        cardElement.mount('#card-element');

        cardElement.on('change', function (event) {
            if (event.error) {
                dom.cardErrors.text(event.error.message);
            } else {
                dom.cardErrors.text('');
            }
            dom.payBtn.prop('disabled', !event.complete);
        });

        dom.modal.addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeStripeModal() {
        dom.modal.removeClass('active');
        $('body').css('overflow', '');
        dom.cardErrors.text('');
        dom.payBtnText.show();
        dom.payBtnLoading.hide();
        dom.payBtn.prop('disabled', false);

        if (cardElement) {
            cardElement.destroy();
            cardElement = null;
        }
        elements = null;
        currentClientSecret = null;
    }

    async function processStripePayment() {
        if (!stripe || !cardElement || !currentClientSecret) return;

        var firstName = $('#first_name').val().trim();
        var lastName  = $('#last_name').val().trim();
        var email     = $('#email').val().trim();

        dom.payBtnText.hide();
        dom.payBtnLoading.show();
        dom.payBtn.prop('disabled', true);
        dom.cardErrors.text('');

        try {
            var result = await stripe.confirmCardPayment(currentClientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: (firstName + ' ' + lastName).trim(),
                        email: email,
                    }
                }
            });

            if (result.error) {
                dom.cardErrors.text(result.error.message);
                dom.payBtnText.show();
                dom.payBtnLoading.hide();
                dom.payBtn.prop('disabled', false);
            } else if (result.paymentIntent) {
                closeStripeModal();
                setLoading(true);
                clearErrors();
                submitDonation('stripe', result.paymentIntent.id);
            } else {
                dom.cardErrors.text('Unexpected payment result. Please try again.');
                dom.payBtnText.show();
                dom.payBtnLoading.hide();
                dom.payBtn.prop('disabled', false);
            }
        } catch (err) {
            dom.cardErrors.text('An unexpected error occurred: ' + (err.message || err));
            dom.payBtnText.show();
            dom.payBtnLoading.hide();
            dom.payBtn.prop('disabled', false);
        }
    }

    /* ─── Main Submit Flow ────────────────────────────────────── */

    function handleDonateClick() {
        clearErrors();

        var errors = validateForm();
        if (errors.length) {
            showError(errors.join('<br>'));
            return;
        }

        var baseAmount = getBaseAmount();
        var charity    = parseCharity();

        if (IS_LOGGED_IN) {
            dom.btnDonate.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status"></span> Checking...'
            );

            var total = getTotal();

            $.ajax({
                url: CHECK_BALANCE_URL,
                method: 'POST',
                data: { amount: total },
                success: function (res) {
                    resetDonateButton();

                    if (res.has_balance) {
                        setLoading(true);
                        submitDonation('balance', null);
                    } else {
                        createIntentAndPay(charity, baseAmount);
                    }
                },
                error: function () {
                    resetDonateButton();
                    showError('Could not verify balance. Please try again.');
                }
            });
        } else {
            createIntentAndPay(charity, baseAmount);
        }
    }

    function createIntentAndPay(charity, baseAmount) {
        dom.btnDonate.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status"></span> Preparing payment...'
        );

        $.ajax({
            url: CREATE_INTENT_URL,
            method: 'POST',
            data: {
                amount:       baseAmount,
                charity_id:   dom.charityId.val(),
                ano_donation: dom.anoDonation.is(':checked') ? 1 : 0,
                charitynote:  dom.charityNote.val(),
                mynote:       dom.myNote.val(),
            },
            success: function (res) {
                resetDonateButton();

                if (res.status === 200 && res.client_secret) {
                    initStripe();
                    openStripeModal(res.client_secret, charity ? charity.name : '', res.total_amount);
                } else {
                    showError(res.message || 'Failed to initialize payment.');
                }
            },
            error: function (xhr) {
                resetDonateButton();
                var msg = 'Failed to initialize payment. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showError(msg);
            }
        });
    }

    function submitDonation(paymentMethod, paymentIntentId) {
        var formData = new FormData(dom.form[0]);
        formData.append('payment_method', paymentMethod);

        if (paymentIntentId) {
            formData.append('payment_intent_id', paymentIntentId);
        }

        formData.set('confirm_donation', dom.confirmDonation.is(':checked') ? '1' : '0');
        formData.set('ano_donation', dom.anoDonation.is(':checked') ? '1' : '0');

        $.ajax({
            url: STORE_DONATION_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                setLoading(false);

                if (res.status === 300) {
                    showSuccess(res.message);
                    dom.form[0].reset();
                    dom.charityId.val('').trigger('change');
                    dom.balanceBadge.removeClass('show use-stripe');
                    dom.amountBreakdown.hide();
                    setTimeout(function () { window.location.reload(); }, 2500);
                } else if (res.status === 303) {
                    showError(res.message);
                } else {
                    showError('Unexpected response from server. Please try again.');
                }
            },
            error: function (xhr) {
                setLoading(false);

                var msg = 'Something went wrong. Please try again.';
                if (xhr.status === 419) {
                    msg = 'Session expired. Please refresh the page and try again.';
                } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errs = [];
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        errs.push(val[0]);
                    });
                    msg = errs.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showError(msg);
            }
        });
    }

    /* ─── Event Bindings ──────────────────────────────────────── */

    function bindEvents() {
        dom.btnDonate.on('click', handleDonateClick);
        dom.closeBtn.on('click', closeStripeModal);
        dom.cancelBtn.on('click', closeStripeModal);
        dom.payBtn.on('click', processStripePayment);

        dom.modal.on('click', function (e) {
            if (e.target === this) closeStripeModal();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && dom.modal.hasClass('active')) closeStripeModal();
        });

        dom.amount.on('input', debounce(function () {
            updateBalanceBadge();
            updateAmountBreakdown();
        }, 400));
    }

    function debounce(fn, delay) {
        var timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn, delay);
        };
    }

    /* ─── Initialize ──────────────────────────────────────────── */

    function init() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        dom.charityId.select2({
            placeholder: 'Select a charity',
            allowClear: true,
            width: '100%'
        });

        if (typeof IdealPostcodes !== 'undefined') {
            IdealPostcodes.AddressFinder.watch({
                apiKey: "ak_lt4ke30geFynIWbUB7nPMdpkvxGcP",
                outputFields: {
                    line_1: "#address_line_1",
                    line_2: "#address_line_2",
                    line_3: "#address_line_3",
                    post_town: "#town",
                    postcode: "#postcode"
                }
            });
        }

        if (IS_LOGGED_IN) {
            updateBalanceBadge();
        }
        updateAmountBreakdown();
        bindEvents();

        @if($charity_id && $charityName)
        dom.charityId.val("{{ $charity_id }}|{{ $charityName }}").trigger('change');
        @endif

        console.log('[DONATION] Initialization complete');
    }

    

    $(init);

})(jQuery);
</script>
@endsection