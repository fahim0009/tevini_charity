@extends('layouts.admin')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet"/>
<style>
    table { overflow: visible; }
    select.form-control { position: static !important; }
</style>

<div class="dashboard-content" id="focusBcode">
    <section class="profile purchase-status">
        <div class="title-section d-flex justify-content-between align-items-center">
            <div>
                <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                Edit Selected Pending Vouchers
            </div>
            <div class="ermsg"></div>
        </div>
    </section>
    
    <!-- Image loader -->
    <div id='loading' style='display:none;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
    </div>

    <section class="">
        <div class="row my-3 mx-0">
            <div class="col-md-12 bg-white px-4">
                <div class="form-container">
                    <div class="overflow mx-auto">
                        <table class="table shadow-sm">
                            <thead>
                                <tr>
                                    <th>Charity</th>
                                    <th>Donor Acc No</th>
                                    <th>Donor Name</th>
                                    <th>Check No</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Waiting</th>
                                    <th>Expired</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="inner">
                                @foreach ($vouchers as $voucher)
                                <tr class="item-row">
                                    <td width="200px">
                                        <input type="hidden" name="voucher_id[]" class="voucher-id" value="{{ $voucher->id }}">
                                        
                                        <select name="charity[]" class="form-control charitylist">
                                            <option value>Select</option>
                                            @foreach ($charities as $charity)
                                                <option value="{{ $charity->id }}" @if($voucher->charity_id == $charity->id) selected @endif>
                                                    {{ $charity->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="150px">
                                        <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="{{ $voucher->account_no ?? '' }}" placeholder="Type Acc no...">
                                    </td>
                                    <td width="200px">
                                        <input style="min-width: 100px;" type="text" name="donor_name[]" readonly class="form-control donorAcc" value="{{ $voucher->user->name ?? '' }}">
                                        <input type="hidden" name="donor[]" class="donorid" value="{{ $voucher->user_id }}">
                                    </td>
                                    <td width="100px">
                                        <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value="{{ $voucher->cheque_no }}" readonly>
                                    </td>
                                    <td width="40px">
                                        <input style="min-width: 30px;" name="amount[]" type="text" class="amount form-control" value="{{ $voucher->amount }}">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value="{{ $voucher->note }}">
                                    </td>
                                    <td width="120px">
                                        <select name="waiting[]" class="form-control">
                                            <option value="No" @if($voucher->waiting == "No") selected @endif>No</option>
                                            <option value="Yes" @if($voucher->waiting == "Yes") selected @endif>Yes</option>
                                        </select>
                                    </td>
                                    <td width="120px">
                                        <select name="expired[]" class="form-control">
                                            <option value="No" @if($voucher->expired == "No") selected @endif>No</option>
                                            <option value="Yes" @if($voucher->expired == "Yes") selected @endif>Yes</option>
                                        </select>
                                    </td>
                                    <td width="80px">
                                        <button class="btn btn-danger btn-sm" type="button" onclick="removeRow(event)">X</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tr>
                                <td colspan="3"></td>
                                <td width="40px"><span>Total</span></td>
                                <td width="250px">
                                    <input style="min-width: 200px;" id="total" readonly type="text" class="form-control">
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="9" class="text-right">
                                    <button class="text-white btn-theme mt-2" id="updateVoucherBtn" type="button">Update Vouchers</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
    $('.charitylist').select2();

    function removeRow(event) {
        event.target.closest('.item-row').remove();
        net_total(); // রিমুভ করার পর টোটাল আবার হিসাব হবে
    }

    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Update Button Click
        $("#updateVoucherBtn").click(function() {
            $("#loading").show();
            
            var url = "{{ route('voucher.bulkupdateDetails') }}";
            
            var voucherIds = $("input[name='voucher_id[]']").map(function() { return $(this).val(); }).get();
            var charityIds = $("select[name='charity[]']").map(function() { return $(this).val(); }).get();
            var donorIds = $("input[name='donor[]']").map(function() { return $(this).val(); }).get();
            var donorAccs = $("input[name='donor_acc[]']").map(function() { return $(this).val(); }).get();
            var chqNos = $("input[name='check[]']").map(function() { return $(this).val(); }).get();
            var amts = $("input[name='amount[]']").map(function() { return $(this).val(); }).get();
            var notes = $("input[name='note[]']").map(function() { return $(this).val(); }).get();
            var waitings = $("select[name='waiting[]']").map(function() { return $(this).val(); }).get();
            var expireds = $("select[name='expired[]']").map(function() { return $(this).val(); }).get();

            $.ajax({
                url: url,
                method: "POST",
                data: { 
                    voucher_id: voucherIds, 
                    charity: charityIds, 
                    donor: donorIds, 
                    donor_acc: donorAccs, 
                    check: chqNos, 
                    amount: amts, 
                    note: notes, 
                    waiting: waitings, 
                    expired: expireds 
                },
                success: function(d) {
                    $("#loading").hide();
                    if (d.status == 300) {
                        $(".ermsg").html("<div class='alert alert-success'>"+ d.message +"</div>");
                        pagetop();
                    }
                },
                error: function(d) {
                    $("#loading").hide();
                    console.log(d);
                }
            });
        });

        // Donor Account Number Lookup
        var urlf = "{{ URL::to('/admin/find-name') }}";
        $("body").delegate(".donor", "keyup", function(event) {
            event.preventDefault();
            var donoracc = $(this).val();
            var row = $(this).parents('.item-row');

            $.ajax({
                url: urlf,
                method: "POST",
                data: { accno: donoracc },
                success: function(d) {
                    if (d.status == 300) {
                        row.find('.donorAcc').val(d.donorname);
                        row.find('.donorid').val(d.donorid);
                    }
                },
                error: function(d) {
                    console.log(d);
                }
            });
        });

        // Calculate Total
        net_total();
        $("body").delegate(".amount", "keyup", function(event) {
            net_total();
        });

        function net_total() {
            var total = 0;
            $('.amount').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total').val(total.toFixed(2));
        }
    });
</script>
@endsection