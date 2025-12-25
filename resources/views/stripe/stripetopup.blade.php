@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Stripe Topup List</div>
            </div>
            <div class="ermsg"></div>
        </section>

        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->

         <section class="px-4"  id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white" id="stripeTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor Account</th>
                                    <th>Amount</th>
                                    <th>Top-Up</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Empty: Populated by Datatables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')



<script type="text/javascript">

$(document).ready(function() {
    $.ajaxSetup({ 
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } 
    });

    // Initialize DataTable
    var table = $('#stripeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('stripetopup') }}", // Ensure this matches your route
        pageLength: 100,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        // 🔥 enable button layout (same as previous)
        dom: '<"html5buttons"B>lTfgitp',

        buttons: [
            {
                extend: 'copy',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'csv',
                title: "Complete Voucher Report",
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                title: "Complete Voucher Report",
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdfHtml5',
                title: "Complete Voucher Report",
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: { columns: ':visible' },
                customize: function(doc) {
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 10,
                        fillColor: '#4d617e',
                        color: 'white',
                        alignment: 'center'
                    };
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 40, 20, 30];
                }
            },
            {
                extend: 'print',
                title: "<h3 style='text-align:center;'>Complete Voucher Report</h3>",
                exportOptions: { columns: ':visible' }
            }
        ],
        columns: [
            {data: 'date', name: 'created_at'},
            {data: 'donor_account', name: 'donor.accountno'},
            {data: 'amount_formatted', name: 'amount'},
            {data: 'topup_link', name: 'topup_link', orderable: false, searchable: false},
            {data: 'status_dropdown', name: 'status_dropdown', orderable: false, searchable: false},
        ]
    });

    var statusUrl = "{{URL::to('/admin/stripe-topup-status')}}";

    // Use delegation because rows are generated dynamically
    $(document).on('change', '.status-change', function() {
        var id = $(this).data('id'); // Get ID from data attribute
        var val = $(this).val();
        
        $("#loading").show();

        $.ajax({
            url: statusUrl,
            method: "POST",
            data: { id: id, status: val }, // Ensure your backend receives the correct ID
            success: function (d) {
                if(d.status == 300){
                    $(".ermsg").html(d.message);
                    table.ajax.reload(null, false); // Reload table without resetting pagination
                }
            },
            complete: function(){
                $("#loading").hide();
            },
            error: function (d) {
                console.log(d);
            }
        });
    });
});
</script>
@endsection
