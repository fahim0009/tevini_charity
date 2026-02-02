@extends('layouts.admin')
@section('content')

<style>
    .dashboard-content { background: #f4f7f6; font-size: 0.9rem; }
    .card-sm { 
        background: #fff; border-radius: 8px; border: 1px solid #dee2e6; 
        padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    /* Compact Table for Vouchers */
    .v-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .v-table tr { background: #fff; }
    .v-table td { 
        padding: 10px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;
    }
    .v-table td:first-child { border-left: 1px solid #eee; border-radius: 6px 0 0 6px; }
    .v-table td:last-child { border-right: 1px solid #eee; border-radius: 0 6px 6px 0; }

    /* Small Qty Controls */
    .qty-group { display: flex; align-items: center; border: 1px solid #cbd5e0; border-radius: 4px; width: fit-content; }
    .qty-btn { border: none; background: #fff; padding: 2px 10px; font-weight: bold; }
    .qty-btn:hover { background: #edf2f7; }
    .qty-input { width: 35px; text-align: center; border: none; font-size: 0.9rem; font-weight: 600; }

    /* Compact Delivery Options */
    .delivery-opt {
        border: 1px solid #cbd5e0; border-radius: 6px; padding: 8px 12px;
        display: flex; align-items: center; cursor: pointer; transition: 0.2s;
    }
    .delivery-opt:hover { border-color: #3182ce; }
    .delivery-opt.active { background: #ebf8ff; border-color: #3182ce; }
    .delivery-opt input { margin-right: 10px; }
    .delivery-opt small { color: #718096; line-height: 1.1; display: block; }

    /* Sidebar Summary */
    .sticky-summary { position: sticky; top: 10px; background: #2d3748; color: #fff; border-radius: 8px; padding: 15px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.85rem; }
    .summary-total { border-top: 1px solid #4a5568; padding-top: 10px; margin-top: 10px; font-weight: bold; font-size: 1.1rem; }
    
    .btn-submit { 
        width: 100%; padding: 10px; background: #38a169; color: white; 
        border: none; border-radius: 5px; font-weight: bold; margin-top: 15px;
    }



    /* Professional Full-Page Loader Overlay */
#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px); /* Modern blur effect */
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.spinner-container {
    text-align: center;
}

.custom-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #38a169; /* Matches your Green Button */
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-weight: 600;
    color: #2d3748;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-size: 0.8rem;
}


</style>

<div class="dashboard-content px-3 py-3">

    <div id="loading-overlay" style="display: none;">
        <div class="spinner-container">
            <div class="custom-spinner"></div>
            <div class="loading-text">Processing Order...</div>
        </div>
    </div>

    @include('inc.user_menue')

    <div class="row gx-3">
        <div class="col-lg-8">
            <div class="card-sm">
                <h6 class="fw-bold mb-3"><span class="iconify" data-icon="et:wallet"></span> Select Voucher Quantities</h6>
                <div class="ermsg"></div>
                
                <table class="v-table">
                    @foreach (App\Models\Voucher::where('status','1')->get() as $voucher )
                    <tr>
                        <td width="30%">
                            <span class="fw-bold text-dark">£{{ $voucher->amount }}</span> 
                            <span class="badge {{ $voucher->type == 'Prepaid' ? 'bg-secondary' : 'bg-info' }}" style="font-size: 0.7rem;">{{ $voucher->type }}</span>
                        </td>
                        <td width="40%">
                            <div class="qty-group">
                                <button type="button" class="qty-btn" onclick="changeQty({{$voucher->id}}, -1, {{ $voucher->amount }})">-</button>
                                <input type="text" name="qty[]" id="cartValue{{$voucher->id}}" class="qty-input" value="0" v_amount="{{ $voucher->amount }}" v_type="{{ $voucher->type }}" v_id="{{$voucher->id}}" readonly>
                                <button type="button" class="qty-btn" onclick="changeQty({{$voucher->id}}, 1, {{ $voucher->amount }})">+</button>
                            </div>
                            <input type="hidden" class="total" id="vamnt{{$voucher->id}}" value="0">
                            <input type="hidden" name="v_ids[]" value="{{$voucher->id}}">
                        </td>
                        <td class="text-end fw-bold" width="30%">
                            <span id="amt{{$voucher->id}}" class="text-primary">£0.00</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>

            <div class="card-sm">
                <h6 class="fw-bold mb-3">Delivery Method</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="delivery-opt" for="delivery">
                            <input type="checkbox" id="delivery" name="delivery" class="delivery_option">
                            <div>
                                <strong>Express</strong>
                                <small>1-2 Days (£3.50)</small>
                            </div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="delivery-opt" for="collection">
                            <input type="checkbox" id="collection" name="collection" class="delivery_option">
                            <div>
                                <strong>Collection</strong>
                                <small>London N16 5HN</small>
                            </div>
                        </label>
                    </div>
                </div>
                <div id="dmsg" class="mt-2" style="display:none">
                    <small class="text-danger">* £3.50 charge added (Order < £200)</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-summary">
                <h6 class="text-uppercase small fw-bold mb-3" style="color: #a0aec0;">Order Details</h6>
                <input type="hidden" value="{{$donor_id}}" id="donner_id">
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="display_subtotal">£0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="display_charge">£0.00</span>
                </div>
                
                <div class="summary-total">
                    <div class="d-flex justify-content-between">
                        <span>Total</span>
                        <input type="text" id="net_total_display" value="£0.00" class="bg-transparent border-0 text-white text-end fw-bold w-50" readonly>
                        <input type="hidden" id="net_total" value="0">
                    </div>
                </div>

                <button class="btn-submit" id="addvoucher" type="button">ORDER NOW</button>
                
                <div id="loading" class="text-center mt-2" style="display:none;">
                    <div class="spinner-border spinner-border-sm text-light"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src='{{ asset('assets/user/js/app.js') }}'> </script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $('.delivery_option').change(function() {
            $('.delivery-opt').removeClass('active');
            if(this.checked) {
                $(this).closest('.delivery-opt').addClass('active');
                $('.delivery_option').not(this).prop('checked', false).closest('.delivery-opt').removeClass('active');
            }
            calculateTotals();
        });

        $("#addvoucher").click(function() {
            let del = $("#delivery").is(":checked");
            let col = $("#collection").is(":checked");
            
            if (!del && !col) { 
                alert('Please select a delivery option'); 
                return; 
            }

            // Show the professional loader
            $("#loading-overlay").fadeIn(200);
            // Disable button to prevent double clicks
            $(this).prop('disabled', true).text('PLEASE WAIT...');

            let data = {
                voucherIds: $("input[name='v_ids[]']").map(function(){return $(this).val();}).get(),
                qtys: $("input[name='qty[]']").map(function(){return $(this).val();}).get(),
                did: $("#donner_id").val(),
                delivery_charge: $("#display_charge").text().replace('£', ''),
                delivery: del,
                collection: col
            };

            $.post("{{URL::to('/admin/addvoucher')}}", data, function(d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    // Hide loader so user can fix errors
                    $("#loading-overlay").fadeOut(200);
                    $("#addvoucher").prop('disabled', false).text('ORDER NOW');
                    window.scrollTo(0, 0);
                } else if (d.status == 300) {
                    $("#loading-overlay").fadeOut(200);
                    $(".ermsg").html(d.message);
                    // Keep loader visible and redirect/reload
                    window.setTimeout(() => location.reload(), 2000);
                }
            }).fail(function() {
                alert("Something went wrong. Please try again.");
                $("#loading-overlay").fadeOut(200);
                $("#addvoucher").prop('disabled', false).text('ORDER NOW');
            });
        });
    });

    function changeQty(id, delta, price) {
        let input = $(`#cartValue${id}`);
        let newVal = Math.max(0, parseInt(input.val()) + delta);
        input.val(newVal);
        $(`#vamnt${id}`).val(newVal * price);
        $(`#amt${id}`).text(`£${(newVal * price).toFixed(2)}`);
        calculateTotals();
    }

    function calculateTotals() {
        let sub = 0;
        $('.total').each(function() { sub += parseFloat($(this).val() || 0); });
        
        let charge = ($("#delivery").is(":checked") && sub < 200 && sub > 0) ? 3.50 : 0;
        if(charge > 0) $("#dmsg").show(); else $("#dmsg").hide();

        $('#display_subtotal').text(`£${sub.toFixed(2)}`);
        $('#display_charge').text(`£${charge.toFixed(2)}`);
        $('#net_total_display').val(`£${(sub + charge).toFixed(2)}`);
        $('#net_total').val((sub + charge).toFixed(2));
    }
</script>
@endsection



