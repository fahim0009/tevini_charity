@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status no-print">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            <div class="mx-2">Donor Topup</div>
            <a href="{{ route('donationlist') }}" class="btn btn-success btn-sm">Back</a>
        </div>
    </section>

    <section class="px-4 no-print">
        <div class="row my-3">
            <div class="ermsg col-12"></div>
        </div>
    </section>

    <section class="px-4 no-print">
        <div class="row my-3">
            <div class="container">
                <div class="col-md-12 my-3 alert alert-warning">
                    <p class="mb-0">**Please note that if you are topping up your account using a credit/debit card there will be an additional fee of 2% on top of the standard 5% commission fee alternatively you can top up by transfer to the following: Tevini Ltd S/C 40-52-40 A/C 00024463.</p>
                </div>
            </div>
        </div>
    </section>

    @php
        // Cleaner Eloquent query using closure grouping
        $gettrans = \App\Models\Usertransaction::where('user_id', $topup->id)
            ->where(function ($query) {
                $query->where('status', '1')
                      ->orWhere('pending', '1');
            })
            ->orderBy('id', 'DESC')
            ->get();

        $donorUpBalance = 0;
        foreach ($gettrans as $tran) {
            if ($tran->t_type == "In") {
                $donorUpBalance += $tran->amount;
            } elseif ($tran->t_type == "Out") {
                $donorUpBalance -= $tran->amount;
            }
        }
    @endphp

    <!-- Image loader -->
    <div id="loading" style="display:none;">
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
    </div>
    <!-- Image loader -->

    <section class="no-print">
        <div class="row my-3 mx-0">
            <div class="col-md-12 my-3">
                <div class="row">
                    <!-- Donor Details -->
                    <div class="col-md-6 mt-2 text-center">
                        <div class="overflow table-responsive">
                            <table class="table table-custom shadow-sm bg-white">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Name</td><td>{{ $topup->name }}</td></tr>
                                    <tr><td>Email</td><td>{{ $topup->email }}</td></tr>
                                    <tr><td>Phone</td><td>{{ $topup->phone }}</td></tr>
                                    <tr><td>Address</td><td>{{ $topup->address }}</td></tr>
                                    <tr><td>Account</td><td>{{ $topup->accountno }}</td></tr>
                                    <tr><td>Balance</td><td>{{ number_format($donorUpBalance, 2) }}</td></tr>
                                    <tr><td>Expected gift aid</td><td>{{ $topup->expected_gift_aid }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Topup Form -->
                    <div class="col-md-6">
                        <div class="form-inline">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group my-2">
                                        <label for="date"><small>Date</small></label>
                                        <input class="form-control" type="date" value="{{ date('Y-m-d') }}" name="date" id="date" required>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="gbalance"><small>Amount</small></label>
                                        <input class="form-control" type="text" value="{{ $amount != 0 ? $amount : '' }}" name="gbalance" id="gbalance" required placeholder="Amount">
                                    </div>
                                    <input type="hidden" name="topupid" id="topupid" value="{{ $topup->id }}">
                                    <div class="form-group my-2">
                                        <label for="cc"><small>Commission Percentage</small></label>
                                        <input class="form-control" type="text" name="cc" id="cc" placeholder="e.g. 5">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="commission"><small>Commission</small></label>
                                        <input class="form-control" type="text" name="commission" id="commission" placeholder="0.00" readonly>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="balance"><small>Total Amount</small></label>
                                        <input class="form-control" type="text" readonly name="balance" id="balance" required placeholder="0.00">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="source"><small>Source</small></label>
                                        <select name="source" id="source" class="form-control">
                                            <option value="Bank">Bank</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Card">Card</option>
                                        </select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="donationBy"><small>Donation By</small></label>
                                        <input class="form-control" type="text" name="donationBy" id="donationBy" list="donors" placeholder="Start typing or enter new name...">
                                        <datalist id="donors">
                                            @foreach($donateBy as $donor)
                                                <option value="{{ $donor->donation_by }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="note"><small>Note</small></label>
                                        <input class="form-control" type="text" name="note" id="note" placeholder="Optional note">
                                    </div>
                                    <div class="form-group my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="receipt">
                                            <label class="form-check-label" for="receipt">I want receipt.</label>
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="gift">
                                            <label class="form-check-label" for="gift">GIFT</label>
                                        </div>
                                    </div>  
                                    <div class="form-group my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="cleargift">
                                            <label class="form-check-label" for="cleargift">CLEAR EXPECTED GIFT AID</label>
                                        </div>
                                    </div>    
                                    <div class="form-group my-2 mt-3">
                                        <button type="button" id="topBal" class="btn btn-info text-white">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Receipt Print Section -->
    <div class="row" id="receiptp" style="display:none;">
        <div class="col-12 text-right no-print mb-3">
            <button onclick="window.print()" class="fa fa-print btn btn-default">Print</button>
        </div>
        <div class="col-md-3 no-print"></div>
        <div class="col-md-6 mt-2 text-center">
            <div class="overflow table-responsive">
                <table class="table table-custom shadow-sm bg-white">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Name</td><td>{{ $topup->name }}</td></tr>
                        <tr><td>Email</td><td>{{ $topup->email }}</td></tr>
                        <tr><td>Phone</td><td>{{ $topup->phone }}</td></tr>
                        <tr><td>Topup amount</td><td id="tamount"></td></tr>
                        <tr><td>Commission</td><td id="cmsn"></td></tr>
                        <tr><td>Total</td><td id="ttl"></td></tr>
                        <tr><td>Source</td><td id="src"></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
 $(document).ready(function () {
    'use strict';

    // Setup AJAX CSRF globally
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Calculation Logic
    function calculateTotals() {
        // parseFloat with fallback to 0 prevents NaN errors
        let amount = parseFloat($('#gbalance').val()) || 0;
        let comnRate = parseFloat($('#cc').val()) || 0;
        
        // Calculate Commission
        let commissionAmount = (amount * comnRate) / 100;
        
        // Calculate Final Amount
        let afterCmnAmount = amount - commissionAmount;
        
        // .toFixed(2) ensures 2 decimal places (e.g., 2.40 instead of 2.396...)
        $('#commission').val(commissionAmount.toFixed(2));
        $('#balance').val(afterCmnAmount.toFixed(2));
    }

    // Trigger calculation on input/keyup
    $("#gbalance, #cc").on('keyup input', calculateTotals);

    // Save Button Click Handler
    $("#topBal").click(function () {
        $("#loading").show();
        $(".ermsg").html(''); // Clear previous errors

        // Gather form data
        let requestData = {
            date: $("#date").val(),
            topupid: $("#topupid").val(),
            balance: $("#balance").val(),
            commission: $("#commission").val(),
            source: $("#source").val(),
            gbalance: $("#gbalance").val(),
            note: $("#note").val(),
            donationBy: $("#donationBy").val(),
            receipt: $("#receipt").is(':checked'),
            gift: $("#gift").is(':checked'),
            cleargift: $("#cleargift").is(':checked')
        };

        $.ajax({
            url: "{{ URL::to('/admin/topupstore') }}",
            method: "POST",
            data: requestData,
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html( d.message );
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                } else if (d.status == 300) {
                    $(".ermsg").html( d.message );
                } else {
                    // Assuming success is handled here if status is different
                    $(".ermsg").html('<div class="alert alert-success">' + (d.message || 'Saved successfully!') + '</div>');
                }
            },
            complete: function () {
                $("#loading").hide();
                
                // Show Receipt if checkbox is checked
                if (requestData.receipt === true) {
                    $("#receiptp").show();
                    // Map calculations properly into the receipt table
                    $("#tamount").html("<span>" + requestData.gbalance + "</span>"); // Gross Topup amount
                    $("#cmsn").html("<span>" + requestData.commission + "</span>"); // Deducted Commission
                    $("#ttl").html("<span>" + requestData.balance + "</span>");     // Final Total
                    $("#src").html("<span>" + requestData.source + "</span>");
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr);
                $(".ermsg").html('<div class="alert alert-danger">An unexpected error occurred. Please try again.</div>');
            }
        });
    });
});
</script>
@endsection