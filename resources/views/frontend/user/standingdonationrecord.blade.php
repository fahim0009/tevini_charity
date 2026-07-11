@extends('frontend.layouts.user')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp

<style>
    /* Base wrapper */
    .switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
    }

    /* Hide default checkbox */
    .switch input {
    opacity: 0;
    width: 0;
    height: 0;
    }

    /* The slider (background) */
    .slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 34px;
    }

    /* The circle knob */
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

    .switch {
        border: 0px !important;
    }

    /* Checked state */
    .switch input:checked + .slider {
    background-color: #18988b;
    }

    /* Move knob when checked */
    .switch input:checked + .slider:before {
    transform: translateX(22px);
    }

    /* Optional glowing effect on active state */
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
            <span class="iconify" data-icon="icomoon-free:profile"></span> <div class="mx-2">Standing Donation records </div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3">

        <div class="col-md-12">

                {{-- Current order start  --}}

                          <section class="px-4"  id="contentContainer">
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
                                                    <th>View</th>
                                                    <th>Edit</th> <!-- NEW -->
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($donation as $data)
                                                    <tr>
                                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                                        <td>{{ Carbon::parse($data->starting)->format('d/m/Y')}}</td>
                                                        <td>{{$data->charity->name}}</td>
                                                        <td>£{{$data->amount}}</td>
                                                        <td>@if ($data->ano_donation == "true") Yes @else No @endif</td>
                                                        <td>{{$data->charitynote}}</td>
                                                        <td>{{$data->mynote}}</td>
                                                        <td>
                                                            <a href="{{ route('user.singlestanding', $data->id)}}">
                                                                <i class="fa fa-eye" style="color: #09a311;font-size:16px;"></i> 
                                                            </a>
                                                        </td>
                                                        <!-- NEW EDIT BUTTON -->
                                                        <td>
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
                                                        </td>
                                                        <td style="text-align: center">
                                                            <label class="switch">
                                                                <input type="checkbox" class="standingdnstatus" data-id="{{ $data->id }}"  @if ($data->status == 1) checked @endif>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>


                {{-- current order end  --}}


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

@endsection

@section('script')
<script>
    $(document).ready(function () {
   //header for csrf-token is must in laravel
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
   
       $(function() {
         $('.standingdnstatus').change(function() {
           var url = "{{URL::to('/user/active-standingdonation')}}";
             var status = $(this).prop('checked') == true ? 1 : 0;
             var id = $(this).data('id');
   
             $.ajax({
                 url: url,
                 method: "POST",
                 data: {'status': status, 'id': id},
                 success: function(d){
                   if (d.status == 303) {
                           pagetop();
                           $(".stsermsg").html(d.message);
                           window.setTimeout(function(){location.reload()},2000)
                       }else if(d.status == 300){
                           pagetop();
                           $(".stsermsg").html(d.message);
                           window.setTimeout(function(){location.reload()},2000)
                       }
                   },
                   error: function (d) {
                       console.log(d);
                   }
             });
         })
       })
   
   });
   </script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#standingorder").addClass('active');
    });
</script>


<script>
 $(document).ready(function() {

    // 1. Open Modal and Populate Data
    $('.edit-standing-btn').on('click', function() {
        $('#editAlert').hide().html('');
        
        var id = $(this).data('id');
        var amount = $(this).data('amount');
        var starting = $(this).data('starting');
        var payments = $(this).data('payments');
        var number_payments = $(this).data('number_payments');
        var interval = $(this).data('interval');
        var charitynote = $(this).data('charitynote');
        var mynote = $(this).data('mynote');

        $('#edit_donation_id').val(id);
        $('#edit_amount').val(amount);
        $('#edit_starting').val(starting);
        $('#edit_payments').val(payments);
        $('#edit_number_payments').val(number_payments);
        $('#edit_interval').val(interval);
        $('#edit_charitynote').val(charitynote);
        $('#edit_mynote').val(mynote);

        if(payments == "2") {
            $('#edit_numPaymentsCol').hide();
        } else {
            $('#edit_numPaymentsCol').show();
        }

        var modal = new bootstrap.Modal(document.getElementById('editStandingModal'));
        modal.show();
    });

    // Handle Payment Type change inside modal
    $('#edit_payments').on('change', function() {
        if($(this).val() == "2") {
            $('#edit_numPaymentsCol').hide();
        } else {
            $('#edit_numPaymentsCol').show();
        }
    });

    // 2. Submit Edit Form via AJAX
    $('#saveStandingChanges').on('click', function() {
        var url = "{{ URL::to('/user/update-standingdonation') }}";
        var formData = $('#editStandingForm').serialize();

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            beforeSend: function() {
                $('#saveStandingChanges').prop('disabled', true).html('Saving...');
            },
            success: function(response) {
                if(response.status == 300) {
                    $('#editAlert').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else if(response.status == 303) {
                    $('#editAlert').removeClass('alert-success').addClass('alert-danger').html(response.message).show();
                }
            },
            complete: function() {
                $('#saveStandingChanges').prop('disabled', false).html('Save Changes');
            },
            error: function() {
                $('#editAlert').removeClass('alert-success').addClass('alert-danger').html('An error occurred. Please try again.').show();
            }
        });
    });

});
</script>


@endsection
