@extends('frontend.layouts.user')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
<style>
    .chkCircle{
    height: 25px;
    width: 25px;
    vertical-align: middle;
    }

    .modal-content{
        background-color: #FDF3EE;
    }

</style>

<style>
    /* Simple spinner */
    #loader {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
    }

    #loader::after {
        content: "";
        display: block;
        width: 50px;
        height: 50px;
        border: 5px solid #fff;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -25px 0 0 -25px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

@php
    if (isset($_GET["cid"])) {
        $cid = $_GET["cid"];
    } 
    if (isset($_GET["amount"])) {
        $amount = $_GET["amount"];
    } 


@endphp

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Make a donation/Standing order
            </div>
        </div>
        <section class="px-4">
            <div class="row my-3">
                <div class="ermsg"></div>

                @if(session()->has('success'))
                <section class="px-4">
                    <div class="row my-3">
                        <div class="alert alert-success" id="successMessage">{{ session()->get('success') }}</div>
                    </div>
                </section>
                @endif
                @if(session()->has('error'))
                <section class="px-4">
                    <div class="row my-3">
                        <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
                    </div>
                </section>
                @endif

                @if (isset($errors))
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    <section class="px-2">
                        <div class="row">
                            <div class="alert alert-danger">{{ $error }}</div>
                        </div>
                    </section>
                    @endforeach
                @endif
                @endif
                

            </div>
        </section>

        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
        </div>
        <!-- Image loader -->
    </div>
    <form action="{{ route('onlinedonation.store') }}" method="POST" enctype="multipart/form-data" id="DonationForm">
        {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
        <div class="row ">
            <div class="col-lg-6  px-3">
                <h4 class="txt-dash mt-5">Account Balance</h4>
                <h3 id="usertestID"></h3>
                <h2 class="amount">{{ Auth::user()->getLiveBalance() }}
                    GBP</h2>
                    
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Beneficiary</label>
                            <!-- <input type="text" class="form-control" placeholder="Select a charity"> -->
                            <select id="charity_id" name="charity_id" required class="form-control">
                                <option value="">Select a charity</option>
                                <option value="">Please Select</option>
                                @foreach (App\Models\Charity::all() as $charity)
                                <option value="{{ $charity->id }}|{{ $charity->name }}" {{ old('charity_id') == $charity->id ?  "selected": "" }} @if (isset($cid)) @if ($charity->id == $cid) selected @endif @endif>{{ $charity->name }} - ({{ $charity->acc_no }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control me-3" name="amount" id="amount" placeholder="0.00" value="@if(isset($amount)){{$amount}}@endif{{old('amount')}}"> <span
                                    class="txt-secondary fs-16">GBP</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group ">
                            <label for=""> &nbsp; </label>
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="ano_donation" id="ano_donation" class="form-check" {{ old('ano_donation') == "on" ?  "checked": "" }}> <span class="txt-secondary fs-16">Make this an anonymous donation</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group ">
                            <span class="txt-secondary fs-16">Please note that it is not possible to make a
                                anonymous standing order.
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-2">
                        <div class="form-group ">

                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="standard" id="standard" class="form-check"  {{ old('standard') == "on" ?  "checked": "" }}> <span
                                    class="txt-secondary fs-16">Set up a standing order</span>
                            </div>

                            <div class="{{ old('standard') == "on" ?  "selected": "standardOptions" }} my-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="">PAYMENTS</label>
                                            <select class="form-control" name="payments_type" id="payments_type">
                                                <option value="1">Fixed number of payments</option>
                                                <option value="2">Continuous payments</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="">NUMBER OF PAYMENTS</label>
                                            <input type="text" class="form-control" name="number_payments" id="number_payments">

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">STARTING</label>
                                            <input type="date" class="form-control" name="starting" id="starting">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">INTERVAL</label>
                                            <select class="form-control" id="interval" name="interval">
                                                <option value="1">Monthly</option>
                                                <option value="3">Every 3 month</option>
                                                <option value="6">Every 6 month</option>
                                                <option value="12">Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Total</label>
                                            <input type="text" class="form-control" id="totalamt" name="totalamt" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group ">
                            <div class="d-flex  ">
                                <input type="checkbox" name="confirm_donation" id="confirm_donation" required class="form-check" style="width: 56px;" {{ old('confirm_donation') == "on" ?  "checked": "" }}>
                                 <div
                                    class="txt-secondary fs-16">I confirm that this donation is for
                                    charitable purposes only, I will not benefit directly or indirectly by
                                    way of goods or services from the donation.</div>
                            </div>
                        </div>
                    </div>
                    
                </div>

            </div>
            <div class="col-lg-6 border-left-lg px-3">
                <div class="col-lg-12 mt-5">
                    <div class="form-group ">
                        <label for="">Notes to charity</label>
                        <textarea id="charitynote" name="charitynote" class="border-0 mt-2 w-100" rows="6">{{ old('charitynote') }}</textarea>
                    </div>
                </div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <label for="">My Notes</label>
                        <textarea name="mynote" id="mynote" class="border-0 mt-2 w-100" rows="6">{{ old('mynote') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mt-2">
                <div class="form-group ">
                    <input type="hidden" id="userid" name="userid" value="{{Auth::user()->id}}">
                    
                    <button type="button" id="donatemodal" class="btn-theme bg-secondary">
                        Make Donation
                    </button>
                    {{-- <input type="button" id="addBtn" value="Make Donation" class="btn-theme bg-primary"> --}}
                    {{-- <button class="btn-theme bg-primary" type="submit">Make a donation</button> --}}
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="fw-bold fs-23 txt-secondary" id="exampleModalLabel">Make Donation</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="txt-secondary border-bottom pb-2">Charity Name: <span id="charityname"></span> </div>
            <div class="txt-secondary border-bottom pb-2">Donation Amount: <span id="donationamnt"></span></div>
            <div class="txt-secondary border-bottom pb-2">Note: <span id="donationNote"></span></div>
            <div class="txt-secondary pb-2">My Note: <span id="dmynote"></span></div>

            <div id="standardDiv">
                <div class="border-bottom"></div>
                <div class="txt-secondary border-bottom pb-2">Set up a standing order: <span id="">Yes</span></div>
                <div class="txt-secondary border-bottom pb-2">PAYMENTS: <span id="d_payment"></span></div>
                <div class="txt-secondary border-bottom pb-2">NUMBER OF PAYMENTS: <span id="d_nymber"></span></div>
                <div class="txt-secondary border-bottom pb-2">STARTING: <span id="d_starting"></span></div>
                <div class="txt-secondary pb-2">INTERVAL: <span id="d_interval"></span></div>
            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addBtn" class="btn-theme bg-secondary">Make Donation</button>
        </div>
      </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
(function ($) {
    'use strict';

    /* ─── Constants ─────────────────────────────────────────── */
    var STANDING_URL   = "{{ URL::to('/user/standing-donation') }}";
    var ONETIME_URL    = "{{ URL::to('/user/make-donation') }}";

    var INTERVAL_LABELS = {
        '1':  'Monthly',
        '3':  'Every 3 months',
        '6':  'Every 6 months',
        '12': 'Yearly'
    };

    var PAYMENT_TYPE_LABELS = {
        '1': 'Fixed number of payments',
        '2': 'Continuous payments'
    };

    var STATUS = { ERROR_SHOW: 303, ERROR_RELOAD: 300 };


    /* ─── DOM Cache ─────────────────────────────────────────── */
    var dom = {
        form:            $('#DonationForm'),
        charitySelect:   $('#charity_id'),
        amount:          $('#amount'),
        anoDonation:     $('#ano_donation'),
        standard:        $('#standard'),
        paymentsType:    $('#payments_type'),
        numberPayments:  $('#number_payments'),
        starting:        $('#starting'),
        interval:        $('#interval'),
        totalAmt:        $('#totalamt'),
        confirmDonation: $('#confirm_donation'),
        charityNote:     $('#charitynote'),
        myNote:          $('#mynote'),
        userId:          $('#userid'),
        errorBox:        $('.ermsg'),
        loading:         $('#loading'),
        standardOpts:    $('.standardOptions'),

        // buttons
        btnOpenModal: $('#donatemodal'),
        btnConfirm:   $('#addBtn'),

        // modal
        modal:                $('#exampleModal'),
        modalStandardDiv:     $('#standardDiv'),
        modalCharityName:     $('#charityname'),
        modalAmount:          $('#donationamnt'),
        modalCharityNote:     $('#donationNote'),
        modalMyNote:          $('#dmynote'),
        modalPaymentType:     $('#d_payment'),
        modalNumberPayments:  $('#d_nymber'),
        modalStarting:        $('#d_starting'),
        modalInterval:        $('#d_interval')
    };


    /* ─── Helpers ───────────────────────────────────────────── */

    /**
     * Parse the combined charity value "id|name" into an object.
     */
    function parseCharity() {
        var raw = dom.charitySelect.val();
        if (!raw) return null;
        var parts = raw.split('|');
        return { id: parts[0], name: parts[1] || '' };
    }

    function toFixed2(n) {
        var num = parseFloat(n);
        return isNaN(num) ? '0.00' : num.toFixed(2);
    }

    /**
     * Gather every field the backend expects.
     */
    function collectFormData() {
        var charity = parseCharity();
        return {
            charity_id:       charity ? charity.id : '',
            amount:           dom.amount.val(),
            ano_donation:     dom.anoDonation.is(':checked'),
            standard:         dom.standard.is(':checked'),
            payments_type:    dom.paymentsType.val(),
            number_payments:  dom.numberPayments.val(),
            starting:         dom.starting.val(),
            interval:         dom.interval.val(),
            c_donation:       dom.confirmDonation.is(':checked'),
            charitynote:      dom.charityNote.val(),
            mynote:           dom.myNote.val(),
            userid:           dom.userId.val()
        };
    }


    /* ─── Inline Error Display ──────────────────────────────── */

    function showError(msg) {
        dom.errorBox.html(
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                msg +
                '<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
        scrollToTop();
    }

    // ✅ NEW: Dedicated function for success messages
    function showSuccess(msg) {
        dom.errorBox.html(msg);
        scrollToTop();
    }


    function scrollToTop() {
        var $target = $('.rightbar').length ? $('.rightbar') : $('html, body');
        $target.animate({ scrollTop: 0 }, 'fast');
    }


    function clearErrors() {
        dom.errorBox.html('');
    }


    /* ─── Loading State ─────────────────────────────────────── */

    function setLoading(on) {
        if (on) {
            dom.loading.show();
            dom.btnConfirm.prop('disabled', true).css('opacity', 0.65);
        } else {
            dom.loading.hide();
            dom.btnConfirm.prop('disabled', false).css('opacity', 1);
        }
    }


    /* ─── Validation ────────────────────────────────────────── */

    function validate() {
        var errors = [];

        if (!dom.charitySelect.val()) {
            errors.push('Please select a charity.');
        }

        var amt = parseFloat(dom.amount.val());
        if (!amt || amt <= 0) {
            errors.push('Please enter a valid donation amount.');
        }

        if (!dom.confirmDonation.is(':checked')) {
            errors.push('Please confirm that this donation is for charitable purposes only.');
        }

        // Standing-order-specific checks
        if (dom.standard.is(':checked')) {
            if (!dom.starting.val()) {
                errors.push('Please select a starting date for the standing order.');
            }
            if (dom.paymentsType.val() === '1') {
                var num = parseInt(dom.numberPayments.val(), 10);
                if (!num || num <= 0) {
                    errors.push('Please enter a valid number of payments.');
                }
            }
        }

        return errors;
    }


    /* ─── Modal ─────────────────────────────────────────────── */

    function fillModal(data) {
        var charity = parseCharity();

        dom.modalCharityName.text(charity ? charity.name : '');
        dom.modalAmount.text('\u00A3' + toFixed2(data.amount));
        dom.modalCharityNote.text(data.charitynote || '\u2014');
        dom.modalMyNote.text(data.mynote || '\u2014');

        if (data.standard) {
            dom.modalStandardDiv.show();
            dom.modalPaymentType.text(PAYMENT_TYPE_LABELS[data.payments_type] || '\u2014');
            dom.modalNumberPayments.text(
                data.payments_type === '2' ? 'Continuous' : (data.number_payments || '\u2014')
            );
            dom.modalStarting.text(data.starting || '\u2014');
            dom.modalInterval.text(INTERVAL_LABELS[data.interval] || '\u2014');
        } else {
            dom.modalStandardDiv.hide();
        }
    }

    function openModal() {
        clearErrors();

        var errors = validate();
        if (errors.length) {
            showError(errors.join('<br>'));
            return;
        }

        fillModal(collectFormData());
        dom.modal.modal('show');
    }


    /* ─── Standing-Order UI ─────────────────────────────────── */

    function toggleStandingOptions() {
        if (dom.standard.is(':checked')) {
            dom.standardOpts.slideDown(300);
        } else {
            dom.standardOpts.slideUp(200);
        }
    }

    function onPaymentTypeChange() {
        if (dom.paymentsType.val() === '2') {
            dom.numberPayments.val('').prop('disabled', true);
        } else {
            dom.numberPayments.prop('disabled', false);
        }
        recalcTotal();
    }

    function recalcTotal() {
        var amount   = parseFloat(dom.amount.val()) || 0;
        var payments = parseInt(dom.numberPayments.val(), 10) || 0;
        var total    = payments > 0 ? amount * payments : amount;
        dom.totalAmt.val(toFixed2(total));
    }


    /* ─── AJAX Submission ───────────────────────────────────── */

    function submitDonation() {
        dom.modal.modal('hide');
        setLoading(true);
        clearErrors();

        var data = collectFormData();
        var url  = data.standard ? STANDING_URL : ONETIME_URL;

        $.ajax({
            url:     url,
            method:  'POST',
            data:    data,
            success: handleResponse,
            error:   function (xhr) {
                var msg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showError(msg);
            },
            complete: function () {
                setLoading(false);
            }
        });
    }

    function handleResponse(res) {
        clearErrors();
        var msg = res.message || '';

        // Handle status 303 and 300
        if (res.status === 303 || res.status === 300) {
            
            // ✅ SMART FIX: Check if the message is actually a success message.
            // If the backend says "success" but uses an error status code, show green!
            if (msg.toLowerCase().indexOf('success') !== -1) {
                showSuccess(msg);
            } else {
                showError(msg);
            }

            // Trigger reload if requested by backend
            if (res.status === 300) {
                setTimeout(function () { window.location.reload(); }, 2000);
            }
            return;
        }

        // Handle standard success (with redirect)
        if (res.redirect) {
            window.location.href = res.redirect;
            return;
        }

        // Fallback success (no redirect provided)
        showSuccess(msg || 'Donation submitted successfully!');
        setTimeout(function () { window.location.reload(); }, 1500);
    }

    /* ─── Event Bindings ────────────────────────────────────── */

    function bindEvents() {
        dom.btnOpenModal.on('click', openModal);
        dom.btnConfirm.on('click', submitDonation);
        dom.standard.on('click', toggleStandingOptions);
        dom.paymentsType.on('change', onPaymentTypeChange);
        dom.amount.on('keyup', recalcTotal);
        dom.numberPayments.on('keyup', recalcTotal);
    }


    /* ─── Initialise ────────────────────────────────────────── */

    function init() {
        // CSRF token for every AJAX request
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Select2
        dom.charitySelect.select2({
            width: '100%',
            placeholder: 'Select an Option',
            allowClear: true
        });

        // Ensure standing-order options start hidden unless pre-checked via old()
        if (!dom.standard.is(':checked')) {
            dom.standardOpts.hide();
        }

        // Pre-fill total when amount is passed via GET (?amount=...)
        recalcTotal();

        bindEvents();
    }

    $(init);

})(jQuery);
</script>
@endsection
