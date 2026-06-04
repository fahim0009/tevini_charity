@extends('frontend.layouts.home')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.min.css">

<style>
    /* ============================================
       Voucher Book Order Page
       ============================================ */

    .voucher-page {
        background-color: #E8E1D9;
        padding: 60px 0;
        min-height: 70vh;
    }

    .voucher-page-title {
        font-size: 48px;
        line-height: 1;
        color: #18988B;
        font-family: "DarkerGrotesque-semibold";
        margin-bottom: 10px;
    }

    .voucher-page-subtitle {
        font-family: "Roboto-Regular";
        font-size: 16px;
        color: #4E4B44;
        margin-bottom: 40px;
    }

    .voucher-card {
        background: #E1D8CE;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    /* --- Auth Prompt --- */
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

    /* --- Balance Box --- */
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

    /* --- Full-width section spacing --- */
    .full-section {
        margin-bottom: 40px;
    }

    .full-section:last-child {
        margin-bottom: 0;
    }

    /* --- Form Labels --- */
    .voucher-card label.form-label-custom {
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
    .voucher-card .form-control,
    .voucher-card .form-select {
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

    .voucher-card .form-control:focus,
    .voucher-card .form-select:focus {
        border-color: #18988B;
        box-shadow: 0 0 0 3px rgba(24, 152, 139, 0.15);
        background-color: #ffffff;
        outline: none;
    }

    .voucher-card .form-control::placeholder {
        color: #9e978d;
    }

    .voucher-card .form-control[readonly] {
        background-color: rgba(0, 48, 87, 0.04);
        cursor: default;
    }

    /* --- Donor Info Grid --- */
    .donor-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0 20px;
    }

    .donor-grid .span-3 {
        grid-column: span 3;
    }

    /* ============================================
       VOUCHER BOOK CARD DESIGN
       ============================================ */

    .voucher-book-col {
        margin-bottom: 36px;
    }

    .voucher-book-wrapper {
        position: relative;
        cursor: pointer;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .voucher-book-wrapper:hover {
        transform: translateY(-8px);
    }

    /* --- Stack effect (2 vouchers behind) --- */
    .voucher-book-wrapper::before,
    .voucher-book-wrapper::after {
        content: '';
        position: absolute;
        left: 4px;
        right: 4px;
        top: 0;
        height: 100%;
        border-radius: 6px;
        background: #f5f0e8;
        z-index: 0;
        pointer-events: none;
    }

    .voucher-book-wrapper::before {
        bottom: -8px;
        top: auto;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .voucher-book-wrapper::after {
        bottom: -16px;
        top: auto;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* --- The actual voucher face --- */
    .voucher-face {
        position: relative;
        z-index: 1;
        background: #faf7f1;
        border-radius: 6px;
        padding: 20px 22px 16px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 48, 87, 0.06);
        overflow: hidden;
        transition: box-shadow 0.3s ease-in-out;
    }

    .voucher-book-wrapper:hover .voucher-face {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    /* --- Voucher top dashed line --- */
    .voucher-face::before {
        content: '';
        position: absolute;
        top: 42px;
        left: 0;
        right: 0;
        border-top: 1.5px dashed rgba(0, 48, 87, 0.15);
    }

    /* --- Voucher bottom dashed line --- */
    .voucher-face::after {
        content: '';
        position: absolute;
        bottom: 52px;
        left: 0;
        right: 0;
        border-top: 1.5px dashed rgba(0, 48, 87, 0.15);
    }

    /* --- Voucher header row --- */
    .voucher-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .voucher-number {
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        color: #6A757C;
        letter-spacing: 0.5px;
    }

    .voucher-charity-label {
        text-align: right;
    }

    .voucher-charity-label .reg-text {
        font-family: "Roboto-Regular";
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9e978d;
        display: block;
        line-height: 1.2;
    }

    .voucher-charity-label .reg-number {
        font-family: 'Courier New', Courier, monospace;
        font-size: 10px;
        color: #6A757C;
    }

    /* --- Voucher address --- */
    .voucher-address {
        font-family: "Roboto-Regular";
        font-size: 9px;
        color: #9e978d;
        line-height: 1.4;
        margin-bottom: 8px;
    }

    /* --- Voucher body --- */
    .voucher-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 80px;
        padding: 8px 0;
    }

    .voucher-pay-section {
        flex: 1;
        padding-right: 12px;
    }

    .voucher-pay-label {
        font-family: "Roboto-Regular";
        font-size: 12px;
        color: #003057;
        margin-bottom: 6px;
    }

    .voucher-amount-words {
        font-family: "Roboto-Regular";
        font-size: 13px;
        color: #4E4B44;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.3;
    }

    /* --- Green amount box --- */
    .voucher-amount-box {
        background: #18988B;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 4px;
        text-align: center;
        min-width: 80px;
        box-shadow: 0 2px 8px rgba(24, 152, 139, 0.3);
        flex-shrink: 0;
    }

    .voucher-amount-box .amount-value {
        font-family: "DarkerGrotesque-bold";
        font-size: 26px;
        line-height: 1;
    }

    .voucher-amount-box .amount-currency {
        font-family: "Roboto-Regular";
        font-size: 10px;
        opacity: 0.8;
        margin-top: 2px;
    }

    /* --- Voucher footer --- */
    .voucher-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding-top: 8px;
    }

    /* --- Barcode --- */
    .voucher-barcode {
        display: flex;
        align-items: flex-end;
        gap: 1px;
        height: 28px;
    }

    .voucher-barcode .bar {
        background: #003057;
        border-radius: 0.5px;
    }

    /* --- Signature line --- */
    .voucher-signature {
        text-align: right;
    }

    .voucher-signature .sig-line {
        width: 80px;
        height: 1px;
        background: rgba(0, 48, 87, 0.3);
        margin-bottom: 3px;
    }

    .voucher-signature .sig-label {
        font-family: "Roboto-Regular";
        font-size: 8px;
        color: #9e978d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- Tevini brand mark --- */
    .voucher-brand {
        position: absolute;
        bottom: 8px;
        left: 22px;
        font-family: "DarkerGrotesque-bold";
        font-size: 10px;
        color: #18988B;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.5;
    }

    /* --- Add button overlay --- */
    .voucher-add-btn {
        position: absolute;
        bottom: -14px;
        right: 18px;
        z-index: 5;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 3px solid #E1D8CE;
        background: #18988B;
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        font-family: "DarkerGrotesque-bold";
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 3px 10px rgba(24, 152, 139, 0.3);
        line-height: 1;
    }

    .voucher-add-btn:hover {
        background: #147a70;
        transform: scale(1.15);
        box-shadow: 0 5px 18px rgba(24, 152, 139, 0.4);
    }

    .voucher-add-btn:active {
        transform: scale(1.05);
    }

    /* --- Voucher note tag below card --- */
    .voucher-note-tag {
        text-align: center;
        margin-top: 28px;
        font-family: "Roboto-Regular";
        font-size: 12px;
        color: #6A757C;
        line-height: 1.4;
    }

    .voucher-note-tag .note-count {
        font-weight: 600;
        color: #003057;
    }

    /* --- Blank cheque variant --- */
    .voucher-face.blank-cheque .voucher-amount-box {
        background: #003057;
        box-shadow: 0 2px 8px rgba(0, 48, 87, 0.3);
    }

    .voucher-face.blank-cheque .voucher-amount-box .amount-value {
        font-size: 11px;
        font-family: "Roboto-Regular";
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* ============================================
       BASKET TABLE
       ============================================ */

    .basket-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
    }

    .basket-table thead th {
        font-family: "Roboto-Regular";
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6A757C;
        padding: 8px 12px;
        border: none;
    }

    .basket-table tbody td {
        background: #E8E1D9;
        padding: 12px;
        vertical-align: middle;
        border: none;
    }

    .basket-table tbody tr td:first-child {
        border-radius: 8px 0 0 8px;
    }

    .basket-table tbody tr td:last-child {
        border-radius: 0 8px 8px 0;
    }

    .basket-table .remove-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 6px;
        border: none;
        background: #d45273;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .basket-table .remove-btn:hover {
        background: #b33d57;
        transform: scale(1.1);
    }

    .basket-table .qty-input {
        width: 60px;
        text-align: center;
        padding: 8px;
        border: 1px solid rgba(0, 48, 87, 0.15);
        border-radius: 6px;
        background: #E1D8CE;
        font-family: "Roboto-Regular";
        font-size: 14px;
        color: #003057;
        height: 38px;
    }

    .basket-table .qty-input:focus {
        border-color: #18988B;
        outline: none;
        background: #ffffff;
    }

    .basket-table .basket-voucher-name {
        font-family: "DarkerGrotesque-bold";
        font-size: 14px;
        color: #003057;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .basket-table .basket-voucher-note {
        font-family: "Roboto-Regular";
        font-size: 12px;
        color: #6A757C;
        margin-top: 2px;
    }

    .voucher-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-family: "Roboto-Regular";
    }

    .voucher-badge.prepaid {
        background-color: rgba(24, 152, 139, 0.12);
        color: #18988B;
    }

    .basket-empty {
        text-align: center;
        padding: 30px 20px;
        font-family: "Roboto-Regular";
        font-size: 14px;
        color: #9e978d;
    }

    /* ============================================
       DELIVERY
       ============================================ */

    .delivery-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .delivery-option {
        background: #E8E1D9;
        border-radius: 10px;
        padding: 18px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        border: 2px solid transparent;
    }

    .delivery-option:hover {
        border-color: rgba(24, 152, 139, 0.2);
    }

    .delivery-option.selected {
        border-color: #18988B;
        background: rgba(24, 152, 139, 0.06);
    }

    .delivery-option input[type="checkbox"] {
        width: 20px;
        height: 20px;
        min-width: 20px;
        margin-top: 2px;
        accent-color: #18988B;
        cursor: pointer;
    }

    .delivery-option .delivery-title {
        font-family: "DarkerGrotesque-bold";
        font-size: 15px;
        color: #003057;
        margin-bottom: 2px;
    }

    .delivery-option .delivery-desc {
        font-family: "Roboto-Regular";
        font-size: 12px;
        color: #6A757C;
        line-height: 1.5;
    }

    .delivery-warning {
        display: none;
        background: rgba(212, 82, 115, 0.08);
        border: 1px solid rgba(212, 82, 115, 0.25);
        border-radius: 8px;
        padding: 10px 16px;
        margin-bottom: 14px;
        font-family: "Roboto-Regular";
        font-size: 13px;
        color: #b33d57;
    }

    .delivery-warning.show {
        display: block;
    }

    /* ============================================
       ORDER TOTAL BAR
       ============================================ */

    .order-total-bar {
        background: #003057;
        border-radius: 10px;
        padding: 20px 24px;
        margin-top: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .order-total-bar .address-info {
        font-family: "Roboto-Regular";
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.6;
        flex: 1;
        min-width: 200px;
    }

    .order-total-bar .total-area {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .order-total-bar .total-label {
        font-family: "Roboto-Regular";
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
    }

    .order-total-bar .total-input {
        width: 120px;
        text-align: center;
        padding: 10px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-family: "DarkerGrotesque-bold";
        font-size: 18px;
        height: 44px;
    }

    .order-total-bar .total-input:focus {
        outline: none;
    }

    .order-total-bar .total-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .btn-place-order {
        display: inline-block;
        padding: 12px 36px;
        background-color: #18988B;
        color: #ffffff;
        font-family: "DarkerGrotesque-bold";
        font-size: 15px;
        border-radius: 25px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        text-decoration: none;
    }

    .btn-place-order:hover {
        background-color: #147a70;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(24, 152, 139, 0.3);
        color: #ffffff;
    }

    .btn-place-order:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .required-star {
        color: #d45273;
        margin-left: 2px;
    }

    .voucher-card .form-group {
        margin-bottom: 16px;
    }

    .voucher-page .alert {
        border-radius: 8px;
        font-family: "Roboto-Regular";
        font-size: 14px;
        padding: 12px 18px;
        margin-bottom: 20px;
    }

    .voucher-page .alert-success {
        background: rgba(24, 152, 139, 0.1);
        border: 1px solid rgba(24, 152, 139, 0.3);
        color: #147a70;
    }

    .voucher-page .alert-danger {
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

    .page-loader {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 9999;
    }

    .page-loader .spinner-box {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* ============================================
       Responsive
       ============================================ */

    @media (max-width: 991px) {
        .voucher-page-title {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .voucher-page-subtitle {
            margin-bottom: 30px;
        }

        .voucher-card {
            padding: 30px;
        }

        .balance-box .balance-amount {
            font-size: 26px;
        }

        .donor-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .donor-grid .span-3 {
            grid-column: span 2;
        }
    }

    @media (max-width: 767px) {
        .voucher-page {
            padding: 40px 0;
        }

        .voucher-page-title {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .voucher-page-subtitle {
            font-size: 14px;
            margin-bottom: 24px;
        }

        .voucher-card {
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

        .donor-grid {
            grid-template-columns: 1fr;
        }

        .donor-grid .span-3 {
            grid-column: span 1;
        }

        .voucher-face {
            padding: 14px 16px 12px;
        }

        .voucher-amount-box {
            padding: 8px 14px;
            min-width: 65px;
        }

        .voucher-amount-box .amount-value {
            font-size: 22px;
        }

        .delivery-grid {
            grid-template-columns: 1fr;
        }

        .order-total-bar {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .order-total-bar .total-area {
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-place-order {
            width: 100%;
            text-align: center;
        }

        .section-divider {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .voucher-page-title {
            font-size: 24px;
        }

        .voucher-face {
            padding: 16px 14px 14px;
        }

        .voucher-amount-box .amount-value {
            font-size: 20px;
        }

        .voucher-amount-box {
            padding: 8px 12px;
            min-width: 58px;
        }

        .voucher-barcode {
            height: 22px;
        }

        .basket-table .qty-input {
            width: 50px;
        }
    }
</style>

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
                                @foreach ($cart ?? collect() as $item)
                                    @php
                                        $cartVoucher = \App\Models\Voucher::where('id', $item->voucher_id)->first();
                                    @endphp
                                    <tr class="basket-row" data-cart-id="{{ $item->id }}">
                                        <td>
                                            <button type="button" class="remove-btn remove-from-cart" data-cartid="{{ $item->id }}">×</button>
                                        </td>
                                        <td>
                                            <input type="hidden" value="{{ $item->voucher_id }}" name="v_ids[]">
                                            <input type="hidden" class="row-total" id="sub{{ $item->voucher_id }}" value="{{ $item->tamount }}">
                                            <div class="basket-voucher-name">
                                                @if ($cartVoucher && $cartVoucher->single_amount == "0")
                                                    Blank Cheque
                                                @else
                                                    £{{ $cartVoucher->single_amount ?? '' }}
                                                @endif
                                                <span class="voucher-badge prepaid" style="font-size:9px;">Prepaid</span>
                                            </div>
                                            <div class="basket-voucher-note">
                                                {{ $cartVoucher->note ?? '' }} @if ($cartVoucher && $cartVoucher->type == 'Prepaid') = £{{ $cartVoucher->amount ?? '' }} @endif
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="qty-input basket-qty" name="qty[]"
                                                value="{{ $item->qty }}"
                                                v_amount="{{ $item->tamount }}"
                                                v_type="Prepaid"
                                                data-type="Prepaid"
                                                vid="{{ $item->voucher_id }}"
                                                id="cartValue{{ $item->voucher_id }}"
                                                onkeypress="return /[0-9]/i.test(event.key)">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="basket-empty" id="basketEmpty"
                            @if (isset($cart) && $cart->count() > 0) style="display:none;" @endif>
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

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function () {

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Toggle delivery options
        $('.delivery_option').on('change', function () {
            if (this.checked) {
                $('.delivery_option').not(this).prop('checked', false);
            }
            $('#deliveryOptionLabel, #collectionOptionLabel').removeClass('selected');
            if ($('#delivery').is(':checked')) $('#deliveryOptionLabel').addClass('selected');
            if ($('#collection').is(':checked')) $('#collectionOptionLabel').addClass('selected');
            recalcTotal();
        });

        // ADD TO CART
        $(document).on('click', '.add-to-cart', function (e) {
            e.preventDefault();

            alert('This page is currently in development. Please contact us to place your order or for more information.');
            return;

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
                url: "{{ route('orderbook.cart.store') }}",
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
                }
            });
        });

        // REMOVE FROM CART
        $(document).on('click', '.remove-from-cart', function () {
            var cartid = $(this).data('cartid');
            $(this).closest('tr').remove();
            $('.delivery_option').prop('checked', false);
            $('#deliveryOptionLabel, #collectionOptionLabel').removeClass('selected');

            if (cartid) {
                $.ajax({
                    url: "{{ route('orderbook.cart.store') }}",
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}", cartid: cartid },
                    success: function () {
                        Swal.fire({ icon: 'success', title: 'Removed!', showConfirmButton: false, timer: 1000 });
                    }
                });
            }

            if ($('#basketBody tr').length === 0) $('#basketEmpty').show();
            recalcTotal();
        });

        // QTY CHANGE
        $(document).on('keyup', '.basket-qty', function () {
            var amount = parseFloat($(this).attr('v_amount')) || 0;
            var qty = parseInt($(this).val()) || 0;
            var vid = $(this).attr('vid');
            $('#sub' + vid).val(amount * qty);
            $('.delivery_option').prop('checked', false);
            $('#deliveryOptionLabel, #collectionOptionLabel').removeClass('selected');
            recalcTotal();
        });

        // RECALC TOTAL
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

        // PLACE ORDER
        var orderUrl = "{{ URL::to('/user/addvoucher') }}";

        $('#placeOrderBtn').click(function () {
            if ($('#basketBody tr').length === 0) {
                Swal.fire({ icon: 'warning', title: 'Basket is empty!', text: 'Please add voucher books first.' });
                return;
            }

            var valid = true;
            $('.voucher-card input[required]').each(function () {
                if (!$(this).val() || $(this).val().trim() === '') {
                    valid = false;
                    $(this).css('border-color', '#d45273');
                } else {
                    $(this).css('border-color', '');
                }
            });

            if (!valid) {
                Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please fill in all required fields.' });
                $('html, body').animate({ scrollTop: 0 }, 500);
                return;
            }

            if (!confirm('Are you sure you want to place this order?')) return;

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            $('#pageLoader').show();

            var voucherIds = $("input[name='v_ids[]']").map(function () { return $(this).val(); }).get();
            var qtys = $("input[name='qty[]']").map(function () { return $(this).val(); }).get();
            var did = $("#donner_id").val();
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

            $.ajax({
                url: orderUrl,
                method: "POST",
                data: { voucherIds, qtys, did, delivery, collection, delivery_charge, donor_info: donorInfo },
                success: function (d) {
                    if (d.status == 303) {
                        $('.ermsg').html(d.message);
                        $('html, body').animate({ scrollTop: 0 }, 500);
                    } else if (d.status == 300) {
                        $('.ermsg').html(d.message);
                        $('html, body').animate({ scrollTop: 0 }, 500);
                        window.setTimeout(function () { location.reload(); }, 2000);
                    }
                },
                error: function (d) { console.log(d); },
                complete: function () {
                    $btn.prop('disabled', false).html('Place order');
                    $('#pageLoader').hide();
                }
            });
        });

        // Clear validation style on input
        $('.voucher-card input[required]').on('input', function () {
            if ($(this).val().trim() !== '') $(this).css('border-color', '');
        });

        recalcTotal();
    });
</script>
@endsection