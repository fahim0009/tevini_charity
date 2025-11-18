@extends('layouts.admin')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<style>
    /* Main Theme Color */
:root {
    --primary-color: #4d617e;
    --primary-hover: #3a4b63;
}

/* Button Group Styling */
div.dt-buttons {
    gap: 8px !important;
}

.btn-export {
    background-color: var(--primary-color) !important;
    border: none !important;
    color: white !important;
    border-radius: 6px !important;
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 4px rgba(77, 97, 126, 0.2) !important;
}

.btn-export:hover {
    background-color: var(--primary-hover) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(77, 97, 126, 0.3) !important;
}

.btn-export i {
    margin-right: 6px;
}

/* Table Styling */
#campaignTable {
    font-size: 0.94rem;
}

#campaignTable thead th {
    background-color: var(--primary-color) !important;
    color: white !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    border: none !important;
    padding: 12px 8px !important;
}

#campaignTable tbody tr {
    transition: all 0.2s ease;
}

#campaignTable tbody tr:hover {
    background-color: #f1f5f9 !important;
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

#campaignTable tbody td {
    padding: 12px 8px !important;
    vertical-align: middle;
    border-bottom: 1px solid #eef2f7;
}

/* Striped rows with subtle colors */
#campaignTable tbody tr:nth-child(even) {
    background-color: #fafbfc;
}

/* Search & Length Menu */
.dataTables_filter input,
.dataTables_length select {
    border-radius: 6px !important;
    border: 1px solid #d1dbe4 !important;
}

.dataTables_filter input:focus,
.dataTables_length select:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 0.2rem rgba(77, 97, 126, 0.15) !important;
}

/* Pagination */
.dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    margin: 0 3px !important;
    border: 1px solid #e2e8f0 !important;
}

.dataTables_paginate .paginate_button.current {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
}

.dataTables_info {
    font-weight: 500;
    color: #64748b;
}
</style>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Campaign List </div>
            </div>
        </section>

        @if (!$errors->any()) 
        
        <section class="profile purchase-status">
            <div class="title-section">
                <button id="newBtn" type="button" class="btn btn-info">Add New</button>
            </div>
        </section>
        
        @endif 

        @if(session()->has('message'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('message') }}</div>
            </div>
        </section>
        @endif
        @if(session()->has('error'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
            </div>
        </section>
        @endif


        <section class="px-4" @if (!$errors->any()) id="addThisFormContainer" @endif  >
            <div class="card row my-3">

                <div class="row justify-content-center">
                    @if ($errors->any())
                    <div class="col-md-4 my-4 bg-white mx-auto">
                        <div class="alert alert-danger text-center">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                        
                    @endif
                </div>

                <div class="row justify-content-center">
                    <form action="{{ route('campaign.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                        @csrf
                        <div class="col-md-4 my-4 bg-white mx-auto">
    
                            <div class="col my-3">
                                    <label for="">Charity</label>
                                    <select name="charity_id" id="charity_id" class="form-control @error('charity_id') is-invalid @enderror select2">
                                    <option value="">Please Select</option>
                                    @foreach (\App\Models\Charity::orderby('id','DESC')->get() as $charity)
                                    <option value="{{$charity->id}}" {{ old('charity_id') == $charity->id ? 'selected' : '' }}>{{$charity->name}}</option>
                                    @endforeach
                                    </select>
                             </div>
                             <div class="col my-3">
                                <label for="">Title</label>
                               <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                            </div>

                            
                            <div class="col my-3">
                                <label for="start_date">Start Date</label>
                               <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                            </div>

                            
                            <div class="col my-3">
                                <label for="">End Date</label>
                               <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                            </div>
    
                            <div class="col my-3">
                                <button class="btn btn-theme mt-2 text-white">Create</button>
                                <a class="btn btn-warning mt-2 text-white" id="FormCloseBtn">close</a>
                            </div>
    
                        </div>
                    </form>
                </div>
            </div>
        </section>


        <section class="px-4"  id="contentContainer">
            <div class="card my-3">
                <div class="ermsg"></div>
                <div class="row  my-3 mx-0 ">

                    
                    <div class="col-md-12">
                        <form id="filterForm">
                            <div class="row justify-content-center">

                                <div class="col-md-4">
                                    <label><small>Campaign</small></label>
                                    <select name="campaign" id="campaign" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Campaign::orderBy('id','DESC')->get() as $item)
                                            <option value="{{$item->id}}">{{$item->campaign_title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label><small>Charity</small></label>
                                    <select name="charity" id="charity" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Charity::orderBy('id','DESC')->get() as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-center">
                                    <button class="text-white btn-theme mt-4" id="filterBtn" type="button">Search</button>
                                </div>

                            </div>
                        </form>

                    </div>



                    <div class="col-md-12 mt-2 text-center">
                        <div class="overflow">
                            <table class="table table-donor shadow-sm bg-white" id="campaignTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Charity</th>
                                        <th>Campaign Title</th>
                                        <th>Hash</th>
                                        <th>Return Url</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>



  <!-- Modal -->
  <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel2">Add or Updte Return URL</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsgod"></div>
            <div class="mb-3">
                <label for="campaignurl" class="form-label">URL</label>
                <input type="text" class="form-control" id="campaignurl">
                <input type="hidden" class="form-control" value="" id="campaignid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="campaignBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->


@endsection

@section('script')

<script>

$(document).ready(function () {

    var table = $('#campaignTable').DataTable({
        processing: true,
        serverSide: true,
        dom: "<'row'<'col-sm-12 col-md-6 d-flex align-items-center'B><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-export btn-sm',
                title: 'Campaign Report',
                messageTop: 'Detailed Campaign Data Export'
            },
            {
    extend: 'pdfHtml5',
    text: '<i class="fas fa-file-pdf"></i> PDF',
    className: 'btn-export btn-sm',
    title: 'Campaign Report',
    orientation: 'landscape',
    pageSize: 'A4',
    exportOptions: { 
        columns: ':not(:last-child)',
        // This modifier will help wrap long text in HTML before PDF conversion
        modifier: {
            page: 'current'
        }
    },
    customize: function (doc) {
        // === 1. Header Styling ===
        doc.defaultStyle.alignment = 'center';
        doc.defaultStyle.fontSize = 9;

        doc.content.splice(0, 1, {
            margin: [0, 0, 0, 20],
            alignment: 'center',
            text: [
                { text: 'Tevini\n', fontSize: 14, bold: true, color: '#4d617e' },
                { text: 'Campaign Report', fontSize: 18, bold: true, color: '#2c3e50' }
            ]
        });

        // === 2. Find the table ===
        let tableNode = doc.content.find(node => node.table);
        if (!tableNode) return;

        // === 3. Set proper column widths (adjusted for better balance) ===
                        tableNode.table.widths = ['5%', '10%', '15%', '40%', '20%', '5%', '5%'];

        // === 4. CRITICAL: Force long text (especially hash) to wrap using zero-width spaces ===
        // This scans every cell in the table body and breaks long strings
        tableNode.table.body.forEach((row, rowIndex) => {
            if (rowIndex === 0) return; // Skip header row

            row.forEach((cell, colIndex) => {
                if (cell && typeof cell === 'object' && cell.text && typeof cell.text === 'string') {
                    let text = cell.text;

                    // Target column index 3 (0-based) = 4th column = Hash Code
                    if (colIndex === 3) {
                        // Insert zero-width space every 16 characters in hash-like strings
                        text = text.replace(/([a-f0-9]{16})(?=[a-f0-9])/gi, '$1\u200B');
                        // Also make font slightly smaller and allow wrapping
                        cell.fontSize = 7.5;
                        cell.lineHeight = 1.2;
                    }

                    // Optional: Also wrap very long URLs (column 4)
                    if (colIndex === 4 && text.length > 50) {
                        text = text.replace(/(.{40})(?=.)/g, '$1\u200B');
                        cell.fontSize = 7;
                    }

                    cell.text = text;
                }
            });
        });

        // === 5. Table styling & layout ===
        tableNode.layout = {
            hLineWidth: () => 0.5,
            vLineWidth: () => 0,
            hLineColor: () => '#ddd',
            paddingLeft: () => 6,
            paddingRight: () => 6,
            paddingTop: () => 8,
            paddingBottom: () => 8
        };

        // Header style
        doc.styles.tableHeader = {
            bold: true,
            fontSize: 9,
            color: 'white',
            fillColor: '#4d617e',
            alignment: 'center'
        };

        // Ensure all body cells allow wrapping
        doc.styles.tableBodyEven = { fontSize: 8, alignment: 'center' };
        doc.styles.tableBodyOdd = { fontSize: 8, alignment: 'center' };

        doc.pageMargins = [30, 70, 30, 50];
    }
},
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn-export btn-sm',
                title: 'Campaign Report'
            },
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn-export btn-sm'
            }
        ],
        ajax: {
            url: "{{ route('campaign.data') }}",
            data: function (d) {
                d.campaign = $('#campaign').val();
                d.charity = $('#charity').val();
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ campaigns",
            paginate: {
                next: '<i class="fas fa-chevron-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>'
            }
        },
        columns: [
            { data: 'id', width: "5%" },
            { data: 'charity', width: "12%" },
            { data: 'campaign_title', width: "23%" },
            { data: 'hash_code', width: "15%" },
            {
                data: 'return_url',
                width: "25%",
                render: function (data, type, row) {
                    return '<div class="d-flex align-items-center justify-content-between">' +
                        '<span class="text-truncate me-2" style="max-width: 180px;" title="' + data + '">' + data + '</span>' +
                        ' <a href="javascript:void(0)" campaign-id="' + row.id + '" class="url text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal2">' +
                        '<i class="fas fa-edit fa-fw"></i></a></div>';
                }
            },
            { data: 'start_date', width: "10%" },
            { data: 'end_date', width: "10%" },
            { data: 'actions', orderable: false, searchable: false, width: "8%" }
        ],
        drawCallback: function () {
            // Re-style length menu and search box on every draw
            $('.dataTables_length select').addClass('form-select form-select-sm shadow-sm');
            $('.dataTables_filter input').addClass('form-control form-control-sm shadow-sm').attr('placeholder', 'Search campaigns...');
        }
    });

    // Initialize styling on first load
    table.buttons().container().addClass('mb-3');

    $('#filterBtn').on('click', function () {
        table.draw();  // This forces DataTables to make a new AJAX request with current filter values
    });



});
</script>
<script>
    


    $(document).ready(function () {



        $("#addThisFormContainer").hide();
        $("#newBtn").click(function(){
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);

        });
        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        function clearform(){
                $('#createThisForm')[0].reset();
            }

            setTimeout(function() {
                $('#successMessage').fadeOut('fast');
                $('#errMessage').fadeOut('fast');
            }, 3000);

     //header for csrf-token is must in laravel
     $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

        var urlDlt = "{{URL::to('/admin/campaign/delete')}}";
        // Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Are you sure?')) return;
            campaignId = $(this).attr('rid');
            $.ajax({
            url: urlDlt,
            method: "POST",
            data: {campaignId:campaignId},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });
        });
        // Delete


        //add url
        $(document).on("click", ".url", function () {
            var campaignid = $(this).attr("campaign-id");
            $('#campaignid').val(campaignid);
        });


        var c_url = "{{URL::to('/admin/update-url')}}";
        $("#campaignBtn").click(function(){
        var campaignid= $("#campaignid").val();
        var campaignurl= $("#campaignurl").val();
        $.ajax({
            url: c_url,
            method: "POST",
            data: {campaignid,campaignurl},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsgod").html(d.message);
                }else if(d.status == 300){
                    $(".ermsgod").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

            });

        // overdrawn END






    });

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>

    $('.select2').select2({
      width: '100%',
      placeholder: "Select an Option",
      allowClear: true
    });
  </script>
@endsection
