@extends('layouts.admin')

@section('content')
<div class="dashboard-content">

    {{-- Page Header --}}
    <section class="profile purchase-status">
        <div class="title-section d-flex align-items-center">
            <span class="iconify" data-icon="mdi:bank-transfer-out" data-width="25"></span>
            <h4 class="mx-2 mb-0">Add Charity Payment Transaction</h4>
        </div>
    </section>

    <div class="container-fluid mt-4">

        {{-- Success Alert --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="iconify me-1" data-icon="mdi:check-circle-outline" data-width="16"></span>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white py-3">
                        <span class="fw-semibold">
                            <span class="iconify me-1" data-icon="mdi:plus-circle-outline" data-width="18"></span>
                            New Payment Entry
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('charityPaymentStore') }}" id="charityPayForm">
                            @csrf

                            {{-- Charity --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">
                                    Charity <span class="text-danger">*</span>
                                </label>
                                <select name="topupid" id="charitySelect" class="form-control" required>
                                    <option value="">-- Select Charity --</option>
                                    @foreach($charities as $charity)
                                        <option value="{{ $charity->id }}" {{ old('topupid') == $charity->id ? 'selected' : '' }}>
                                            ({{ $charity->id }}) {{ $charity->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date & Time --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">
                                    Date & Time <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="created_at" id="created_at"
                                    class="form-control"
                                    value="{{ old('created_at') }}"
                                    required>
                            </div>

                            {{-- Amount --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" min="0.01" name="balance"
                                        class="form-control"
                                        placeholder="0.00"
                                        value="{{ old('balance') }}"
                                        required>
                                </div>
                            </div>

                            {{-- Note --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">
                                    Note <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="note" class="form-control"
                                    placeholder="Payment note..."
                                    value="{{ old('note') }}"
                                    required>
                            </div>

                            {{-- Source --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">
                                    Source <span class="text-danger">*</span>
                                </label>
                                <select name="source" class="form-control" required>
                                    <option value="Bank"   {{ old('source') == 'Bank'   ? 'selected' : '' }}>Bank</option>
                                    <option value="Cheque" {{ old('source') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Card"   {{ old('source') == 'Card'   ? 'selected' : '' }}>Card</option>
                                </select>
                            </div>

                            {{-- Send Email --}}
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="sendemail" id="sendemail"
                                        class="form-check-input"
                                        {{ old('sendemail') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sendemail">
                                        Send confirmation email to charity
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success" onclick="return confirmSubmit()">
                                    <span class="iconify me-1" data-icon="mdi:content-save-outline" data-width="16"></span>
                                    Save Transaction
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <span class="iconify me-1" data-icon="mdi:refresh" data-width="16"></span>
                                    Reset
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
{{-- Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    // ── Select2 on charity dropdown ────────────────────────────────
    $('#charitySelect').select2({
        placeholder: '-- Search Charity by name or ID --',
        allowClear: true,
        width: '100%',
    });

});

function confirmSubmit() {
    const charity  = $('#charitySelect option:selected').text().trim();
    const amount   = document.querySelector('input[name="balance"]').value;
    const datetime = document.getElementById('created_at').value;

    if (!charity || charity === '-- Select Charity --') {
        alert('Please select a charity.');
        return false;
    }
    if (!datetime) {
        alert('Please select a date & time.');
        return false;
    }

    return confirm(`Save transaction?\n\nCharity: ${charity}\nAmount: £${amount}\nDate: ${datetime.replace('T', ' ')}`);
}
</script>
@endsection