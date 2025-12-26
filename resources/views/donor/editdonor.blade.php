@extends('layouts.admin')

@section('style')
<style>
    .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 0.5rem; }
    .form-control { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #ced4da; transition: all 0.2s; }
    .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid #f8f9fa; }
    .btn-theme { background: #4e73df; border: none; padding: 10px 30px; border-radius: 8px; font-weight: 600; transition: transform 0.2s; }
    .btn-theme:hover { background: #2e59d9; transform: translateY(-1px); }
    .profile-header { background: #fff; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="profile-header shadow-sm">
        <div class="ms-3 p-3">
            <h4 class="mb-0">Edit Donor Profile</h4>
            <small class="text-muted">Update account information and preferences</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 p-md-5">
                <form action="{{ route('donor.update', $users->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                    @csrf
                    <input type="hidden" name="donorid" value="{{ $users->id }}">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-primary">Account Registration For</label>
                            <select name="profile_type" id="profile_type" class="form-select form-control @error('profile_type') is-invalid @enderror">
                                <option value="Company" {{ $users->profile_type == 'Company' ? 'selected' : '' }}>Corporate / Company</option>
                                <option value="Personal" {{ $users->profile_type == 'Personal' ? 'selected' : '' }}>Personal Individual</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-title">General Information</div>

                    <div class="row g-3 mb-4" id="companyDiv">
                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input id="company_name" type="text" class="form-control" name="company_name" value="{{ $users->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Representative Name</label>
                            <input id="company_last_name" type="text" class="form-control" name="company_last_name" value="{{ $users->surname }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4" id="personalDiv" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="fname" id="fname" class="form-control" value="{{ $users->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Surname</label>
                            <input type="text" name="surname" id="surname" class="form-control" value="{{ $users->surname }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $users->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $users->email }}">
                        </div>
                    </div>

                    <div class="section-title">Address & Billing</div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" name="houseno" class="form-control" value="{{ $users->houseno }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="street" class="form-control" value="{{ $users->street }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Address Line 3</label>
                            <input type="text" name="address_third_line" class="form-control" value="{{ $users->address_third_line }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Town/City</label>
                            <input type="text" name="town" class="form-control" value="{{ $users->town }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Postcode</label>
                            <input type="text" name="postcode" class="form-control" value="{{ $users->postcode }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account No (Internal)</label>
                            <input type="text" name="accountno" class="form-control" value="{{ $users->accountno }}">
                        </div>
                    </div>

                    <div class="section-title">Security (Leave blank to keep current)</div>

                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="cpassword" class="form-control" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-light px-4 me-md-2">Cancel</button>
                        <button type="submit" class="btn btn-theme text-white px-5 shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection



@section('script')
<script>
    $(document).ready(function() {
        // Function to toggle visibility
        function toggleProfileFields(type) {
            if (type === 'Personal') {
                $('#personalDiv').fadeIn();
                $('#companyDiv').hide();
            } else {
                $('#companyDiv').fadeIn();
                $('#personalDiv').hide();
            }
        }

        // Run on page load to set correct state
        toggleProfileFields($('#profile_type').val());

        // Handle Change
        $('#profile_type').change(function() {
            const type = $(this).val();
            toggleProfileFields(type);
            
            // Logic to move values between fields if needed
            if (type === 'Personal') {
                $('#fname').val($('#company_last_name').val());
            } else {
                $('#company_last_name').val($('#fname').val());
            }
        });
    });
</script>
@endsection
