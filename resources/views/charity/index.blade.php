@extends('layouts.admin')
@section('content')

<style>
    a {
        text-decoration: none;
    }
</style>
<div class="rightSection">
    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Charity List </div>
            </div>
        </section>
        <section class="profile purchase-status">
            <div class="title-section">
                <button id="newBtn" type="button" class="btn btn-info">Add New</button>
            </div>
        </section>
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

        <section class="px-4"  id="addThisFormContainer">
            <div class="row justify-content-md-center my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('charity.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Name</label>
                               <input type="text" name="name" id="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror">
                         </div>
                         <div class="col my-3">
                             <label for="">Address</label>
                            <input type="text" name="address" id="address" placeholder="Address" class="form-control @error('address') is-invalid @enderror">
                         </div>
                        <div class="col my-3">
                             <label for="">Town</label>
                            <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror">
                         </div>
                        <div class="col my-3">
                             <label for="">Post Code</label>
                            <input type="text" name="post_code" id="post_code" placeholder="" class="form-control @error('address') is-invalid @enderror">
                         </div>

                         
                        <div class="col my-3">
                            <label for="">Account name</label>
                           <input type="text" name="account_name" id="account_name" placeholder="" class="form-control @error('account_name') is-invalid @enderror">
                        </div>

                        
                        <div class="col my-3">
                            <label for="">Account number</label>
                           <input type="text" name="account_number" id="account_number" placeholder="" class="form-control @error('account_number') is-invalid @enderror">
                        </div>
    
                    </div>

                    <div class="col-md-6  my-4  bg-white">


                        <div class="col my-3">
                            <label for="">Email</label>
                           <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror">
                        </div>
                        <div class="col my-3">
                            <label for="">Phone</label>
                           <input type="text" name="number" id="number" placeholder="Phone" class="form-control @error('number') is-invalid @enderror">
                        </div>

                        <div class="col my-3">
                            <label for="">Charity Number</label>
                           <input type="text" name="acc" id="acc" placeholder="account no" class="form-control @error('acc') is-invalid @enderror">
                        </div>
                        <div class="col my-3">
                            <label for="">Balance</label>
                           <input type="text" name="balance" id="balance" placeholder="balance" class="form-control">
                        </div>

                        
                        <div class="col my-3">
                            <label for="">Account sortcode</label>
                           <input type="text" name="account_sortcode" id="account_sortcode" placeholder="" class="form-control @error('account_sortcode') is-invalid @enderror">
                        </div>

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Create</button>
                        <a class="btn btn-warning mt-2 text-white" id="FormCloseBtn">close</a>
                    </div>
                    </form>
            </div>
        </section>


        <section id="contentContainer">
            <div class="row my-3">
                <div class="stsermsg"></div>

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white" id="charityTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Town</th>
                                    <th>Post Code</th>
                                    <th>Charity Number</th>
                                    <th>Balance</th>
                                    <th>Pending</th>
                                    <th>Status</th>
                                    <th>Bank</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<div class="modal fade" id="bankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Bank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="bankModalBody" style="text-align:center;">
            </div>

            <div class="modal-footer">
                <a href="#" id="openInTab" target="_blank" class="btn btn-primary">Open in new tab</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>




@endsection
@section('script')
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





    });

</script>

<script>
    $(document).ready(function () {

    // Campaign Status
    $(document).on('change','.campaignstatus',function(){
        var url = "{{URL::to('/admin/active-charity')}}";
        var status = $(this).prop('checked') ? 1 : 0;
        var id = $(this).data('id');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: url,
            data: {'status': status, 'id': id},
            success: function(d){
                $(".stsermsg").html(d.message);
            }
        });
    });

    // Delete row
    $(document).on('click','.deleteBtn', function () {
        if (!confirm('Sure?')) return;
        var id = $(this).attr('rid');

        $.ajax({
            url: "/admin/add-charity/delete/" + id,
            type: "GET",
            success: function(res){
                $('#charityTable').DataTable().ajax.reload();
            }
        });
    });

});

$(document).on('click', '.openBankModal', function(e) {
    e.preventDefault();

    let file = $(this).data('file');
    let fileUrl = "/images/" + file;
    let ext = file.split('.').pop().toLowerCase();
    let html = "";

    if (['jpg','jpeg','png','gif','bmp','webp'].includes(ext)) {
        html = `<img src="${fileUrl}" style="width:100%;border-radius:8px;">`;
    }
    else if (ext === 'pdf') {
        html = `<iframe src="${fileUrl}" width="100%" height="600px" style="border:none;"></iframe>`;
    }
    else {
        html = `<p>Unsupported file format. 
                   <a href="${fileUrl}" target="_blank">Click here to download</a>
                </p>`;
    }

    $("#bankModalBody").html(html);
    $("#openInTab").attr("href", fileUrl);
    $("#bankModal").modal("show");
});


</script>

<script>
$(document).ready(function() {
    var data = 'Tevini';
    var title = 'Charity report';

    // destroy existing instance if any (avoids reinitialise error)
    if ($.fn.DataTable.isDataTable('#charityTable')) {
        $('#charityTable').DataTable().clear().destroy();
    }

    $('#charityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('charity.data') }}",
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [{ type: 'date', targets: [1] }], 
        order: [[1, 'desc']], 
        dom: '<"html5buttons"B>lTfgitp',
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
                exportOptions: { columns: ':not(:nth-last-child(-n+3))' },
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
                        var widths = ['15%', '16%', '10%', '17%', '10%', '8%', '8%', '8%', '8%'];
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
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'number', name: 'number' },
            { data: 'address', name: 'address' },
            { data: 'town', name: 'town' },
            { data: 'post_code', name: 'post_code' },
            { data: 'acc_no', name: 'acc_no' },
            { data: 'balance', name: 'balance', orderable:false, searchable:false },
            { data: 'pending', name: 'pending', orderable:false, searchable:false },
            { data: 'status', name: 'status', orderable:false, searchable:false },
            { data: 'bank', name: 'bank', orderable:false, searchable:false },
            { data: 'action', name: 'action', orderable:false, searchable:false },
        ]
    });

});
</script>
@endsection
