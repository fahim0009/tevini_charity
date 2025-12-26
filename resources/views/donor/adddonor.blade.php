@extends('layouts.admin')

@section('content')

<style>
    #addThisFormContainer .card {
        transition: all 0.3s ease;
    }
    
    #addThisFormContainer .form-control, 
    #addThisFormContainer .form-select,
    #addThisFormContainer .input-group-text {
        border-color: #e9ecef;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
    }

    #addThisFormContainer .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }

    #addThisFormContainer .btn-primary {
        background-color: #0d6efd;
        border: none;
        padding: 0.7rem 2rem;
        border-radius: 8px;
    }

    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #f0f0f0;
        }
    }
</style>

<div class="rightSection">
    <div class="dashboard-content">
        <!-- Title Section -->
        <section class="profile purchase-status">
            <div class="title-section d-flex align-items-center">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Donor List</div>
                <button id="newBtn" type="button" class="btn btn-info ms-auto">Add New</button>
            </div>
        </section>

        <!-- Success/Error Messages -->
        @if(session('message'))
            <section class="px-4">
                <div class="row my-3">
                    <div class="alert alert-success" id="successMessage">{{ session('message') }}</div>
                </div>
            </section>
        @endif
        @if(session('error'))
            <section class="px-4">
                <div class="row my-3">
                    <div class="alert alert-danger" id="errMessage">{{ session('error') }}</div>
                </div>
            </section>
        @endif

        <!-- Add Donor Form -->
        <section class="px-4" id="addThisFormContainer" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm rounded-4 mt-4">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="fw-bold text-dark mb-0">Register New Donor</h5>
                            <p class="text-muted small">Fill in the information below to create a new donor account.</p>
                        </div>
                        
                        <div class="card-body p-4">
                            <form action="{{ route('donor.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                                @csrf
                                <input type="hidden" name="donorid" id="donorid">

                                <div class="row g-4">
                                    <div class="col-md-6 border-end-md">
                                        <h6 class="text-primary fw-bold mb-3">Identity Details</h6>
                                        
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Prefix</label>
                                                <select name="prefix_name" id="prefix_name" class="form-select border-radius-8">
                                                    <option value="">Select</option>
                                                    <option value="Mr">Mr</option>
                                                    <option value="Mrs">Mrs</option>
                                                    <option value="Ms">Ms</option>
                                                    <option value="Dr">Dr</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="title" class="form-label small fw-bold">Title/Position</label>
                                                <input type="text" name="title" id="title" placeholder="e.g. Manager" class="form-control @error('title') is-invalid @enderror">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fname" class="form-label small fw-bold">First Name</label>
                                            <input type="text" name="fname" id="fname" placeholder="Enter first name" class="form-control @error('fname') is-invalid @enderror">
                                        </div>

                                        <div class="mb-3">
                                            <label for="surname" class="form-label small fw-bold">Surname</label>
                                            <input type="text" name="surname" id="surname" placeholder="Enter surname" class="form-control @error('surname') is-invalid @enderror">
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label small fw-bold">Email Address</label>
                                            <input type="email" name="email" id="email" placeholder="email@example.com" class="form-control @error('email') is-invalid @enderror">
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label small fw-bold">Phone Number</label>
                                            <input type="text" name="phone" id="phone" placeholder="+123456789" class="form-control @error('phone') is-invalid @enderror">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-primary fw-bold mb-3">Address & Account</h6>

                                        <div class="row g-2 mb-3">
                                            <div class="col-md-8">
                                                <label for="street" class="form-label small fw-bold">Street</label>
                                                <input type="text" name="street" id="street" placeholder="Street name" class="form-control @error('street') is-invalid @enderror">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="houseno" class="form-label small fw-bold">House No</label>
                                                <input type="text" name="houseno" id="houseno" placeholder="No." class="form-control @error('houseno') is-invalid @enderror">
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label for="town" class="form-label small fw-bold">Town/City</label>
                                                <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="postcode" class="form-label small fw-bold">Postcode</label>
                                                <input type="text" name="postcode" id="postcode" placeholder="Postcode" class="form-control @error('postcode') is-invalid @enderror">
                                            </div>
                                        </div>

                                        <hr class="my-4 opacity-50">

                                        <div class="mb-3">
                                            <label for="accno" class="form-label small fw-bold">Account Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="iconify" data-icon="fluent:badge-24-regular"></i></span>
                                                <input type="text" name="accno" id="accno" placeholder="ACC-0000" class="form-control">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="balance" class="form-label small fw-bold">Opening Balance</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted">£</span>
                                                <input type="text" name="balance" id="balance" placeholder="0.00" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4 pt-3 border-top">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light px-4 fw-semibold" id="FormCloseBtn">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                                            <span class="iconify me-1" data-icon="fluent:add-circle-24-regular"></span> Create Donor
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Donor List Table -->
        <section class="px-4" id="contentContainer">
            <div class="row my-3">
                <div class="col-md-12 my-2 d-flex gap-3 align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="checkAll" value="all">
                        <label class="form-check-label" for="checkAll">All Select</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" for="fromdate">Date From</label>
                        <input type="date" id="fromdate" name="fromdate" class="form-control">
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" for="todate">Date To</label>
                        <input type="date" id="todate" name="todate" class="form-control">
                    </div>
                    <button class="btn btn-primary mt-3" id="sentRpt" type="button">Send Mail</button>
                    <a href="{{ route('admin.donor.email') }}" class="btn btn-success mt-3">Custom Mail</a>
                </div>

                <!-- Loader -->
                <div id="loading" style="display: none;">
                    <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." loading="lazy" />
                </div>

                <!-- Error Message Container -->
                <div class="ermsg"></div>

                <!-- Table -->
                <div class="col-md-12 mt-2">
                    <div class="overflow-auto">
                        <table class="table table-donor shadow-sm bg-white" id="donorexample">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Town</th>
                                    <th>Account</th>
                                    <th>Balance</th>
                                    <th>Overdrawn</th>
                                    <th>Pending</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                    <!-- Pagination Links -->
                    <div class="mt-3">
                        {{-- {{ $users->links() }} --}}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Overdrawn Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2">Update Overdrawn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsgod"></div>
                <div class="mb-3">
                    <label for="overdrawnno" class="form-label">Overdrawn Amount</label>
                    <input type="text" class="form-control" id="overdrawnno">
                    <input type="hidden" class="form-control" value="" id="overdrawnid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="overdrawnBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Donor Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <div class="mb-3">
                    <label for="updaccno" class="form-label">Account</label>
                    <input type="text" class="form-control" id="updaccno">
                    <input type="hidden" class="form-control" value="" id="donnerid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="addaccBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    // CSRF Token Setup
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Hide form by default
    $("#addThisFormContainer").hide();

    // Toggle form visibility
    $("#newBtn").click(function() {
        $("#createThisForm")[0].reset();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn").click(function() {
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        $("#createThisForm")[0].reset();
    });

    // Auto-hide messages
    setTimeout(function() {
        $('#successMessage, #errMessage').fadeOut('fast');
    }, 3000);

    // Check all checkboxes
    $("#checkAll").click(function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    // Uncheck "All Select" when individual checkbox is clicked
    $("#contentContainer").on('click', '.getDid', function() {
        $("#checkAll").prop('checked', false);
    });

    // Delete donor
    $("#contentContainer").on('click', '#deleteBtn', function() {
        if (!confirm('Are you sure?')) return;
        const donorId = $(this).attr('rid');
        $.ajax({
            url: "{{ URL::to('/admin/donor/delete') }}",
            method: "POST",
            data: { donorId },
            success: function(d) {
                $(".ermsg").html(d.message);
                if (d.status == 300) {
                    location.reload();
                }
            },
            error: function() {
                $(".ermsg").html("An error occurred. Please try again.");
            }
        });
    });

    // Add account to donor
    $("#contentContainer").on('click', '.acc', function() {
        $('#donnerid').val($(this).attr("user-id"));
    });

    $("#addaccBtn").click(function() {
        const donnerId = $("#donnerid").val();
        const accno = $("#updaccno").val();
        $.ajax({
            url: "{{ URL::to('/admin/add-account') }}",
            method: "POST",
            data: { donnerId, accno },
            success: function(d) {
                $(".ermsg").html(d.message);
                if (d.status == 300) {
                    location.reload();
                }
            },
            error: function() {
                $(".ermsg").html("An error occurred. Please try again.");
            }
        });
    });

    // Update overdrawn amount
    $("#contentContainer").on('click', '.overdrawn', function() {
        $('#overdrawnid').val($(this).attr("overdrawn-id"));
    });

    $("#overdrawnBtn").click(function() {
        const overdrawnid = $("#overdrawnid").val();
        const overdrawnno = $("#overdrawnno").val();
        $.ajax({
            url: "{{ URL::to('/admin/update-overdrawn') }}",
            method: "POST",
            data: { overdrawnid, overdrawnno },
            success: function(d) {
                $(".ermsgod").html(d.message);
                if (d.status == 300) {
                    location.reload();
                }
            },
            error: function() {
                $(".ermsgod").html("An error occurred. Please try again.");
            }
        });
    });

    // Send report to donors
    $("#sentRpt").click(function() {
        $("#loading").show();
        const donorIds = $('.getDid:checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        const fromdate = $("#fromdate").val();
        const todate = $("#todate").val();
        const checkAll = $("#checkAll").prop('checked') ? "all" : "";

        $.ajax({
            url: "{{ URL::to('/admin/reportall') }}",
            method: "POST",
            data: { donorIds, fromdate, todate, checkAll },
            success: function(d) {
                $(".ermsg").html(d.message);
                $('html, body').animate({ scrollTop: 0 }, 'fast');
            },
            complete: function() {
                $("#loading").hide();
            },
            error: function() {
                $(".ermsg").html("An error occurred. Please try again.");
            }
        });
    });

    
});
</script>

<script>
$(document).ready(function() {
    var data = 'Tevini';
    var title = 'Donors report';

    // destroy existing instance if any (avoids reinitialise error)
    if ($.fn.DataTable.isDataTable('#donorexample')) {
        $('#donorexample').DataTable().clear().destroy();
    }

    $('#donorexample').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('donors.data') }}",
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [{ type: 'date', targets: [1] }], 
        order: [[1, 'desc']], 
        dom: '<"html5buttons"B>lTfgitp',

        // Buttons config - mirrors your previous pattern
        buttons: [
            { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },

            {
                extend: 'excel',
                title: title,
                messageTop: data,
                exportOptions: { columns: ':not(:last-child)' }
            },

            {
                extend: 'pdfHtml5',
                title: 'Report',
                orientation: 'portrait',
                pageSize: 'A4',
                header: true,
                exportOptions: { columns: ':not(:last-child)' },
                customize: function (doc) {
                    // Custom title (unchanged)
                    doc.content.splice(0, 1, {
                        text: [
                            { text: data + '\n', bold: true, fontSize: 12 },
                            { text: title + '\n', bold: true, fontSize: 15 }
                        ],
                        margin: [0, 0, 0, 12],
                        alignment: 'center'
                    });

                    // Center the document default style (unchanged)
                    doc.defaultStyle.alignment = 'center';

                    // Find the table node
                    var tableNode = null;
                    for (var i = 0; i < doc.content.length; i++) {
                        if (doc.content[i] && doc.content[i].table) {
                            tableNode = doc.content[i];
                            break;
                        }
                    }

                    if (tableNode) {
                        var widths = ['2%', '8%', '10%', '15%', '15%', '10%', '10%', '10%', '10%', '10%'];
                        tableNode.table.widths = widths;

                        tableNode.layout = {
                            hLineWidth: function (i, node) {
                                return 0.5; 
                            },
                            vLineWidth: function (i, node) {
                                return 0; 
                            },
                            hLineColor: function (i, node) {
                                return '#aaa';
                            },
                            vLineColor: function (i, node) {
                                return '#aaa';
                            },
                            paddingLeft: function (i, node) { return 2; },
                            paddingRight: function (i, node) { return 2; },
                            paddingTop: function (i, node) { return 4; },
                            paddingBottom: function (i, node) { return 4; }
                        };

                        
                        if (!doc.styles) doc.styles = {};
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 8,
                            color: 'white', 
                            fillColor: '#4d617e', 
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        };
                        doc.styles.tableBodyEven = {
                            fontSize: 7,
                            color: '#4d617e', 
                            fillColor: '#f8f9fa', 
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        };
                        doc.styles.tableBodyOdd = {
                            fontSize: 7,
                            color: '#4d617e', 
                            fillColor: 'white', 
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        };

                        doc.pageMargins = [20, 60, 20, 40]; // [left, top, right, bottom]
                    }
                }
            },


            {
                extend: 'print',
                title: "<p style='text-align:center;'>" + data + "<br>" + title + "</p>",
                header: true,
                exportOptions: { columns: ':not(:last-child)' },
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],

        // columns must match what your server returns
        columns: [
            { data: 'id', render: function(id){
                return `<input type="checkbox" class="form-check-input getDid" value="${id}">`;
            }},
            { data: 'id', name: 'id' },   // example: date column (index 1)
            { data: 'fullname', name: 'fullname' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'town', name: 'town' },
            { data: 'accountno', name: 'accountno' },
            { data: 'balance', name: 'balance' },
            { data: 'overdrawn_amount', name: 'overdrawn_amount' },
            { data: 'pending', name: 'pending' },
            { data: 'action', orderable: false, searchable: false } // last column - excluded from export
        ]
    });

});
</script>



@endsection