@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white p-2 rounded-3 me-3">
            <span class="iconify" data-icon="fluent:contact-card-28-regular" data-width="24"></span>
        </div>
        <div>
            <h4 class="fw-bold mb-0">Donor Profile</h4>
            <small class="text-muted">ID: {{$donor_id}}</small> <br>
            <small class="text-muted">Account No: {{$profile_data->accountno ?? ''}}</small>
        </div>
    </div>

    @include('inc.user_menue')

    <div class="row mt-4">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="position-relative mb-3">
                    <img class="rounded-circle border border-4 border-white shadow-sm" 
                         width="120px" height="120px" style="object-fit: cover;"
                         src="{{ $profile_data->photo && file_exists(public_path('images/'.$profile_data->photo)) ? asset('images/'.$profile_data->photo) : 'https://www.tevini.co.uk/assets/admin/images/profile.png' }}">
                </div>
                <h5 class="fw-bold mb-1">{{ $profile_data->name }} {{ $profile_data->surname }}</h5>
                <p class="text-muted small mb-3">{{ $profile_data->email }}</p>

                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Current Balance</span>
                        <span class="fw-bold text-primary">
                            {{ $profile_data->getLiveBalance() < 0 ? '-' : '' }}£{{ number_format(abs($profile_data->getLiveBalance()), 2) }}
                        </span>
                    </div>
                    <hr class="my-2 opacity-25">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Expected Gift Aid</span>
                        <span class="fw-bold">
                            @if ($profile_data->gift_aid_currenction > 0)
                                <span class="text-success small">£{{ number_format($profile_data->gift_aid_currenction, 2) }} Gift Aid</span>
                            @else
                                <span class="text-danger small">£{{ number_format($profile_data->expected_gift_aid, 2) }} Gift Aid</span>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Current Year</span>
                        @if ($profile_data->current_yr_gift_aid > 0)
                            <span class="text-success small">£{{ number_format($profile_data->current_yr_gift_aid, 2) }} Gift Aid</span>
                        @else
                            <span class="text-danger small">£{{ number_format($currentyramountExpGiftAid, 2) }} Gift Aid</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Prev. Year</span>
                        <span class="fw-bold">
                            @if ($profile_data->prev_yr_gift_aid > 0)
                                <span class="text-success small">£{{ number_format($profile_data->prev_yr_gift_aid, 2) }} Gift Aid</span>
                            @else
                                <span class="text-danger small">£{{ number_format($lastTaxYearAmountExpGiftAid, 2) }} Gift Aid</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if(session('message'))
                <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('message') }}</div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Personal Information</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary px-3" id="enableEditMode">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('donor.update', $profile_data->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="donorid" value="{{ $profile_data->id }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">First Name</label>
                                <input type="text" class="form-control bg-light border-0" name="name" value="{{ $profile_data->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Surname</label>
                                <input type="text" class="form-control bg-light border-0" name="surname" value="{{ $profile_data->surname }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Phone</label>
                                <input type="text" class="form-control bg-light border-0" name="phone" value="{{ $profile_data->phone }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Email</label>
                                <input type="email" class="form-control bg-light border-0" name="email" value="{{ $profile_data->email }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">House No</label>
                                <input type="text" class="form-control bg-light border-0" name="houseno" value="{{ $profile_data->houseno }}" readonly>
                            </div>
                            <div class="col-md-8">
                                <label class="small text-muted fw-bold">Street</label>
                                <input type="text" class="form-control bg-light border-0" name="street" value="{{ $profile_data->street }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Town</label>
                                <input type="text" class="form-control bg-light border-0" name="town" value="{{ $profile_data->town }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Postcode</label>
                                <input type="text" class="form-control bg-light border-0" name="postcode" value="{{ $profile_data->postcode }}" readonly>
                            </div>
                        </div>
                        <div class="mt-4 d-none" id="saveButtonGroup">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                            <button type="button" class="btn btn-link text-muted" onclick="location.reload()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">Gift Aid Adjustments</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('donor.update_gift_aid', $profile_data->id) }}" method="POST" id="giftAidForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">Expected Gift Aid</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0">£</span>
                                    <input type="number" step="0.01" class="form-control bg-light border-0 gift-aid-field" name="gift_aid_currenction" value="{{ $profile_data->gift_aid_currenction }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">Current Year</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0">£</span>
                                    <input type="number" step="0.01" class="form-control bg-light border-0 gift-aid-field" name="current_yr_gift_aid" value="{{ $profile_data->current_yr_gift_aid }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">Previous Year</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0">£</span>
                                    <input type="number" step="0.01" class="form-control bg-light border-0 gift-aid-field" name="prev_yr_gift_aid" value="{{ $profile_data->prev_yr_gift_aid }}">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-dark px-4">Update Gift Aid</button>
                            <button type="button" id="clearGiftAidBtn" class="btn btn-outline-danger px-3 ms-2">
                                <i class="fas fa-eraser me-1"></i> Clear All
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6 class="fw-bold mb-0">Linked Email Accounts</h6>
                        </div>
                        <div class="col-md-8">
                            <form action="{{route('newUserCredentialStore')}}" method="POST" class="row g-2 justify-content-end">
                                @csrf
                                <input type="hidden" name="donor_id" value="{{$donor_id}}">
                                <div class="col-sm-8 col-md-7">
                                    <input type="email" name="newemail" class="form-control form-control-sm" placeholder="Enter new linked email address" required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-sm btn-dark px-3">Add Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small text-muted" style="width: 20%;">DATE</th>
                                <th class="small text-muted" style="width: 60%;">EMAIL ADDRESS</th>
                                <th class="text-end pe-4 small text-muted" style="width: 20%;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\UserDetail::where('user_id', $donor_id)->get() as $data)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $data->date }}</td>
                                <td class="fw-medium text-dark">{{ $data->email }}</td>
                                <td class="text-end pe-4">
                                    <button data-udid="{{$data->id}}" data-email="{{$data->email}}" class="btn btn-sm btn-light border editEmailBtn">
                                        <i class="fas fa-edit small"></i> Edit
                                    </button>
                                    <form action="{{ route('useremail.destroy', $data->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 ms-1" onclick="return confirm('Delete email?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editEmailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" id="editEmailForm">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h6 class="fw-bold mb-0">Update Linked Email</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <label class="small fw-bold text-muted mb-2">Email Address</label>
                    <input type="email" class="form-control" id="editEmail" name="email" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100">Update Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Enable Editing for Personal Info
    $('#enableEditMode').on('click', function() {
        $(this).closest('.card').find('input[readonly]').each(function() {
            $(this).removeAttr('readonly').removeClass('bg-light border-0').addClass('border');
        });
        $('#saveButtonGroup').removeClass('d-none');
        $(this).fadeOut();
    });

    // Handle Gift Aid Clear Button
    $('#clearGiftAidBtn').on('click', function() {
        if(confirm('Reset all Gift Aid adjustment fields to zero?')) {
            $('.gift-aid-field').val('0.00');
        }
    });

    // Handle Email List Editing
    $('.editEmailBtn').on('click', function() {
        let udid = $(this).data('udid');
        let email = $(this).data('email');
        let updateRoute = "{{ route('useremail.update', ':id') }}".replace(':id', udid);

        $('#editEmail').val(email);
        $('#editEmailForm').attr('action', updateRoute);
        $('#editEmailModal').modal('show');
    });
});
</script>
@endsection