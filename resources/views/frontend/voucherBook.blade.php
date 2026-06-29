@extends('frontend.layouts.home')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/front/css/voucherbook.css') }}">
@endsection
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.min.css">

<div class="page-loader" id="pageLoader">
    <div class="spinner-box">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<section class="voucher-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                <div class="voucher-page-title">Order voucher books</div>
                <div class="voucher-page-subtitle">Select voucher books and complete your order</div>

                <div class="ermsg"></div>

                @if(session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                @if (isset($errors) && $errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="voucher-card">

                    {{-- AUTH / BALANCE --}}
                    @auth
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
                        <div class="auth-prompt">
                            <p>Have an account? Log in to use your balance for faster ordering.</p>
                            <div class="auth-links">
                                <a href="{{ route('login') }}">Log In</a>
                                <a href="{{ route('register') }}" class="btn-outline-light">Register</a>
                            </div>
                        </div>
                    @endauth

                    {{-- ===== DONOR INFORMATION ===== --}}
                    <div class="full-section">
                        <div class="section-divider">Donor Information</div>
                        <div class="donor-grid">
                            <div class="form-group">
                                <label class="form-label-custom">First Name <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First name" required
                                    value="@auth{{ Auth::user()->name ?? '' }}@else{{ old('first_name') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Last Name <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last name" required
                                    value="@auth{{ Auth::user()->surname ?? '' }}@else{{ old('last_name') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Email Address <span class="required-star">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="your@email.com" required
                                    value="@auth{{ Auth::user()->email ?? '' }}@else{{ old('email') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Contact Number <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="e.g. 441234567890" required maxlength="13"
                                    value="@auth{{ Auth::user()->phone ?? '' }}@else{{ old('phone') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Address Line 1 <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="address_line_1" id="address_line_1" placeholder="House number/name" required
                                    value="@auth{{ Auth::user()->houseno ?? '' }}@else{{ old('address_line_1') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Address Line 2</label>
                                <input type="text" class="form-control" name="address_line_2" id="address_line_2" placeholder="Street name"
                                    value="@auth{{ Auth::user()->streetname ?? '' }}@else{{ old('address_line_2') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Town/City <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="town" id="town" placeholder="Town" required
                                    value="@auth{{ Auth::user()->town ?? '' }}@else{{ old('town') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Post Code <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="postcode" id="postcode" placeholder="Post code" required
                                    value="@auth{{ Auth::user()->postcode ?? '' }}@else{{ old('postcode') }}@endauth"
                                    @auth readonly @endif>
                            </div>
                        </div>
                    </div>

                    {{-- ===== SELECT VOUCHER BOOKS ===== --}}
                    <div class="full-section">
                        <div class="section-divider">Select Voucher Books</div>

                        @php
                            $vouchers = App\Models\Voucher::where('status', '=', '1')
                                ->where('type', '=', 'Prepaid')
                                ->get();

                            $numberWords = [
                                '0.50' => 'Fifty pence only',
                                '1' => 'One pound only',
                                '2' => 'Two pounds only',
                                '3' => 'Three pounds only',
                                '5' => 'Five pounds only',
                                '10' => 'Ten pounds only',
                                '20' => 'Twenty pounds only',
                                '50' => 'Fifty pounds only',
                                '100' => 'One hundred pounds only',
                            ];
                        @endphp

                        {{-- Using Bootstrap row, NOT CSS Grid --}}
                        <div class="row">
                            @foreach ($vouchers as $voucher)
                                @php
                                    $isBlank = ($voucher->single_amount == "0");
                                    $amt = $voucher->single_amount;
                                    $words = $numberWords[$amt] ?? strtoupper($voucher->note);
                                    $voucherNo = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

                                    $bars = '';
                                    for ($i = 0; $i < 28; $i++) {
                                        $h = rand(60, 100);
                                        $w = ($i % 3 == 0) ? 3 : 1;
                                        $bars .= '<div class="bar" style="height:'.$h.'%;width:'.$w.'px;"></div>';
                                    }
                                @endphp

                                <div class="col-lg-4 col-md-6">
                                    <div class="voucher-book-col">
                                        <div class="voucher-book-wrapper">
                                            <div class="voucher-face {{ $isBlank ? 'blank-cheque' : '' }}">
                                                <div class="voucher-header">
                                                    <div class="voucher-number">NO.{{ $voucherNo }}</div>
                                                    <div class="voucher-charity-label">
                                                        <span class="reg-text">Registered Charity</span>
                                                        <span class="reg-number">282079</span>
                                                    </div>
                                                </div>

                                                <div class="voucher-address">
                                                    5a Holmdale Terrace<br>
                                                    London N15 5PP
                                                </div>

                                                <div class="voucher-body">
                                                    <div class="voucher-pay-section">
                                                        <div class="voucher-pay-label">Please pay</div>
                                                        <div class="voucher-amount-words">
                                                            @if ($isBlank)
                                                                Blank cheque
                                                            @else
                                                                {{ $words }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="voucher-amount-box">
                                                        @if ($isBlank)
                                                            <div class="amount-value">Blank</div>
                                                            <div class="amount-currency">cheque</div>
                                                        @else
                                                            <div class="amount-value">£{{ $voucher->single_amount }}</div>
                                                            <div class="amount-currency">sterling</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="voucher-footer">
                                                    <div class="voucher-barcode">{!! $bars !!}</div>
                                                    <div class="voucher-signature">
                                                        <div class="sig-line"></div>
                                                        <div class="sig-label">Signature</div>
                                                    </div>
                                                </div>

                                                <div class="voucher-brand">tevini</div>
                                            </div>

                                            <button type="button"
                                                class="voucher-add-btn add-to-cart"
                                                voucherID="{{ $voucher->id }}"
                                                v_amount="{{ $voucher->amount }}"
                                                v_type="{{ $voucher->type }}"
                                                v_note="{{ $voucher->note }}"
                                                single_amount="{{ $voucher->single_amount }}"
                                                data-type="{{ $voucher->type }}">
                                                +
                                            </button>
                                        </div>

                                        <div class="voucher-note-tag">
                                            <span class="note-count">{{ $voucher->note }}</span>
                                            @if (!$isBlank)
                                                <br>= £{{ $voucher->amount }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ===== YOUR BASKET ===== --}}
                    <div class="full-section">
                        <div class="section-divider">Your Basket</div>

                        <div class="delivery-warning" id="deliveryWarning">
                            <strong>Note:</strong> £3.50 delivery charge applies on prepaid voucher orders under £200.
                        </div>

                        <table class="basket-table" id="basketTable">
                            <thead>
                                <tr>
                                    <th width="40px"></th>
                                    <th>Voucher</th>
                                    <th width="80px">Qty</th>
                                </tr>
                            </thead>
                            <tbody id="basketBody">
                                @if(auth()->check())
                                    @foreach ($cart as $item)
                                        @php $cartVoucher = \App\Models\Voucher::where('id', $item->voucher_id)->first(); @endphp
                                        <tr class="basket-row" data-cart-id="{{ $item->id }}">
                                            <td><button type="button" class="remove-btn remove-from-cart" data-cartid="{{ $item->id }}">×</button></td>
                                            <td>
                                                <input type="hidden" value="{{ $item->voucher_id }}" name="v_ids[]">
                                                <input type="hidden" class="row-total" id="sub{{ $item->voucher_id }}" value="{{ $item->tamount }}">
                                                <div class="basket-voucher-name">
                                                    @if($cartVoucher && $cartVoucher->single_amount == "0") Blank Cheque @else £{{ $cartVoucher->single_amount ?? '' }} @endif
                                                    <span class="voucher-badge prepaid" style="font-size:9px;">Prepaid</span>
                                                </div>
                                                <div class="basket-voucher-note">{{ $cartVoucher->note ?? '' }} = £{{ $cartVoucher->amount ?? '' }}</div>
                                            </td>
                                            <td>
                                                <input type="text" class="qty-input basket-qty" name="qty[]" value="{{ $item->qty }}"
                                                    v_amount="{{ $item->tamount }}" v_type="Prepaid" data-type="Prepaid"
                                                    vid="{{ $item->voucher_id }}" id="cartValue{{ $item->voucher_id }}"
                                                    onkeypress="return /[0-9]/i.test(event.key)">
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($cart as $item)
                                        @php $cartVoucher = \App\Models\Voucher::where('id', $item['voucher_id'])->first(); @endphp
                                        <tr class="basket-row" data-cart-id="{{ $item['id'] }}">
                                            <td><button type="button" class="remove-btn remove-from-cart" data-cartid="{{ $item['id'] }}">×</button></td>
                                            <td>
                                                <input type="hidden" value="{{ $item['voucher_id'] }}" name="v_ids[]">
                                                <input type="hidden" class="row-total" id="sub{{ $item['voucher_id'] }}" value="{{ $item['tamount'] }}">
                                                <div class="basket-voucher-name">
                                                    @if($cartVoucher && $cartVoucher->single_amount == "0") Blank Cheque @else £{{ $cartVoucher->single_amount ?? '' }} @endif
                                                    <span class="voucher-badge prepaid" style="font-size:9px;">Prepaid</span>
                                                </div>
                                                <div class="basket-voucher-note">{{ $cartVoucher->note ?? '' }} = £{{ $cartVoucher->amount ?? '' }}</div>
                                            </td>
                                            <td>
                                                <input type="text" class="qty-input basket-qty" name="qty[]" value="{{ $item['qty'] }}"
                                                    v_amount="{{ $item['tamount'] }}" v_type="Prepaid" data-type="Prepaid"
                                                    vid="{{ $item['voucher_id'] }}" id="cartValue{{ $item['voucher_id'] }}"
                                                    onkeypress="return /[0-9]/i.test(event.key)">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        <div class="basket-empty" id="basketEmpty"
                            @if(!empty($cart) && (auth()->check() ? $cart->count() > 0 : count($cart) > 0)) style="display:none;" @endif>
                            Your basket is empty. Click + to add voucher books.
                        </div>
                    </div>

                    {{-- ===== DELIVERY ===== --}}
                    <div class="full-section">
                        <div class="section-divider">Delivery</div>
                        <div class="delivery-grid">
                            <label class="delivery-option" id="deliveryOptionLabel">
                                <input type="checkbox" id="delivery" name="delivery" class="delivery_option">
                                <div>
                                    <div class="delivery-title">Express delivery</div>
                                    <div class="delivery-desc">1-2 working days</div>
                                </div>
                            </label>
                            <label class="delivery-option" id="collectionOptionLabel">
                                <input type="checkbox" id="collection" name="collection" class="delivery_option">
                                <div>
                                    <div class="delivery-title">Collection</div>
                                    <div class="delivery-desc">
                                        100 Fairholt Rd, London N16 5HN<br>
                                        Mon – Thu: 10:00 – 17:00 | Fri: 10:00 – 13:00
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ===== ORDER TOTAL BAR ===== --}}
                    <div class="order-total-bar">
                        <div class="address-info">
                            <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M8.16666 13.167H9.83332V8.16699H8.16666V13.167ZM8.99999 6.50033C9.2361 6.50033 9.43416 6.42033 9.59416 6.26033C9.7536 6.10088 9.83332 5.9031 9.83332 5.66699C9.83332 5.43088 9.7536 5.23283 9.59416 5.07283C9.43416 4.91338 9.2361 4.83366 8.99999 4.83366C8.76388 4.83366 8.5661 4.91338 8.40666 5.07283C8.24666 5.23283 8.16666 5.43088 8.16666 5.66699C8.16666 5.9031 8.24666 6.10088 8.40666 6.26033C8.5661 6.42033 8.76388 6.50033 8.99999 6.50033ZM8.99999 17.3337C7.84721 17.3337 6.76388 17.1148 5.74999 16.677C4.7361 16.2398 3.85416 15.6462 3.10416 14.8962C2.35416 14.1462 1.76055 13.2642 1.32332 12.2503C0.885545 11.2364 0.666656 10.1531 0.666656 9.00033C0.666656 7.84755 0.885545 6.76421 1.32332 5.75033C1.76055 4.73644 2.35416 3.85449 3.10416 3.10449C3.85416 2.35449 4.7361 1.7606 5.74999 1.32283C6.76388 0.885603 7.84721 0.666992 8.99999 0.666992C10.1528 0.666992 11.2361 0.885603 12.25 1.32283C13.2639 1.7606 14.1458 2.35449 14.8958 3.10449C15.6458 3.85449 16.2394 4.73644 16.6767 5.75033C17.1144 6.76421 17.3333 7.84755 17.3333 9.00033C17.3333 10.1531 17.1144 11.2364 16.6767 12.2503C16.2394 13.2642 15.6458 14.1462 14.8958 14.8962C14.1458 15.6462 13.2639 16.2398 12.25 16.677C11.2361 17.1148 10.1528 17.3337 8.99999 17.3337ZM8.99999 15.667C10.8611 15.667 12.4375 15.0212 13.7292 13.7295C15.0208 12.4378 15.6667 10.8614 15.6667 9.00033C15.6667 7.13921 15.0208 5.56283 13.7292 4.27116C12.4375 2.97949 10.8611 2.33366 8.99999 2.33366C7.13888 2.33366 5.56249 2.97949 4.27082 4.27116C2.97916 5.56283 2.33332 7.13921 2.33332 9.00033C2.33332 10.8614 2.97916 12.4378 4.27082 13.7295C5.56249 15.0212 7.13888 15.667 8.99999 15.667Z" fill="rgba(255,255,255,0.6)"/>
                            </svg>
                            @auth
                                Delivery address: {{ Auth::user()->houseno }}, {{ Auth::user()->streetname ?? '' }} {{ Auth::user()->town }} {{ Auth::user()->postcode }}
                            @else
                                Delivery address will be taken from the form above.
                            @endauth
                        </div>
                        <div class="total-area">
                            <span class="total-label">Order total</span>
                            <input type="text" id="net_total" class="total-input" value="" placeholder="£0.00" readonly>
                            <input type="hidden" value="@auth{{ auth()->user()->id }}@endauth" id="donner_id">
                            <button type="button" class="btn-place-order" id="placeOrderBtn">Place order</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stripe.js --}}
<script src="https://js.stripe.com/v3/"></script>

    <style>
        /* ============================================
        STRIPE PAYMENT MODAL
        ============================================ */

        #stripeModal {
            display: none;
        }

        #stripeModal.show {
            display: flex !important;
        }

        #stripeModal .StripeElement {
            background-color: white;
            padding: 0;
        }

        #card-element {
            transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        #card-element.StripeElement--focus {
            border-color: #18988B;
            box-shadow: 0 0 0 3px rgba(24, 152, 139, 0.15);
            background-color: #ffffff;
        }

        #card-element.StripeElement--invalid {
            border-color: #d45273;
            background-color: #fff5f5;
        }

        #payWithStripeBtn:hover:not(:disabled) {
            background-color: #147a70;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(24, 152, 139, 0.3);
        }

        #payWithStripeBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        #closeStripeModal:hover {
            color: #d45273;
        }
    </style>

{{-- Stripe Card Element Container (hidden by default, shown when Stripe needed) --}}
<div id="stripeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:#E1D8CE; border-radius:12px; padding:30px; max-width:440px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="font-family:'DarkerGrotesque-bold'; color:#003057; margin:0; font-size:20px;">Payment Details</h3>
            <button type="button" id="closeStripeModal" style="background:none; border:none; font-size:24px; color:#6A757C; cursor:pointer; padding:0; line-height:1;">&times;</button>
        </div>
        <div id="stripeAmountDisplay" style="font-family:'DarkerGrotesque-bold'; font-size:24px; color:#18988B; text-align:center; margin-bottom:16px;"></div>
        <div id="card-element" style="border:1px solid rgba(0,48,87,0.15); border-radius:8px; padding:14px; background:#E8E1D9; margin-bottom:8px;"></div>
        <div id="card-errors" style="color:#d45273; font-size:13px; min-height:20px; margin-bottom:12px; font-family:'Roboto-Regular';"></div>
        <button type="button" id="payWithStripeBtn" style="width:100%; padding:14px; background:#18988B; color:#fff; border:none; border-radius:25px; font-family:'DarkerGrotesque-bold'; font-size:16px; cursor:pointer; transition:all 0.3s;">
            Pay Now
        </button>
        <p style="text-align:center; margin:12px 0 0; font-size:11px; color:#9e978d; font-family:'Roboto-Regular';">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9e978d" stroke-width="2" style="vertical-align:middle; margin-right:3px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Secured by Stripe. Your card info is never stored on our servers.
        </p>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function () {

        // ==========================================
        // CONFIG
        // ==========================================
        var isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        var cartStoreUrl = "{{ auth()->check() ? route('orderbook.cart.store') : route('guest.voucher.cart.store') }}";
        var balanceOrderUrl = "{{ URL::to('/user/addvoucher') }}";
        var paymentIntentUrl = "{{ route('payment.intent') }}";

        @if(auth()->check())
            var userBalance = parseFloat("{{ Auth::user()->getLiveBalance() }}") || 0;
        @else
            var userBalance = 0;
        @endif

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // ==========================================
        // STRIPE INITIALIZATION
        // ==========================================
        var stripe = null;
        var elements = null;
        var cardElement = null;

        try {
            stripe = Stripe('{{ config("services.stripe.key") }}');
            elements = stripe.elements({
                fonts: [
                    { cssSrc: 'https://fonts.googleapis.com/css2?family=DarkerGrotesque:wght@700' }
                ]
            });
            cardElement = elements.create('card', {
                style: {
                    base: {
                        fontFamily: '"Roboto-Regular", sans-serif',
                        fontSize: '15px',
                        color: '#003057',
                        '::placeholder': {
                            color: '#9e978d'
                        }
                    }
                }
            });
            cardElement.mount('#card-element');

            // Real-time card validation errors
            cardElement.on('change', function (event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        } catch (e) {
            console.log('Stripe not configured:', e.message);
        }

        // Close modal
        $('#closeStripeModal').on('click', function () {
            $('#stripeModal').removeClass('show');
        });

        // Close on backdrop click
        $('#stripeModal').on('click', function (e) {
            if ($(e.target).is('#stripeModal')) {
                $('#stripeModal').removeClass('show');
            }
        });

        // ==========================================
        // TOGGLE DELIVERY OPTIONS
        // ==========================================
        $('.delivery_option').on('change', function () {
            if (this.checked) {
                $('.delivery_option').not(this).prop('checked', false);
            }
            $('#deliveryOptionLabel, #collectionOptionLabel').removeClass('selected');
            if ($('#delivery').is(':checked')) $('#deliveryOptionLabel').addClass('selected');
            if ($('#collection').is(':checked')) $('#collectionOptionLabel').addClass('selected');
            recalcTotal();
        });

        // ==========================================
        // ADD TO CART
        // ==========================================
        $(document).on('click', '.add-to-cart', function (e) {
            e.preventDefault();

            var v_amount = $(this).attr('v_amount');
            var voucherID = $(this).attr('voucherID');
            var v_type = $(this).attr('v_type');
            var v_note = $(this).attr('v_note');
            var single_amount = $(this).attr('single_amount');
            var quantity = 1;

            var $btn = $(this);
            $btn.css('transform', 'scale(0.8)');
            setTimeout(function () { $btn.css('transform', ''); }, 200);

            var existingInput = $('#cartValue' + voucherID);
            if (existingInput.length) {
                var newQty = (parseFloat(existingInput.val()) || 0) + 1;
                existingInput.val(newQty);
                $('#sub' + voucherID).val(newQty * v_amount);
                Swal.fire({ icon: 'success', title: 'Updated!', showConfirmButton: false, timer: 1200 });
                recalcTotal();
                return;
            }

            $.ajax({
                url: cartStoreUrl,
                method: "POST",
                data: { v_amount, voucherID, v_type, v_note, single_amount, quantity },
                success: function (d) {
                    if (d.status == 303) {
                        $('.ermsg').html(d.message);
                        $('html, body').animate({ scrollTop: 0 }, 500);
                    } else if (d.status == 300) {
                        var nameText = single_amount == 0 ? 'Blank Cheque' : '£' + single_amount;
                        var noteText = v_note + ' = £' + v_amount;

                        var markup = '<tr class="basket-row" data-cart-id="' + d.newID + '">';
                        markup += '<td><button type="button" class="remove-btn remove-from-cart" data-cartid="' + d.newID + '">×</button></td>';
                        markup += '<td>';
                        markup += '<input type="hidden" value="' + voucherID + '" name="v_ids[]">';
                        markup += '<input type="hidden" class="row-total" id="sub' + voucherID + '" value="' + v_amount + '">';
                        markup += '<div class="basket-voucher-name">' + nameText + ' <span class="voucher-badge prepaid" style="font-size:9px;">Prepaid</span></div>';
                        markup += '<div class="basket-voucher-note">' + noteText + '</div>';
                        markup += '</td>';
                        markup += '<td><input type="text" class="qty-input basket-qty" name="qty[]" value="' + quantity + '" v_amount="' + v_amount + '" v_type="Prepaid" data-type="Prepaid" vid="' + voucherID + '" id="cartValue' + voucherID + '" onkeypress="return /[0-9]/i.test(event.key)"></td>';
                        markup += '</tr>';

                        $('#basketBody').append(markup);
                        $('#basketEmpty').hide();
                        Swal.fire({ icon: 'success', title: 'Added to basket!', showConfirmButton: false, timer: 1200 });
                        recalcTotal();
                    }
                },
                error: function (d) {
                    if (d.status === 419) {
                        Swal.fire({ icon: 'error', title: 'Session expired', text: 'Please refresh the page.' });
                    }
                }
            });
        });

        // ==========================================
        // REMOVE FROM CART
        // ==========================================
        $(document).on('click', '.remove-from-cart', function () {
            var cartid = $(this).data('cartid');
            $(this).closest('tr').remove();
            $('.delivery_option').prop('checked', false);
            $('#deliveryOptionLabel, #collectionOptionLabel').removeClass('selected');

            $.ajax({
                url: cartStoreUrl,
                method: "POST",
                data: { _token: "{{ csrf_token() }}", cartid: cartid },
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Removed!', showConfirmButton: false, timer: 1000 });
                }
            });

            if ($('#basketBody tr').length === 0) $('#basketEmpty').show();
            recalcTotal();
        });

        // ==========================================
        // QTY CHANGE
        // ==========================================
        $(document).on('keyup', '.basket-qty', function () {
            var amount = parseFloat($(this).attr('v_amount')) || 0;
            var qty = parseInt($(this).val()) || 0;
            var vid = $(this).attr('vid');
            $('#sub' + vid).val(amount * qty);
            $('.delivery_option').prop('checked', false);
            $('#deliveryOptionLabel, #collectionOptionLabel').removeClass('selected');
            recalcTotal();
        });

        // ==========================================
        // RECALC TOTAL
        // ==========================================
        function recalcTotal() {
            var total = 0;
            $('.row-total').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            if ($('#delivery').is(':checked') && total > 0 && total < 200) {
                total += 3.50;
                $('#deliveryWarning').addClass('show');
            } else {
                $('#deliveryWarning').removeClass('show');
            }
            $('#net_total').val(total > 0 ? '£' + total.toFixed(2) : '');
        }

        // ==========================================
        // HELPER: Validate donor fields
        // ==========================================
        function validateDonorFields() {
            var valid = true;
            var firstInvalid = null;
            $('.voucher-card input[required]').each(function () {
                if (!$(this).val() || $(this).val().trim() === '') {
                    valid = false;
                    $(this).css('border-color', '#d45273');
                    if (!firstInvalid) firstInvalid = this;
                } else {
                    $(this).css('border-color', '');
                }
            });

            if (!valid && firstInvalid) {
                Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please fill in all required fields.' });
                $('html, body').animate({ scrollTop: $(firstInvalid).offset().top - 100 }, 500);
            }

            return valid;
        }

        // Clear validation style on input
        $('.voucher-card input[required]').on('input', function () {
            if ($(this).val().trim() !== '') $(this).css('border-color', '');
        });

        // ==========================================
        // HELPER: Get order data
        // ==========================================
        function getOrderData() {
            var voucherIds = $("input[name='v_ids[]']").map(function () { return $(this).val(); }).get();
            var qtys = $("input[name='qty[]']").map(function () { return $(this).val(); }).get();
            var did = $("#donner_id").val() || null;
            var delivery = $('#delivery').is(':checked');
            var collection = $('#collection').is(':checked');

            var delivery_charge = 0;
            if (delivery) {
                var prepaidTotal = 0;
                $('.row-total').each(function () { prepaidTotal += parseFloat($(this).val()) || 0; });
                if (prepaidTotal < 200) delivery_charge = 3.50;
            }

            var donorInfo = {
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                address_line_1: $('#address_line_1').val(),
                address_line_2: $('#address_line_2').val(),
                town: $('#town').val(),
                postcode: $('#postcode').val()
            };

            return {
                voucherIds: voucherIds,
                qtys: qtys,
                did: did,
                delivery: delivery,
                collection: collection,
                delivery_charge: delivery_charge,
                donor_info: donorInfo
            };
        }

        // ==========================================
        // HELPER: Get numeric total from display
        // ==========================================
        function getNumericTotal() {
            var totalText = $('#net_total').val();
            return parseFloat(totalText.replace('£', '')) || 0;
        }

        // ==========================================
        // HELPER: Set button state
        // ==========================================
        function setButtonLoading($btn, loading) {
            if (loading) {
                $btn.data('originalText', $btn.html());
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            } else {
                $btn.prop('disabled', false).html($btn.data('originalText') || 'Place order');
            }
        }

        // ==========================================
        // PATH 1: Balance payment (auth user, enough balance)
        // ==========================================
        function payWithBalance(orderData) {
            return new Promise(function (resolve, reject) {

                if (!confirm('Your balance of £' + userBalance.toFixed(2) + ' will be used for this order. Continue?')) {
                    reject('cancelled');
                    return;
                }

                $.ajax({
                    url: balanceOrderUrl,
                    method: "POST",
                    data: orderData,
                    success: function (d) {
                        if (d.status == 303) {
                            $('.ermsg').html(d.message);
                            $('html, body').animate({ scrollTop: 0 }, 500);
                            reject('validation_error');
                        } else if (d.status == 200 || d.status == 300) {
                            resolve(d);
                        }
                    },
                    error: function (d) {
                        console.log(d);
                        reject('server_error');
                    }
                });
            });
        }

        // ==========================================
        // PATH 2: Stripe payment (guest or insufficient balance)
        // ==========================================
        function payWithStripe(stripeAmount) {
            return new Promise(function (resolve, reject) {

                // Show Stripe modal
                $('#stripeAmountDisplay').text('£' + stripeAmount.toFixed(2));
                $('#card-errors').text('');
                $('#stripeModal').addClass('show');

                // Set up the pay button handler (remove old one first)
                $('#payWithStripeBtn').off('click').on('click', async function () {

                    var $payBtn = $(this);
                    $payBtn.prop('disabled', true).text('Processing payment...');

                    try {
                        // Step 1: Create PaymentIntent from server
                        var intentResp = await $.ajax({
                            url: paymentIntentUrl,
                            method: "POST",
                            data: {
                                amount: stripeAmount.toFixed(2),
                                _token: "{{ csrf_token() }}"
                            }
                        });

                        // Step 2: Confirm card payment with Stripe
                        var result = await stripe.confirmCardPayment(intentResp.client_secret, {
                            payment_method: { card: cardElement }
                        }, {
                            return_url: window.location.href
                        });

                        if (result.error) {
                            // Show error in card element
                            $('#card-errors').text(result.error.message);
                            $payBtn.prop('disabled', false).text('Pay Now');
                            reject('card_error');
                            return;
                        }

                        // Step 3: Payment successful
                        $('#stripeModal').removeClass('show');
                        resolve(result);

                    } catch (err) {
                        console.log(err);

                        if (err.message && err.message.includes('Redirecting')) {
                            // 3DS authentication in progress — don't show error
                            return;
                        }

                        var errorMsg = 'Payment failed. Please try again.';
                        if (err.responseJSON && err.responseJSON.message) {
                            errorMsg = err.responseJSON.message;
                        }
                        $('#card-errors').text(errorMsg);
                        $payBtn.prop('disabled', false).text('Pay Now');
                        reject('payment_failed');
                    }
                });
            });
        }

        // ==========================================
        // MAIN: Place Order Button Click
        // ==========================================
        $('#placeOrderBtn').click(async function () {

            // --- Validate basket ---
            if ($('#basketBody tr').length === 0) {
                Swal.fire({ icon: 'warning', title: 'Basket is empty!', text: 'Please add voucher books first.' });
                return;
            }

            // --- Validate donor fields ---
            if (!validateDonorFields()) return;

            // --- Get totals ---
            var totalAmount = getNumericTotal();
            if (totalAmount <= 0) {
                Swal.fire({ icon: 'warning', title: 'Invalid amount', text: 'Order total is £0.' });
                return;
            }

            // --- Get order data ---
            var orderData = getOrderData();

            // --- Determine payment path ---
            // Path A: Auth user with enough balance → pay from balance
            // Path B: Auth user without enough balance → Stripe for full amount
            // Path C: Guest user → Stripe for full amount
            var useBalance = false;
            var stripeAmount = 0;

            if (isLoggedIn && userBalance >= totalAmount) {
                // PATH A: Full balance payment
                useBalance = true;
            } else if (isLoggedIn && userBalance > 0 && userBalance < totalAmount) {
                // PATH B: Has some balance but not enough — Stripe for full amount
                // (You could do partial: stripeAmount = totalAmount - userBalance)
                // For now, Stripe for full amount
                stripeAmount = totalAmount;
            } else {
                // PATH C: Guest or no balance — Stripe for full amount
                stripeAmount = totalAmount;
            }

            // --- Execute payment ---
            var $btn = $(this);
            setButtonLoading($btn, true);

            try {
                if (useBalance) {
                    // ==========================================
                    // PATH A: Pay from balance
                    // ==========================================
                    var result = await payWithBalance(orderData);
                    $('.ermsg').html('<div class="alert alert-success"><b>Order placed successfully! Voucher books will be dispatched soon.</b></div>');
                    $('html, body').animate({ scrollTop: 0 }, 500);
                    window.setTimeout(function () { location.reload(); }, 2000);

                } else {
                    // ==========================================
                    // PATH B / C: Pay with Stripe
                    // ==========================================
                    var result = await payWithStripe(stripeAmount);

                    // After successful Stripe payment, send order to server
                    // (Your webhook should handle order creation, but you can also do it here)
                    $('.ermsg').html('<div class="alert alert-success"><b>Payment successful! Your order is being processed.</b></div>');
                    $('html, body').animate({ scrollTop: 0 }, 500);
                    window.setTimeout(function () { location.reload(); }, 2000);
                }

            } catch (err) {
                // Handle specific errors
                if (err === 'cancelled') {
                    // User cancelled — do nothing
                } else if (err === 'validation_error' || err === 'server_error') {
                    // Already handled in the sub-functions
                } else if (err === 'card_error' || err === 'payment_failed') {
                    // Already handled in Stripe modal
                } else {
                    // Unexpected error
                    console.error('Order error:', err);
                    Swal.fire({ icon: 'error', title: 'Something went wrong', text: 'Please try again or contact support.' });
                }
            } finally {
                setButtonLoading($btn, false);
            }
        });

        // Initial calc
        recalcTotal();
    });
</script>
@endsection