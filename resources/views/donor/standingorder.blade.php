@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')
          
    <section class="px-4">
        <div class="row  my-3">
    
            <div class="col-md-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <button class="nav-link active" id="transactionOut-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Current</button>
                      <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-transcationIn" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Previous</button>
    
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    {{-- Current order start  --}}
    
    
                    <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                              <section class="px-4"  id="contentContainer">
                                <div class="row my-3">
                    
                                    <div class="col-md-12 mt-2 text-center">
                                        <div class="overflow">
                                            <table class="table table-custom shadow-sm bg-white">
                                                <thead>
                                                    <tr>
                                                        <th>Sl</th>
                                                        <th>Starting</th>
                                                        <th>Beneficiary</th>
                                                        <th>amount</th>
                                                        <th>Annonymous Donation</th>
                                                        <th>Standing Order</th>
                                                        <th>Charity Note</th>
                                                        <th>Note</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $n = 1;
                                                    ?>
                                                    @forelse ($donation as $data)
                                                        <tr>
                                                            <td>{{$n++}}</td>
                                                            <td>{{$data->starting}}</td>
                                                            <td>{{$data->charity->name}}</td>
                                                            <td>£{{$data->amount}}</td>
                                                            <td>@if ($data->ano_donation == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif</td>
                                                            <td>@if ($data->standing_order == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif</td>
                                                            <td>{{$data->charitynote}}</td>
                                                            <td>{{$data->mynote}}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-warning edit-standing-btn"
                                                                        data-id="{{ $data->id }}"
                                                                        data-amount="{{ $data->amount }}"
                                                                        data-starting="{{ $data->starting }}"
                                                                        data-payments="{{ $data->payments }}"
                                                                        data-number_payments="{{ $data->number_payments }}"
                                                                        data-interval="{{ $data->interval }}"
                                                                        data-charitynote="{{ $data->charitynote }}"
                                                                        data-mynote="{{ $data->mynote }}"
                                                                        data-details="{{ json_encode($data->standingdonationDetail) }}">
                                                                    <span class="iconify" data-icon="clarity:edit-solid"></span>
                                                                </button>
                                                            </td>
                                                           
                                                        </tr>
                                                    @empty
                                                    <div class="row">
                                                      <div class="col-md-6 mx-auto d-flex align-items-center justify-content-center">
                                                    <div class="orderInfo">
                                                      You currently have no Standing Orders.
                                                      <a class="btn  btn-info my-3 text-white">Setup Standing Order</a>
                                                    </div>                                                                         
                                                  </div>
                                                </div>
                                                @endforelse              
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>  
    
                    </div>
    
                    {{-- current order end  --}}
    
                    {{-- previous order start  --}}
                    <div class="tab-pane fade" id="nav-transcationIn" role="tabpanel" aria-labelledby="nav-profile-tab">
                     
                      <section class="px-4"  id="contentContainer">
                        <div class="row my-3">
            
                            <div class="col-md-12 mt-2 text-center">
                                <div class="overflow">
                                    <table class="table table-custom shadow-sm bg-white">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Starting</th>
                                                <th>Beneficiary</th>
                                                <th>amount</th>
                                                <th>Annonymous Donation</th>
                                                <th>Standing Order</th>
                                                <th>Charity Note</th>
                                                <th>Note</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $n = 1;
                                            ?>
                                            @forelse ($pdonation as $data)
                                                <tr>
                                                    <td>{{$n++}}</td>
                                                    <td>{{$data->starting}}</td>
                                                    <td>{{$data->charity->name}}</td>
                                                    <td>£{{$data->amount}}</td>
                                                    <td>@if ($data->ano_donation == "true")
                                                        Yes
                                                    @else
                                                        No
                                                    @endif</td>
                                                    <td>@if ($data->standing_order == "true")
                                                        Yes
                                                    @else
                                                        No
                                                    @endif</td>
                                                    <td>{{$data->charitynote}}</td>
                                                    <td>{{$data->mynote}}</td>
                                                    <td>
                                                        
                                                    </td>
                                                   
                                                </tr>
                                            @empty
                                            <div class="row">
                                              <div class="col-md-6 mx-auto d-flex align-items-center justify-content-center">
                                                <div class="orderInfo">
                                                  You don't have previous Standing Orders.
                                                  <a class="btn  btn-info my-3 text-white">Setup Standing Order</a>
                                                </div>
                                              </div>
                                          </div>
                                            @endforelse
            
            
            
            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>  
                      
    
    
    
                    </div>
                    {{-- Previous order end  --}}
    
                  </div>
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
                <div id="editAlert" class="alert alert-danger" style="display:none;"></div>
                
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

                    <hr>
                    <h6 class="fw-bold mb-3">Instalment Details History</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Instalment Date</th>
                                    <th>Amount</th>
                                    <th>Mode</th>
                                </tr>
                            </thead>
                            <tbody id="detailsTableBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveStandingChanges" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
 $(document).ready(function() {

    // 1. Open Modal and Populate Data
    $('.edit-standing-btn').on('click', function() {
        $('#editAlert').hide().html('');
        
        // Get data from button attributes
        var id = $(this).data('id');
        var amount = $(this).data('amount');
        var starting = $(this).data('starting');
        var payments = $(this).data('payments');
        var number_payments = $(this).data('number_payments');
        var interval = $(this).data('interval');
        var charitynote = $(this).data('charitynote');
        var mynote = $(this).data('mynote');
        var details = $(this).data('details'); // JSON object of standingdonationDetail

        // Populate form fields
        $('#edit_donation_id').val(id);
        $('#edit_amount').val(amount);
        $('#edit_starting').val(starting);
        $('#edit_payments').val(payments);
        $('#edit_number_payments').val(number_payments);
        $('#edit_interval').val(interval);
        $('#edit_charitynote').val(charitynote);
        $('#edit_mynote').val(mynote);

        // Handle payment type dropdown logic
        if(payments == "2") {
            $('#edit_numPaymentsCol').hide();
        } else {
            $('#edit_numPaymentsCol').show();
        }

        // Populate Details Table
        $('#detailsTableBody').empty();
        if(details && details.length > 0) {
            $.each(details, function(index, row) {
                var statusText = row.status == 1 ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-warning text-dark">Pending</span>';
                $('#detailsTableBody').append(`
                    <tr>
                        <td>${row.instalment_date}</td>
                        <td>£${row.amount}</td>
                        <td>${row.instalment_mode}</td>
                    </tr>
                `);
            });
        } else {
            $('#detailsTableBody').append('<tr><td colspan="4" class="text-center">No instalment details found.</td></tr>');
        }

        // Show modal
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
        var url = "{{ URL::to('/admin/update-stnddonation') }}";
        var formData = $('#editStandingForm').serialize(); // Gathers all form data including CSRF

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            beforeSend: function() {
                $('#saveStandingChanges').prop('disabled', true).html('Saving...');
            },
            success: function(response) {
                if(response.status == 300) {
                    // Success
                    $('#editAlert').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                    setTimeout(function() {
                        location.reload(); // Reload page to see changes
                    }, 1500);
                } else if(response.status == 303) {
                    // Validation error
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
