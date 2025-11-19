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
@endsection

@section('script')
<script type="text/javascript">

$(document).ready(function() {

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

        columns: [
            { data: 'checkbox', orderable: false, searchable: false }, // ❌ do not export
            { data: 'created_at' },
            { data: 'charity' },
            { data: 'donor' },
            { data: 'cheque_no' },
            { data: 'note' },
            { data: 'amount' },
            { data: 'status' }
        ],

        buttons: [
            {
                extend: 'copy',
                exportOptions: { columns: ':not(:first-child)' } // remove checkbox column
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
</script>

@endsection
