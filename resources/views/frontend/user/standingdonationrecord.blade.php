@extends('frontend.layouts.user')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        top: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .switch { border: 0px !important; }
    .switch input:checked + .slider {
        background-color: #18988b;
    }
    .switch input:checked + .slider:before {
        transform: translateX(22px);
    }
    .switch input:checked + .slider {
        box-shadow: 0 0 10px rgba(24,152,139,0.5);
    }
    .switch input:checked + .slider:before {
        animation: pulse 0.6s ease;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(24,152,139,0.6); }
        70% { box-shadow: 0 0 0 8px rgba(24,152,139,0); }
        100% { box-shadow: 0 0 0 0 rgba(24,152,139,0); }
    }
</style>

<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icomoon-free:profile"></span>
            <div class="mx-2">Standing Donation records</div>
        </div>
    </section>

    <section class="">
        <div class="row my-3">
            <div class="col-md-12">
                <section class="px-4" id="contentContainer">
                    <div class="row my-3">
                        <div class="stsermsg"></div>
                        <div class="col-md-12 mt-2 text-center shadow-sm">
                            <div class="overflow pt-3">
                                <table class="table" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Starting Date</th>
                                            <th>Beneficiary</th>
                                            <th>Amount</th>
                                            <th>Anonymous</th>
                                            <th>Charity Note</th>
                                            <th>Note</th>
                                            <th>Donations Made</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($donations as $data)
                                            <tr>
                                                <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                                <td>{{ Carbon::parse($data->starting)->format('d/m/Y') }}</td>
                                                <td>{{ $data->charity->name }}</td>
                                                <td>£{{ $data->amount }}</td>
                                                <td>@if ($data->ano_donation == "true") Yes @else No @endif</td>
                                                <td>{{ $data->charitynote }}</td>
                                                <td>{{ $data->mynote }}</td>
                                                <td>
                                                    <span class="badge btn-theme bg-secondary">{{ $data->donation_count }}</span>
                                                </td>
                                                <!-- View Button -->
                                                <td>
                                                    <a href="{{ route('user.singlestanding', $data->id) }}">
                                                        <i class="fa fa-eye" style="color: #09a311;font-size:16px;"></i>
                                                    </a>
                                                </td>

                                                <!-- Edit Button (Disabled if completed) -->
                                                <td>
                                                    @if ($data->status == 2 || $data->donation_count >= $data->number_payments || $data->status == 0)
                                                        <button type="button" class="btn-theme bg-secondary" disabled title="This donations cannot be edited">
                                                            <i class="fa fa-lock"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn-theme bg-primary edit-standing-btn" 
                                                                data-id="{{ $data->id }}"
                                                                data-amount="{{ $data->amount }}"
                                                                data-starting="{{ $data->starting }}"
                                                                data-payments="{{ $data->payments }}"
                                                                data-number_payments="{{ $data->number_payments }}"
                                                                data-interval="{{ $data->interval }}"
                                                                data-charitynote="{{ $data->charitynote }}"
                                                                data-mynote="{{ $data->mynote }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    @endif
                                                </td>

                                                <!-- Status Toggle / Completed Badge -->
                                                <td style="text-align: center">
                                                    @if ($data->status == 2)
                                                        <span class="badge rounded-pill bg-success p-2">
                                                            <i class="fa fa-check-circle"></i> Completed
                                                        </span>
                                                    @else
                                                        <label class="switch">
                                                            <input type="checkbox" 
                                                                class="standingdnstatus" 
                                                                data-id="{{ $data->id }}" 
                                                                data-charity="{{ $data->charity->name }}"
                                                                data-amount="{{ $data->amount }}"
                                                                data-starting="{{ $data->starting }}"
                                                                data-payments="{{ $data->payments }}"
                                                                data-number_payments="{{ $data->number_payments }}"
                                                                data-interval="{{ $data->interval }}"
                                                                data-charitynote="{{ $data->charitynote }}"
                                                                data-mynote="{{ $data->mynote }}"
                                                                data-anonymous="{{ $data->ano_donation }}"
                                                                @if ($data->status == 1) checked @endif>
                                                            <span class="slider"></span>
                                                        </label>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center">No standing donation records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>

<!-- Edit Standing Donation Modal -->
<div class="modal fade" id="editStandingModal" tabindex="-1" aria-labelledby="editStandingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStandingModalLabel">Edit Standing Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="editAlert" class="alert" style="display:none;"></div>
                <form id="editStandingForm">
                    @csrf
                    <input type="hidden" id="edit_donation_id" name="donation_id">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (£)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="edit_amount" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Starting Date</label>
                            <input type="date" class="form-control" name="starting" id="edit_starting" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Type</label>
                            <select class="form-select" name="payments" id="edit_payments">
                                <option value="1">Fixed number of payments</option>
                                <option value="2">Continuous payments</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="edit_numPaymentsCol">
                            <label class="form-label fw-semibold">Number of Payments</label>
                            <input type="number" class="form-control" name="number_payments" id="edit_number_payments">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Interval (Months)</label>
                            <select class="form-select" name="interval" id="edit_interval">
                                <option value="1">Monthly</option>
                                <option value="3">Every 3 months</option>
                                <option value="6">Every 6 months</option>
                                <option value="12">Yearly</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Charity Note</label>
                            <textarea class="form-control" name="charitynote" id="edit_charitynote" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Personal Note</label>
                            <textarea class="form-control" name="mynote" id="edit_mynote" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-theme bg-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveStandingChanges" class="btn-theme bg-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Re-Activate Standing Donation Modal (Now Editable) -->
<div class="modal fade" id="activateStandingModal" tabindex="-1" aria-labelledby="activateStandingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="activateStandingModalLabel">
                    <i class="fa fa-refresh"></i> 
                    *Set Up a New Standing Order*
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="activateAlert" class="alert" style="display:none;"></div>

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Resuming this standing order will be treated as setting up a new standing order. You’ll need to choose your monthly donation amount and donation duration before continuing.
                    <p class="mt-2"><strong>Would you like to proceed? </strong></p>
                </div>

                <form id="activateStandingForm">
                    @csrf
                    <input type="hidden" id="activate_donation_id" name="id">
                    <input type="hidden" name="status" value="1">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Beneficiary</label>
                            <input type="text" class="form-control" id="activate_charity" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (£)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="activate_amount" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Starting Date</label>
                            <input type="date" class="form-control" name="starting" id="activate_starting" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Type</label>
                            <select class="form-select" name="payments" id="activate_payments">
                                <option value="1">Fixed number of payments</option>
                                <option value="2">Continuous payments</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6" id="activate_numPaymentsCol">
                            <label class="form-label fw-semibold">Number of Payments</label>
                            <input type="number" class="form-control" name="number_payments" id="activate_number_payments">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Interval (Months)</label>
                            <select class="form-select" name="interval" id="activate_interval">
                                <option value="1">Monthly</option>
                                <option value="3">Every 3 months</option>
                                <option value="6">Every 6 months</option>
                                <option value="12">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Charity Note</label>
                            <textarea class="form-control" name="charitynote" id="activate_charitynote" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Personal Note</label>
                            <textarea class="form-control" name="mynote" id="activate_mynote" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-theme bg-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmActivateStanding" class="btn-theme bg-success">
                    <i class="fa fa-check"></i> Confirm &amp; Re-Activate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- NEW: Pause Standing Donation Confirmation Modal -->
<div class="modal fade" id="pauseStandingModal" tabindex="-1" aria-labelledby="pauseStandingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pauseStandingModalLabel">
                    <i class="fa fa-pause-circle"></i> *Pause Standing Order?*
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pauseAlert" class="alert" style="display:none;"></div>
                
                <p>Are you sure you want to pause this standing order? Your regular donations to this charity will be temporarily stopped. You can resume donating at any time.</p>
                
                <input type="hidden" id="pause_donation_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-theme bg-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmPauseStanding" class="btn-theme bg-primary text-white">
                    <i class="fa fa-check"></i> Confirm Pause
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $("#standingorder").addClass('active');

        // ============================================================
        // 1. TOGGLE STATUS HANDLER
        // ============================================================
        $('.standingdnstatus').on('change', function (e) {
            var $checkbox = $(this);
            var status = $checkbox.prop('checked') == true ? 1 : 0;
            var id = $checkbox.data('id');

            if (status == 1) {
                // Trying to turn ON -> Revert visual state and show Activate Modal
                $checkbox.prop('checked', false);

                $('#activate_donation_id').val(id);
                $('#activate_charity').val($checkbox.data('charity'));
                $('#activate_amount').val($checkbox.data('amount'));
                $('#activate_starting').val($checkbox.data('starting'));
                $('#activate_payments').val($checkbox.data('payments'));
                $('#activate_number_payments').val($checkbox.data('number_payments'));
                $('#activate_interval').val($checkbox.data('interval'));
                $('#activate_charitynote').val($checkbox.data('charitynote'));
                $('#activate_mynote').val($checkbox.data('mynote'));

                if ($checkbox.data('payments') == "2") {
                    $('#activate_numPaymentsCol').hide();
                } else {
                    $('#activate_numPaymentsCol').show();
                }

                $('#activateAlert').hide().html('');

                var modal = new bootstrap.Modal(document.getElementById('activateStandingModal'));
                modal.show();

            } else {
                // Trying to turn OFF -> Revert visual state and show Pause Modal
                $checkbox.prop('checked', true); // Keep it checked until confirmed

                $('#pause_donation_id').val(id);
                $('#pauseAlert').hide().html('');
                
                var modal = new bootstrap.Modal(document.getElementById('pauseStandingModal'));
                modal.show();
            }
        });

        // Handle Payment Type change inside activate modal
        $('#activate_payments').on('change', function () {
            if ($(this).val() == "2") {
                $('#activate_numPaymentsCol').hide();
            } else {
                $('#activate_numPaymentsCol').show();
            }
        });

        // ============================================================
        // 2. CONFIRM ACTIVATION (Submit Edited Data for Re-Creation)
        // ============================================================
        $('#confirmActivateStanding').on('click', function () {
            var url = "{{ URL::to('/user/active-standingdonation') }}";
            var formData = $('#activateStandingForm').serialize();
            var $btn = $(this);

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                beforeSend: function () {
                    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Activating...');
                },
                success: function (d) {
                    if (d.status == 300 || d.status == 303) {
                        $('#activateAlert')
                            .html(d.message)
                            .show();
                        pagetop();
                        $(".stsermsg").html(d.message);
                        setTimeout(function () { location.reload(); }, 1500);
                    } else {
                        $('#activateAlert')
                            .html(d.message || 'Failed to activate.')
                            .show();
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false)
                        .html('<i class="fa fa-check"></i> Confirm &amp; Re-Activate');
                },
                error: function () {
                    $('#activateAlert')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html('An error occurred. Please try again.')
                        .show();
                }
            });
        });

        // ============================================================
        // 3. CONFIRM PAUSE (Turn OFF Status)
        // ============================================================
        $('#confirmPauseStanding').on('click', function () {
            var url = "{{ URL::to('/user/active-standingdonation') }}";
            var id = $('#pause_donation_id').val();
            var $btn = $(this);

            $.ajax({
                url: url,
                method: "POST",
                data: { 'status': 0, 'id': id },
                beforeSend: function () {
                    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Pausing...');
                },
                success: function (d) {
                    if (d.status == 300 || d.status == 303) {
                        $('#pauseAlert')
                            .html(d.message)
                            .show();
                        pagetop();
                        $(".stsermsg").html(d.message);
                        setTimeout(function () { location.reload(); }, 1500);
                    } else {
                        $('#pauseAlert')
                            .html(d.message || 'Failed to pause.')
                            .show();
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Pause');
                },
                error: function () {
                    $('#pauseAlert')
                        .addClass('alert-danger')
                        .html('An error occurred. Please try again.')
                        .show();
                }
            });
        });

        // ============================================================
        // 4. EDIT STANDING DONATION (unchanged)
        // ============================================================
        $('.edit-standing-btn').on('click', function () {
            $('#editAlert').hide().html('');

            $('#edit_donation_id').val($(this).data('id'));
            $('#edit_amount').val($(this).data('amount'));
            $('#edit_starting').val($(this).data('starting'));
            $('#edit_payments').val($(this).data('payments'));
            $('#edit_number_payments').val($(this).data('number_payments'));
            $('#edit_interval').val($(this).data('interval'));
            $('#edit_charitynote').val($(this).data('charitynote'));
            $('#edit_mynote').val($(this).data('mynote'));

            if ($(this).data('payments') == "2") {
                $('#edit_numPaymentsCol').hide();
            } else {
                $('#edit_numPaymentsCol').show();
            }

            var modal = new bootstrap.Modal(document.getElementById('editStandingModal'));
            modal.show();
        });

        $('#edit_payments').on('change', function () {
            if ($(this).val() == "2") {
                $('#edit_numPaymentsCol').hide();
            } else {
                $('#edit_numPaymentsCol').show();
            }
        });

        $('#saveStandingChanges').on('click', function () {
            var url = "{{ URL::to('/user/update-standingdonation') }}";
            var formData = $('#editStandingForm').serialize();

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                beforeSend: function () {
                    $('#saveStandingChanges').prop('disabled', true).html('Saving...');
                },
                success: function (response) {
                    if (response.status == 300) {
                        $('#editAlert').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                        setTimeout(function () { location.reload(); }, 1500);
                    } else if (response.status == 303) {
                        $('#editAlert').removeClass('alert-success').addClass('alert-danger').html(response.message).show();
                    }
                },
                complete: function () {
                    $('#saveStandingChanges').prop('disabled', false).html('Save Changes');
                },
                error: function () {
                    $('#editAlert').removeClass('alert-success').addClass('alert-danger').html('An error occurred. Please try again.').show();
                }
            });
        });
    });
</script>
@endsection