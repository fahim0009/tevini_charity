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



        <!-- Donor List Table -->
        <section class="px-4" id="contentContainer">
            <div class="row my-3">

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
                                    <th>Date</th>
                                    <th>Charity</th>
                                    <th>Batch No</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batches as $batch)
                                <tr>
                                    <td>{{ $batch->date ? $batch->date->format('d-m-Y') : 'N/A' }}</td>
                                    <td><strong>{{ $batch->charity->name }}</strong></td>
                                    <td><strong>{{ $batch->batch_no }}</strong></td>
                                    
                                    <td>{{ number_format($batch->total_amount, 2) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#batchModal{{ $batch->id }}">
                                            View Vouchers ({{ $batch->provoucher->count() }})
                                        </button>
                                        
                                        <div class="modal fade" id="batchModal{{ $batch->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Batch No: {{ $batch->batch_no }} - Voucher Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Cheque No</th>
                                                                        <th>Donor Acc</th>
                                                                        <th>Title</th>
                                                                        <th>Amount</th>
                                                                        <th>Image</th>
                                                                        <th>Created At</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($batch->transaction as $voucher)
                                                                        <tr>
                                                                            <td>{{ $voucher->cheque_no }}</td>
                                                                            <td>{{ $voucher->user->name }}</td>
                                                                            <td>{{ $voucher->title }}</td>
                                                                            <td>£{{ number_format($voucher->amount, 2) }}</td>
                                                                            <td id="barcode-container-{{ $voucher->id }}">
                                                                                @if($voucher->barcode_image)
                                                                                    <img src="{{ asset($voucher->barcode_image) }}" 
                                                                                        id="img-{{ $voucher->id }}" 
                                                                                        alt="Cheque Image" 
                                                                                        class="img-fluid img-preview" 
                                                                                        style="max-width: 100px; cursor: pointer;" 
                                                                                        title="Click to enlarge">
                                                                                @else
                                                                                    <p id="text-{{ $voucher->id }}">No image</p>
                                                                                @endif
                                                                                
                                                                                <div class="mt-2">
                                                                                    <input type="file" class="form-control form-control-sm barcode-input" 
                                                                                        data-id="{{ $voucher->id }}" accept="image/*">
                                                                                </div>
                                                                            </td>
                                                                            <td>{{ $voucher->created_at->format('d-m-Y') }}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="5" class="text-center">No vouchers available in this batch.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                    
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center p-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img src="" id="fullSizeImage" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</div>



@endsection

@section('script')


<script>
$(document).ready(function () {


    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(document).on('change', '.barcode-input', function() {
        let id = $(this).data('id');
        let file = this.files[0];
        let formData = new FormData();
        
        formData.append('barcode_image', file);
        formData.append('id', id);

        // Show a simple loader/feedback
        let $container = $(`#barcode-container-${id}`);
        $container.css('opacity', '0.5');

        $.ajax({
            url: "{{ route('voucher.upload.barcode') }}", 
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $container.css('opacity', '1');
                if(response.success) {
                    // Update the image src or replace the text
                    let newImgHtml = `<img src="${response.image_url}" id="img-${id}" alt="Cheque Image" class="img-fluid" style="max-width: 100px;">`;
                    
                    if($(`#img-${id}`).length > 0) {
                        $(`#img-${id}`).attr('src', response.image_url);
                    } else {
                        $(`#text-${id}`).replaceWith(newImgHtml);
                    }
                    alert('Barcode uploaded successfully!');
                }
            },
            error: function(xhr) {
                $container.css('opacity', '1');
                alert('Error uploading barcode. Please try again.');
            }
        });
    });


    $(document).on('click', '.img-preview', function() {
        let imageUrl = $(this).attr('src');
        $('#fullSizeImage').attr('src', imageUrl);
        $('#imagePreviewModal').modal('show');
    });

    // Ensure the main modal stays scrollable after closing the image modal
    $('#imagePreviewModal').on('hidden.bs.modal', function () {
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    });


});
</script>

@endsection