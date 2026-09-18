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
            <td>{{ $voucher->user->name ?? 'N/A' }}</td>
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