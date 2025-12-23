@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Standing Order</div>
            </div>
        </section>

        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
                <div class="stsermsg"></div>
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">

                        <ul class="nav nav-tabs mb-3" id="donationTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-status="1" type="button">Active Orders</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-status="0" type="button">Inactive Orders</button>
                            </li>
                        </ul>

                        <div class="table-responsive">
                            <table class="table table-custom shadow-sm bg-white" id="standingTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Donor</th>
                                        <th>Beneficiary</th>
                                        <th>Amount</th>
                                        <th>Anonymous</th>
                                        <th>Starting</th>
                                        <th>Interval</th>
                                        <th>Payments</th>
                                        <th>Charity Note</th>
                                        <th>Note</th>
                                        <th>View</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>


                        {{-- <div class="table-responsive">
                            <table class="table table-custom shadow-sm bg-white" id="standingTable">
                                
                            </table>
                        </div> --}}
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')


<script>
    $(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Store active status in a variable
    let currentStatus = 1;

    var table = $('#standingTable').DataTable({
        processing: true,
        serverSide: true,
        dom: '<"html5buttons"B>lTfgitp',
        ajax: {
            url: "{{ route('donationstanding') }}",
            data: function (d) {
                d.status = currentStatus; // Pass status to the controller
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        // ... keep all your other columns and buttons config exactly as they are ...
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'donor', name: 'donor' },
            { data: 'beneficiary', name: 'beneficiary' },
            { data: 'amount', name: 'amount' },
            { data: 'anonymous', name: 'anonymous' },
            { data: 'starting', name: 'starting' },
            { data: 'interval', name: 'interval' },
            { data: 'payments', name: 'payments' },
            { data: 'charitynote', name: 'charitynote' },
            { data: 'mynote', name: 'mynote' },
            { data: 'view', name: 'view', orderable:false, searchable:false },
            { data: 'status_switch', name: 'status_switch', orderable:false, searchable:false }
        ],
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))' 
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: "Standing Donation Report",
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 9,
                            fillColor: '#4d617e',
                            color: 'white',
                            alignment: 'center'
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.defaultStyle.alignment = 'center';
                        doc.pageMargins = [20, 40, 20, 30];
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    }
                }
            ]
    });

    // Handle Tab Clicks
    $('#donationTabs .nav-link').on('click', function() {
        $('#donationTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        
        currentStatus = $(this).data('status');
        table.draw(); // Redraw table with new status
    });

    // Handle Status Switch (Toggle)
    $(document).on('change', '.standingdnstatus', function () {
        var url = "{{URL::to('/admin/active-standingdonation')}}";
        var status = $(this).prop('checked') ? 1 : 0;
        var id = $(this).data('id');

        $.post(url, {status:status, id:id}, function(d){
            console.log(d);
            $('.stsermsg').html('<div class="alert alert-success">'+d.message+'</div>');
            table.draw(); // Refresh table so the item moves to the other tab
        });
    });
});
</script>


<script>
 $(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $('#standingTablewdw').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"html5buttons"B>lTfgitp',
            ajax: "{{ route('donationstanding') }}",

            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'donor', name: 'donor' },
                { data: 'beneficiary', name: 'beneficiary' },
                { data: 'amount', name: 'amount' },
                { data: 'anonymous', name: 'anonymous' },
                { data: 'starting', name: 'starting' },
                { data: 'interval', name: 'interval' },
                { data: 'payments', name: 'payments' },
                { data: 'charitynote', name: 'charitynote' },
                { data: 'mynote', name: 'mynote' },
                { data: 'view', name: 'view', orderable:false, searchable:false },
                { data: 'status_switch', name: 'status_switch', orderable:false, searchable:false }
            ],
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))' 
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: "Standing Donation Report",
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 9,
                            fillColor: '#4d617e',
                            color: 'white',
                            alignment: 'center'
                        };
                        doc.defaultStyle.fontSize = 8;
                        doc.defaultStyle.alignment = 'center';
                        doc.pageMargins = [20, 40, 20, 30];
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(:last-child):not(:nth-last-child(2))'
                    }
                }
            ]
        });


        // ON STATUS CHANGE
        $(document).on('change', '.standingdnstatus', function () {
            var url = "{{URL::to('/admin/active-standingdonation')}}";
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');

            $.post(url, {status:status, id:id}, function(d){
                $('.stsermsg').html(d.message);
            });
        });

});
</script>

@endsection
