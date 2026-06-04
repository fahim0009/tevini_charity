@extends('frontend.layouts.home')

@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>

<style>

        /* ============================================
       Online Donation Page
       ============================================ */

    .donation-page {
        background-color: #E8E1D9;
        padding: 60px 0;
        min-height: 70vh;
    }

    .donation-page-title {
        font-size: 48px;
        line-height: 1;
        color: #18988B;
        font-family: "DarkerGrotesque-semibold";
        margin-bottom: 10px;
    }

    .donation-page-subtitle {
        font-family: "Roboto-Regular";
        font-size: 16px;
        color: #4E4B44;
        margin-bottom: 40px;
    }

    .donation-card {
        background: #E1D8CE;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    /* --- Auth Prompt (for non-logged in) --- */
    .auth-prompt {
        background: #003057;
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .auth-prompt p {
        color: rgba(255, 255, 255, 0.85);
        font-family: "Roboto-Regular";
        font-size: 14px;
        margin: 0;
    }

    .auth-prompt .auth-links {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .auth-prompt .auth-links a {
        display: inline-block;
        padding: 8px 20px;
        background-color: #18988B;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.3s ease-in-out;
    }

    .auth-prompt .auth-links a:hover {
        background-color: #147a70;
        color: #ffffff;
    }

    .auth-prompt .auth-links a.btn-outline-light {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #ffffff;
    }

    .auth-prompt .auth-links a.btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #ffffff;
    }

    /* --- Balance Box (for logged in) --- */
    .balance-box {
        background: #003057;
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .balance-box .balance-label {
        font-family: "Roboto-Regular";
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 2px;
    }

    .balance-box .balance-amount {
        font-family: "DarkerGrotesque-bold";
        font-size: 32px;
        color: #ffffff;
        line-height: 1;
    }

    .balance-box .balance-currency {
        font-family: "Roboto-Regular";
        font-size: 14px;
        color: rgba(255, 255, 255, 0.6);
        margin-left: 4px;
    }

    /* --- Section Divider --- */
    .section-divider {
        font-family: "DarkerGrotesque-semibold";
        font-size: 18px;
        color: #003057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(0, 48, 87, 0.15);
    }

    /* --- Form Labels --- */
    .donation-card label {
        font-family: "Roboto-Regular";
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4E4B44;
        margin-bottom: 6px;
        display: block;
    }

    /* --- Form Controls --- */
    .donation-card .form-control,
    .donation-card .form-select {
        border: 1px solid rgba(0, 48, 87, 0.15);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        font-family: "Roboto-Regular";
        color: #003057;
        background-color: #E8E1D9;
        transition: all 0.3s ease-in-out;
        height: 44px;
    }

    .donation-card .form-control:focus,
    .donation-card .form-select:focus {
        border-color: #18988B;
        box-shadow: 0 0 0 3px rgba(24, 152, 139, 0.15);
        background-color: #ffffff;
        outline: none;
    }

    .donation-card .form-control::placeholder {
        color: #9e978d;
    }

    .donation-card .form-control[readonly] {
        background-color: rgba(0, 48, 87, 0.04);
        cursor: default;
    }

    /* --- Amount Input Group --- */
    .amount-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .amount-group .form-control {
        flex: 1;
    }

    .amount-group .currency-badge {
        font-family: "DarkerGrotesque-bold";
        font-size: 16px;
        color: #003057;
        background: rgba(0, 48, 87, 0.08);
        padding: 10px 16px;
        border-radius: 8px;
        white-space: nowrap;
        height: 44px;
        display: flex;
        align-items: center;
    }

    /* --- Checkbox Custom --- */
    .donation-check {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
    }

    .donation-check input[type="checkbox"] {
        width: 20px;
        height: 20px;
        min-width: 20px;
        margin-top: 2px;
        accent-color: #18988B;
        cursor: pointer;
    }

    .donation-check span {
        font-family: "Roboto-Regular";
        font-size: 14px;
        color: #4E4B44;
        line-height: 1.5;
    }

    /* --- Textarea --- */
    .donation-card textarea {
        border: 1px solid rgba(0, 48, 87, 0.15);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
        font-family: "Roboto-Regular";
        color: #003057;
        background-color: #E8E1D9;
        resize: vertical;
        transition: all 0.3s ease-in-out;
        width: 100%;
        height: 120px;
    }

    .donation-card textarea:focus {
        border-color: #18988B;
        box-shadow: 0 0 0 3px rgba(24, 152, 139, 0.15);
        background-color: #ffffff;
        outline: none;
    }

    .donation-card textarea::placeholder {
        color: #9e978d;
    }

    /* --- Right Column Divider --- */
    .donation-right-col {
        border-left: 2px solid rgba(0, 48, 87, 0.08);
        padding-left: 30px;
    }

    /* --- Confirm Checkbox --- */
    .confirm-check {
        background: rgba(24, 152, 139, 0.06);
        border-radius: 10px;
        padding: 16px;
        border: 1px solid rgba(24, 152, 139, 0.15);
    }

    .confirm-check input[type="checkbox"] {
        width: 22px;
        height: 22px;
        min-width: 22px;
        accent-color: #18988B;
        cursor: pointer;
    }

    .confirm-check .confirm-text {
        font-family: "Roboto-Regular";
        font-size: 13px;
        color: #4E4B44;
        line-height: 1.6;
    }

    /* --- Submit Button --- */
    .btn-donate {
        display: inline-block;
        padding: 14px 50px;
        background-color: #003057;
        color: #ffffff;
        font-family: "DarkerGrotesque-bold";
        font-size: 16px;
        border-radius: 30px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        text-decoration: none;
    }

    .btn-donate:hover {
        background-color: #18988B;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(24, 152, 139, 0.3);
        color: #ffffff;
    }

    .btn-donate:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* --- Form Group Spacing --- */
    .donation-card .form-group {
        margin-bottom: 16px;
    }

    /* --- Error/Success Messages --- */
    .donation-page .alert {
        border-radius: 8px;
        font-family: "Roboto-Regular";
        font-size: 14px;
        padding: 12px 18px;
        margin-bottom: 20px;
    }

    .donation-page .alert-success {
        background: rgba(24, 152, 139, 0.1);
        border: 1px solid rgba(24, 152, 139, 0.3);
        color: #147a70;
    }

    .donation-page .alert-danger {
        background: rgba(212, 82, 115, 0.1);
        border: 1px solid rgba(212, 82, 115, 0.3);
        color: #b33d57;
    }

    .ermsg .alert {
        border-radius: 8px;
        font-family: "Roboto-Regular";
        font-size: 14px;
        padding: 12px 18px;
        margin-bottom: 0;
    }

    /* --- Select2 Override --- */
    .select2-container--default .select2-selection--single {
        border: 1px solid rgba(0, 48, 87, 0.15) !important;
        border-radius: 8px !important;
        height: 44px !important;
        background-color: #E8E1D9 !important;
        padding: 6px 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #003057 !important;
        font-family: "Roboto-Regular" !important;
        font-size: 14px !important;
        padding-left: 0 !important;
        line-height: 32px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9e978d !important;
    }

    .select2-container--focus .select2-selection--single {
        border-color: #18988B !important;
        box-shadow: 0 0 0 3px rgba(24, 152, 139, 0.15) !important;
        background-color: #ffffff !important;
    }

    .select2-dropdown {
        border: 1px solid rgba(0, 48, 87, 0.15) !important;
        border-radius: 8px !important;
        overflow: hidden;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #18988B !important;
    }

    /* --- Required Star --- */
    .required-star {
        color: #d45273;
        margin-left: 2px;
    }

    /* ============================================
       Responsive
       ============================================ */

    @media (max-width: 991px) {
        .donation-page-title {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .donation-page-subtitle {
            margin-bottom: 30px;
        }

        .donation-card {
            padding: 30px;
        }

        .donation-right-col {
            border-left: none;
            padding-left: 0;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid rgba(0, 48, 87, 0.08);
        }

        .balance-box .balance-amount {
            font-size: 26px;
        }
    }

    @media (max-width: 767px) {
        .donation-page {
            padding: 40px 0;
        }

        .donation-page-title {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .donation-page-subtitle {
            font-size: 14px;
            margin-bottom: 24px;
        }

        .donation-card {
            padding: 20px;
        }

        .balance-box,
        .auth-prompt {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
        }

        .balance-box .balance-amount {
            font-size: 24px;
        }

        .auth-prompt .auth-links {
            width: 100%;
        }

        .auth-prompt .auth-links a {
            flex: 1;
            text-align: center;
        }

        .btn-donate {
            width: 100%;
            text-align: center;
            padding: 14px 30px;
            font-size: 15px;
        }

        .section-divider {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .donation-page-title {
            font-size: 24px;
        }

        .amount-group {
            flex-direction: column;
            align-items: stretch;
        }

        .amount-group .currency-badge {
            justify-content: center;
        }
    }
</style>

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
                                                @auth readonly @endif readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address_line_3">Address Line 3</label>
                                            <input type="text" class="form-control" name="address_line_3" id="address_line_3" 
                                                placeholder="Street name"
                                                value="@auth{{ Auth::user()->address_third_line ?? '' }}@else{{ old('address_third_line') }}@endauth"
                                                @auth readonly @endif readonly>
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
                                                @auth readonly @endif readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="postcode">Post Code <span class="required-star">*</span></label>
                                            <input type="text" class="form-control" name="postcode" id="postcode" 
                                                placeholder="Post code" required
                                                value="@auth{{ Auth::user()->postcode ?? '' }}@else{{ old('postcode') }}@endauth"
                                                @auth readonly @endif readonly>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- ===== RIGHT COLUMN ===== --}}
                            <div class="col-lg-6 donation-right-col">
                                
                                {{-- Donation Details Section --}}
                                <div class="section-divider">Donation Details</div>

                                <div class="form-group">
                                    <label for="charity_id">Beneficiary <span class="required-star">*</span></label>
                                    <select id="charity_id" name="charity_id" required class="form-select select2">
                                        <option value="">Select a charity</option>
                                        @foreach (App\Models\Charity::all() as $charity)
                                            <option value="{{ $charity->id }}|{{ $charity->name }}" 
                                                {{ old('charity_id') == $charity->id ? 'selected' : '' }}
                                                @if (isset($cid) && $charity->id == $cid) selected @endif
                                            >{{ $charity->name }} - ({{ $charity->acc_no }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="amount">Amount <span class="required-star">*</span></label>
                                    <div class="amount-group">
                                        <input type="text" class="form-control" name="amount" id="amount" 
                                            placeholder="0.00" required
                                            value="@if(isset($amount)){{ $amount }}@endif{{ old('amount') }}">
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

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@ideal-postcodes/address-finder-bundled@4"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
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
    });


    $(document).ready(function () {

        // Initialize Select2
        $('#charity_id').select2({
            placeholder: 'Select a charity',
            allowClear: true,
            width: '100%'
        });

        // CSRF setup
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Submit donation
        var url = "{{ route('front.onlinedonation.store') }}";
        $('#donatemodal').click(function () {

            alert('work in progress...');

            // Validate form
            var form = $('#DonationForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var $button = $(this);
            $button.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
            );

            var formData = new FormData($('#DonationForm')[0]);

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (d) {
                    if (d.status == 303) {
                        $('.ermsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                        $button.prop('disabled', false).html('Make Donation');
                        $('html, body').animate({
                            scrollTop: $('.ermsg').offset().top - 100
                        }, 500);
                    } else if (d.status == 200) {
                        $('.ermsg').html('<div class="alert alert-success">' + d.message + '</div>');
                        $('html, body').animate({
                            scrollTop: $('.ermsg').offset().top - 100
                        }, 500);
                        window.setTimeout(function () { location.reload(); }, 2000);
                    }
                },
                error: function (d) {
                    console.log(d);
                    $button.prop('disabled', false).html('Make Donation');
                }
            });
        });
    });
</script>
@endsection