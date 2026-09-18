@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Expired Voucher</div>
        </div>
    </section>
<!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
   </div>
 <!-- Image loader -->
    <div class="ermsg"></div>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">

                        <div class="col-md-1 my-1">
                        </div>

                        <div class="col-md-4 my-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label" for="checkAll">
                                  All Select
                                </label>
                            </div>
                            <button class="btn btn-primary" id="vsrComplete" type="button">Complete</button>
                            <button class="btn btn-danger" id="vsrCancel" type="button">Cancel</button>
                            <button class="btn btn-success" id="vsrMail" type="button">Send Mail</button>
                                <button class="btn btn-warning" id="vsrBulkEdit" type="button">Edit</button>
                        </div>

                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th style=""></th>
                                            <th>Date</th>
                                            <th>Charity</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($data as $voucher)

                                        <tr>
                                                <td style="">
                                                    <input class="form-check-input getvid" type="checkbox" name="voucherId[]" donor_id="{{ $voucher->user_id }}" charity_id="{{ $voucher->charity_id }}" value="{{ $voucher->id }}">
                                                </td>
                                                <td><span style="display:none;">{{ $voucher->id }}</span>{{ $voucher->created_at->format('d/m/Y')}} </td>
                                                <td>{{ $voucher->charity->name}} </td>
                                                <td>{{ $voucher->user->name }} {{ $voucher->user->surname }}</td>
                                                <td>{{ $voucher->cheque_no}}</td>
                                                <td>{{ $voucher->note}}</td>
                                                <td>£{{ $voucher->amount}}</td>
                                                <td><input type="file" id="image{{ $voucher->id }}" process_voucher_id="{{ $voucher->id }}" name="image{{ $voucher->id }}" class="txt-theme txt-secondary fs-14 my-2"></td>
                                                <td>
                                                @if($voucher->status == "0") Pending @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning editCharityBtn" 
                                                            data-voucher_id="{{ $voucher->id }}" 
                                                            data-current_charity="{{ $voucher->charity_id }}"
                                                            data-current_donor="{{ $voucher->user_id }}"
                                                            data-current_note="{{ $voucher->note }}"
                                                            data-current_status="{{ $voucher->expired == 'Yes' ? 'Expired' : 'Pending' }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                </td>

                                        </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
  </section>
</div>

<!-- Voucher Edit Modal -->
<div class="modal fade" id="editCharityModal" tabindex="-1" aria-labelledby="editCharityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCharityModalLabel">Edit Voucher Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editing_voucher_id">
        
        <div class="form-group mb-2">
            <label for="new_charity_id">Select Charity</label>
            <select class="form-control" id="new_charity_id">
                @foreach (\App\Models\Charity::all() as $charity)
                    <option value="{{ $charity->id }}">{{ $charity->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2">
            <label for="new_donor_id">Select Donor</label>
            <select class="form-control select2" id="new_donor_id">
                @foreach (\App\Models\User::where('is_type','user')->get() as $donor)
                    <option value="{{ $donor->id }}">{{ $donor->name }} {{ $donor->surname }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-2">
            <label for="new_voucher_status">Change Status</label>
            <select class="form-control" id="new_voucher_status">
                <option value="Pending">Pending</option>
                <option value="Waiting">Waiting</option>
                <option value="Expired">Expired</option>
            </select>
        </div>

        <div class="form-group">
            <label for="new_note">Note</label>
            <textarea class="form-control" id="new_note" rows="3" placeholder="Enter note..."></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveNewCharityBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>


@endsection

@section('script')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {


    $('#new_charity_id, #new_donor_id').select2({
        width: '100%',
        dropdownParent: $('#editCharityModal')
    });



    $("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
    });



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

// select and confirm
var url = "{{URL::to('/admin/waiting-vouchercomplete')}}";

$("#vsrComplete").click(function(){
    $("#loading").show();
    var voucherIds = [];
    $('.getvid:checkbox:checked').each(function(i){
        voucherIds[i] = $(this).val();
        });

    var charityIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        charityIds[i] = $(this).attr('charity_id');
    });
    
    var donorIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        donorIds[i] = $(this).attr('donor_id');
    });  


        $.ajax({
            url: url,
            method: "POST",
            data: {voucherIds,charityIds,donorIds},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
                    setTimeout(() => location.reload(), 3000);

                }
            },
            complete:function(d){
                        $("#loading").hide();
                    },
            error: function (d) {
                console.log(d);
            }
        });

});


// select and cancel
var urlc = "{{URL::to('/admin/waiting-vouchercancel')}}";

$("#vsrCancel").click(function(){
    $("#loading").show();
    var voucherIds = [];
    $('.getvid:checkbox:checked').each(function(i){
        voucherIds[i] = $(this).val();
        });

    var charityIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        charityIds[i] = $(this).attr('charity_id');
    });
    
    var donorIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        donorIds[i] = $(this).attr('donor_id');
    });  

        $.ajax({
            url: urlc,
            method: "POST",
            data: {voucherIds,charityIds,donorIds},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
                    setTimeout(() => location.reload(), 3000);

                }
            },
            complete:function(d){
                        $("#loading").hide();
                    },
            error: function (d) {
                console.log(d);
            }
        });

});

//upload image for send mail
var urlimgadd = "{{URL::to('/admin/waiting-voucherimgadd')}}";
$("input[type='file']").on("change", function() {
    var image = $(this)[0].files[0];
    var process_voucher_id = $(this).attr('process_voucher_id');
    var formData = new FormData();
    formData.append('image', image);
    formData.append('process_voucher_id', process_voucher_id);

        $.ajax({
        url: urlimgadd,
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (d) {
            if (d.status == 303) {
                $(".ermsg").html(d.message);
                pagetop();
            }else if(d.status == 300){
                $(".ermsg").html(d.message);
                pagetop();
                setTimeout(() => location.reload(), 3000);

            }
        },
        complete:function(d){
                    $("#loading").hide();
                },
        error: function (d) {
            console.log(d);
        }
    });

});


// mail send
var urlmail = "{{URL::to('/admin/waiting-vouchermail')}}";
$("#vsrMail").click(function(){
    $("#loading").show();
    var voucherIds = [];
    $('.getvid:checkbox:checked').each(function(i){
        voucherIds[i] = $(this).val();
        });

    var charityIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        charityIds[i] = $(this).attr('charity_id');
    });
    
    var donorIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        donorIds[i] = $(this).attr('donor_id');
    });  

        $.ajax({
            url: urlmail,
            method: "POST",
            data: {voucherIds,charityIds,donorIds},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
                    setTimeout(() => location.reload(), 3000);

                }
            },
            complete:function(d){
                        $("#loading").hide();
                    },
            error: function (d) {
                console.log(d);
            }
        });

});


// Edit Button Click Handler (Expired Page)
 $(document).on('click', '.editCharityBtn', function() {
    var voucherId = $(this).data('voucher_id');
    var currentCharity = $(this).data('current_charity');
    var currentDonor = $(this).data('current_donor'); 
    var currentNote = $(this).data('current_note');
    var currentStatus = $(this).data('current_status');
    
    $('#editing_voucher_id').val(voucherId);
    $('#new_charity_id').val(currentCharity).trigger('change');
    $('#new_donor_id').val(currentDonor).trigger('change'); 
    $('#new_note').val(currentNote);
    $('#new_voucher_status').val(currentStatus);
    
    $('#editCharityModal').modal('show');
});

// Save Button Click Handler (Expired Page)
 $("#saveNewCharityBtn").click(function() {
    $("#loading").show();
    var voucherId = $('#editing_voucher_id').val();
    var newCharityId = $('#new_charity_id').val();
    var newDonorId = $('#new_donor_id').val();
    var newNote = $('#new_note').val();
    var newStatus = $('#new_voucher_status').val();
    
    $.ajax({
        url: "{{ route('voucher.updateDetails') }}",
        method: "POST",
        data: { 
            voucher_id: voucherId, 
            charity_id: newCharityId, 
            donor_id: newDonorId, 
            note: newNote, 
            voucher_status: newStatus 
        },
        success: function (d) {
            $("#loading").hide();
            if (d.status == 300) {
                $("#editCharityModal").modal('hide');
                location.reload(); 
            } else {
                alert(d.message);
            }
        },
        error: function (d) {
            $("#loading").hide();
            console.log(d);
        }
    });
});

// Bulk Edit Button Click
 $("#vsrBulkEdit").click(function(){
    var voucherIds = [];
    $('.getvid:checkbox:checked').each(function(i){
        voucherIds[i] = $(this).val();
    });

    if(voucherIds.length === 0){
        alert("Please select at least one voucher to edit.");
        return;
    }

    var idsString = voucherIds.join(',');
    
    var editUrl = "{{ route('voucher.bulkEdit') }}?ids=" + idsString;
    window.location.href = editUrl;
});



});
</script>
@endsection

