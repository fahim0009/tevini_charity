@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Pending Voucher</div>
        </div>
    </section>
    @if (isset($donor_id))
        @include('inc.user_menue')
    @endif
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
                            <button class="btn btn-success" id="vsrBulkEdit" type="button">Edit</button>
                        </div>

                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="pendingTable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Date</th>
                                            <th>Charity</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
            <select class="form-control select2" id="new_charity_id">
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
var url = "{{URL::to('/admin/pvcomplete')}}";

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


        $.ajax({
            url: url,
            method: "POST",
            data: {voucherIds,charityIds},

            success: function (d) {
                console.log(d.message);

                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
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
var urlc = "{{URL::to('/admin/pvcancel')}}";

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

        $.ajax({
            url: urlc,
            method: "POST",
            data: {voucherIds,charityIds},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
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


});
</script>

<script>
$(function () {
    if (!$.fn.DataTable.isDataTable('#pendingTable')) {
        initDT();
    }
});

function initDT() {
    $('#pendingTable').DataTable({
        processing: true,
        serverSide: true,

        // 🔥 required for export buttons
        dom: '<"html5buttons"B>lTfgitp',

        ajax: {
            url: "{{ route('pendingvoucher') }}",
            data: { id: "{{ $donor_id ?? '' }}" }
        },

        pageLength: 100,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],

        columns: [
            { data: 'checkbox', orderable: false, searchable: false }, 
            { data: 'created_at' },
            { data: 'charity' },
            { data: 'donor' },
            { data: 'cheque_no' },
            { data: 'note' },
            { data: 'amount' },
            { data: 'status' },
            { data: 'action', orderable: false, searchable: false } 
        ],

        buttons: [
            {
                extend: 'copy',
                exportOptions: { columns: ':not(:first-child)' } 
            },
            {
                extend: 'csv',
                title: "Pending Voucher Report",
                exportOptions: { columns: ':not(:first-child)' }
            },
            {
                extend: 'excel',
                title: "Pending Voucher Report",
                exportOptions: { columns: ':not(:first-child)' }
            },
            {
                extend: 'pdfHtml5',
                title: "Pending Voucher Report",
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: { columns: ':not(:first-child)' },
                customize: function(doc) {

                    // Style
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 8,
                        fillColor: '#4d617e',
                        color: 'white',
                        alignment: 'center'
                    };
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 40, 20, 30];

                    // Fix column width cropping issue
                    for (var i = 0; i < doc.content.length; i++) {
                        if (doc.content[i].table) {
                            doc.content[i].table.widths = [
                                '12%', // created_at
                                '16%', // charity
                                '19%', // donor
                                '15%', // cheque no
                                '10%', // note
                                '20%', // amount
                                '8%',  // status
                            ];
                            break;
                        }
                    }
                }
            },

            {
                extend: 'print',
                title: "<h3 style='text-align:center;'>Pending Voucher Report</h3>",
                exportOptions: { columns: ':not(:first-child)' }
            }
        ]
    });
}


// Edit Button Click Handler
 $(document).on('click', '.editCharityBtn', function() {
    var voucherId = $(this).data('voucher_id');
    var currentCharity = $(this).data('current_charity');
    var currentDonor = $(this).data('current_donor'); // 👈 NEW
    var currentNote = $(this).data('current_note');
    var currentStatus = $(this).data('current_status');
    
    $('#editing_voucher_id').val(voucherId);
    $('#new_charity_id').val(currentCharity).trigger('change');
    $('#new_donor_id').val(currentDonor).trigger('change'); // 👈 NEW
    $('#new_note').val(currentNote);
    $('#new_voucher_status').val(currentStatus);
    
    $('#editCharityModal').modal('show');
});

// Save Button Click Handler
 $("#saveNewCharityBtn").click(function() {
    $("#loading").show();
    var voucherId = $('#editing_voucher_id').val();
    var newCharityId = $('#new_charity_id').val();
    var newDonorId = $('#new_donor_id').val(); // 👈 NEW
    var newNote = $('#new_note').val();
    var newStatus = $('#new_voucher_status').val();
    
    $.ajax({
        url: "{{ route('voucher.updateDetails') }}",
        method: "POST",
        data: { 
            voucher_id: voucherId, 
            charity_id: newCharityId, 
            donor_id: newDonorId, // 👈 NEW
            note: newNote, 
            voucher_status: newStatus 
        },
        success: function (d) {
            $("#loading").hide();
            if (d.status == 300) {
                $("#editCharityModal").modal('hide');
                $('#pendingTable').DataTable().ajax.reload(null, false); 
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


</script>

@endsection
