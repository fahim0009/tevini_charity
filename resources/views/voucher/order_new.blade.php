@extends('layouts.admin')

@section('content')

<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
             <div class="mx-2">
                 {{ (request()->is('admin/order/new')) ? 'New Order' : '' }}
                 {{ (request()->is('admin/order/complete')) ? 'Complete Order' : '' }}
                 {{ (request()->is('admin/order/cancel')) ? 'Cancel Order' : '' }}
                </div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline">
                                    <div class="row">
                                        <div class="col-md-5 d-flex align-items-center">
                                            <div class="form-group d-flex mt-4">
                                                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                                                <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                              </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for=""><small>Date From </small> </label>
                                                <input class="form-control mr-sm-2" type="date" placeholder="Search" aria-label="Search">
                                              </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for=""><small>Date To </small> </label>
                                                <input class="form-control mr-sm-2" type="date" placeholder="Search" aria-label="Search">
                                              </div>
                                        </div>
                                    </div>
                                  </form>
                            </div>
                            <div class="col-md-3 d-flex align-items-center justify-content-center">
                                <div>
                                    {{-- <button title="Download" class="my-2 btn btn-sm btn-info text-white">Download PDF</button>
                                    <button title="Download" class="my-2 btn btn-sm btn-secondary">Download excel</button> --}}
                                </div>
                            </div>
                           </div>
                        </div>
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="completeTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Order Id</th>
                                            <th>Donor Name</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Barcode</th>
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
@endsection

@section('script')

<script>
$(document).ready(function() {

    if ($.fn.DataTable.isDataTable('#completeTable')) {
        $('#completeTable').DataTable().clear().destroy();
    }

    $('#completeTable').DataTable({
        processing: true,
        serverSide: true,

        // 🔥 Required for buttons
        dom: '<"html5buttons"B>lTfgitp',

        ajax: {
            url: "{{ route('neworder') }}",
            type: "GET",
        },

        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'order_id', name: 'order_id' },
            { data: 'donor', name: 'donor' },
            { data: 'amount', name: 'amount' },
            { data: 'status_text', name: 'status_text' },
            { data: 'barcode', name: 'barcode', orderable: false, searchable: false }, // ❌ don't export
            { data: 'action', name: 'action', orderable: false, searchable: false }     // ❌ don't export
        ],

        pageLength: 50,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],

        buttons: [
            {
                extend: 'copy',
                exportOptions: { columns: [0,1,2,3,4] }
            },
            {
                extend: 'csv',
                title: "New Order Report",
                exportOptions: { columns: [0,1,2,3,4] }
            },
            {
                extend: 'excel',
                title: "New Order Report",
                exportOptions: { columns: [0,1,2,3,4] }
            },
            {
                extend: 'pdfHtml5',
                title: "New Order Report",
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: { columns: [0,1,2,3,4] },
                customize: function(doc) {

                    // Header style
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 9,
                        fillColor: '#4d617e',
                        color: 'white',
                        alignment: 'center'
                    };

                    // Center all text
                    doc.defaultStyle.alignment = 'center';

                    // Page margins
                    doc.pageMargins = [20, 40, 20, 30];

                    // Fix table width
                    for (var i = 0; i < doc.content.length; i++) {
                        if (doc.content[i].table) {
                            doc.content[i].table.widths = [
                                '18%', // created_at
                                '22%', // order_id
                                '25%', // donor
                                '15%', // amount
                                '20%'  // status
                            ];
                            break;
                        }
                    }
                }
            },
            {
                extend: 'print',
                title: "<h3 style='text-align:center;'>New Order Report</h3>",
                exportOptions: { columns: [0,1,2,3,4] }
            }
        ]
    });

});
</script>

    
@endsection
