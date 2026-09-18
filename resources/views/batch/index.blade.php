@extends('layouts.admin')

@section('content')

<link href="{{URL::to('/css/additional.css')}}" rel="stylesheet">

<div class="dashboard-content">
    <div class="container-fluid px-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0 fw-bold">Batch Transactions</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item active">Batches</li>
                </ol>
            </nav>
        </div>

        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('message') }}</div>
        @endif

        <div class="card card-table-wrapper bg-white">
            <div class="table-responsive">
                <table class="table table-donor mb-0" id="donorexample">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Charity Details</th>
                            <th>Batch Info</th>
                            <th>Total Amount</th>
                            <th class="text-end">Vouchers</th>
                            <th class="text-end">Upload PDF</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will inject rows here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ONE Global Modal instead of hundreds -->
<div class="modal fade" id="globalBatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="globalBatchModalTitle">Batch - Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="globalBatchModalBody">
                <div class="text-center p-5"><div class="spinner-border"></div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center">
                <button type="button" class="btn-close btn-close-white mb-2" data-bs-dismiss="modal"></button>
                <img src="" id="fullSizeImage" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
 $(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Initialize Yajra DataTables
        // Initialize Yajra DataTables
    var table = $('#donorexample').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.batches.data') }}",
        order: [[0, 'desc']], // Sorts index 0
        columns: [
            // Use name: 'created_at' so Yajra sorts by the correct DB column
            { data: 'date', name: 'created_at' }, 
            { data: 'charity_details', name: 'charity_details' },
            { data: 'batch_no', name: 'batch_no' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'vouchers_btn', name: 'vouchers_btn', orderable: false, searchable: false },
            { data: 'pdf_upload', name: 'pdf_upload', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
    // Handle "View Vouchers" click to load modal via AJAX
    $(document).on('click', '.btn-view-vouchers', function() {
        let batchId = $(this).data('id');
        console.log('Loading vouchers for batch ID:', batchId); // Debugging line
        $('#globalBatchModalTitle').text('Loading Vouchers...');
        $('#globalBatchModalBody').html('<div class="text-center p-5"><div class="spinner-border"></div></div>');
        
        $.ajax({
            url: `/admin/batch/${batchId}/vouchers`,
            success: function(html) {
                // Extract batch_no from the returned HTML or pass it via data attribute
                $('#globalBatchModalTitle').text('Batch Items');
                $('#globalBatchModalBody').html(html);
            },
            error: function() {
                $('#globalBatchModalBody').html('<div class="p-4 text-danger">Failed to load vouchers.</div>');
            }
        });
    });

    // Show selected filename for PDF (Delegate event for DataTables injected rows)
    $(document).on('change', '.pdf-input', function() {
        let fileName = this.files[0] ? this.files[0].name : "Select PDF";
        let batchId = $(this).data('id');
        $(`#pdf-label-${batchId} span`).text(fileName);
    });

    // Barcode Upload logic (Delegate event for DataTables injected rows)
    $(document).on('change', '.barcode-input', function() {
        let id = $(this).data('id');
        let file = this.files[0];
        let formData = new FormData();
        formData.append('barcode_image', file);
        formData.append('id', id);

        let $container = $(`#barcode-container-${id}`);
        $container.addClass('opacity-50');

        $.ajax({
            url: "{{ route('voucher.upload.barcode') }}", 
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.success) {
                    let imgHtml = `<img src="${response.image_url}" id="img-${id}" class="img-preview-thumb img-preview">`;
                    if($(`#img-${id}`).length > 0) {
                        $(`#img-${id}`).attr('src', response.image_url);
                    } else {
                        $(`#text-${id}`).replaceWith(imgHtml);
                    }
                }
            },
            complete: function() { $container.removeClass('opacity-50'); }
        });
    });

    // PDF Upload logic (Delegate event for DataTables injected rows)
    $(document).on('click', '.upload-pdf-btn', function() {
        let batchId = $(this).data('id');
        let batch_no = $(this).data('batch_no');
        let fileInput = $('#pdf-' + batchId)[0];
        let $btn = $(this);
        let $statusMsg = $('#status-' + batchId);

        if (fileInput.files.length === 0) return alert('Select file');

        let formData = new FormData();
        formData.append('pdf_file', fileInput.files[0]);
        formData.append('batch_id', batchId);
        formData.append('batch_no', batch_no);

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: "{{ route('batch.upload.pdf') }}", 
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $statusMsg.text('✓ Uploaded').removeClass('text-danger').addClass('text-success');
                $(`#pdf-label-${batchId} span`).text('Select PDF');
                fileInput.value = '';
            },
            error: function() { $statusMsg.text('Upload failed').addClass('text-danger'); },
            complete: function() { $btn.prop('disabled', false).text('Submit'); }
        });
    });

    // Image Zoom (Delegate event for DataTables injected rows)
    $(document).on('click', '.img-preview', function() {
        $('#fullSizeImage').attr('src', $(this).attr('src'));
        $('#imagePreviewModal').modal('show');
    });
});
</script>
@endsection