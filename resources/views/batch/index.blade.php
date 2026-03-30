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
                        @foreach($batches as $batch)
                        <tr>
                            <td><span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> {{ $batch->date ? $batch->date->format('d-M-Y') : 'N/A' }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $batch->charity->name }}</div>
                                <div class="text-muted small">ID: #{{ $batch->charity->id }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">#{{ $batch->batch_no }}</span>
                            </td>
                            <td class="fw-bold text-primary">£{{ number_format($batch->total_amount, 2) }}</td>
                            <td class="text-end">
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <button type="button" class="btn btn-sm btn-view-vouchers px-3" data-bs-toggle="modal" data-bs-target="#batchModal{{ $batch->id }}">
                                        View Vouchers ({{ $batch->provoucher->count() }})
                                    </button>
                                    
                                </div>

                                <div class="modal fade" id="batchModal{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Batch #{{ $batch->batch_no }} - Items</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="bg-light sticky-top">
                                                        <tr>
                                                            <th class="ps-4">Cheque No</th>
                                                            <th>Donor Acc</th>
                                                            <th>Title</th>
                                                            <th>Amount</th>
                                                            <th>Cheque Image</th>
                                                            <th class="pe-4">Added</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($batch->transaction as $voucher)
                                                        <tr>
                                                            <td class="ps-4 fw-medium">{{ $voucher->cheque_no }}</td>
                                                            <td>{{ $voucher->user->name }}</td>
                                                            <td class="small">{{ $voucher->title }}</td>
                                                            <td class="fw-bold text-success">£{{ number_format($voucher->amount, 2) }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2" id="barcode-container-{{ $voucher->id }}">
                                                                    @if($voucher->barcode_image)
                                                                        <img src="{{ asset($voucher->barcode_image) }}" id="img-{{ $voucher->id }}" class="img-preview-thumb img-preview">
                                                                    @else
                                                                        <span class="text-muted small italic" id="text-{{ $voucher->id }}">None</span>
                                                                    @endif
                                                                    
                                                                    <div class="file-upload-wrapper" style="width: 40px;">
                                                                        <div class="file-upload-label p-1">
                                                                            <i class="fas fa-upload small"></i>
                                                                        </div>
                                                                        <input type="file" class="file-upload-input barcode-input" data-id="{{ $voucher->id }}" accept="image/*">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="pe-4 small text-muted">{{ $voucher->created_at->format('d/m/y') }}</td>
                                                        </tr>
                                                        @empty
                                                        <tr><td colspan="6" class="text-center py-4">No vouchers found.</td></tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <div class="input-group input-group-sm justify-content-end" style="width: 250px;">
                                        <div class="file-upload-wrapper me-1">
                                            <div class="file-upload-label" id="pdf-label-{{ $batch->id }}">
                                                <i class="fas fa-file-pdf text-danger"></i> <span class="text-truncate" style="max-width: 80px;">Select PDF</span>
                                            </div>
                                            <input type="file" class="file-upload-input pdf-input" id="pdf-{{ $batch->id }}" accept="application/pdf" data-id="{{ $batch->id }}">
                                        </div>
                                        <button class="btn btn-dark upload-pdf-btn" data-id="{{ $batch->id }}"  data-batch_no="{{ $batch->batch_no }}">Submit</button>
                                    </div>
                                    <small class="status-msg" id="status-{{ $batch->id }}"></small>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <a href="{{ route('admin.batchesEdit', $batch->id)}}" class="btn btn-sm btn-view-vouchers px-3">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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

    // Show selected filename for PDF
    $('.pdf-input').on('change', function() {
        let fileName = this.files[0] ? this.files[0].name : "Select PDF";
        let batchId = $(this).data('id');
        $(`#pdf-label-${batchId} span`).text(fileName);
    });

    // Barcode Upload logic
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

    // PDF Upload logic
    $('.upload-pdf-btn').on('click', function() {
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

    // Image Zoom
    $(document).on('click', '.img-preview', function() {
        $('#fullSizeImage').attr('src', $(this).attr('src'));
        $('#imagePreviewModal').modal('show');
    });
});
</script>
@endsection