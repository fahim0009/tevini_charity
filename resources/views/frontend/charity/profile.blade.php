@extends('frontend.layouts.charity')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                My Profile
            </div>
        </div>
    </div>

    <!-- Image loader -->
    <div id='loading' style='display:none;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
    </div>
    <!-- Image loader -->

    <div class="ermsg"></div>

    @if(session()->has('message') && session()->get('status') == 200)
        <div class="alert alert-success" style="margin:10px 15px;">{{ session()->get('message') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger" style="margin:10px 15px;">{{ session()->get('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" style="margin:10px 15px;">
            <ul style="margin-bottom:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('charity_profileUpdate') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mt-4 px-3">

            <!-- Left: Profile Image -->
            <div class="col-lg-4">
                <div class="form-group">
                    <label>Profile Image</label>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:15px;">
                        <div id="profileImgWrap" style="width:140px;height:140px;border-radius:50%;overflow:hidden;border:3px solid #113250;background:#f5f5f5;flex-shrink:0;">
                            <img id="profileImgPreview" src="{{ auth('charity')->user()->profile_image ? asset(auth('charity')->user()->profile_image) : 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg' }}" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div style="display:flex;flex-direction:column;gap:8px;align-items:center;">
                            <label class="btn-theme bg-primary" style="cursor:pointer;display:inline-block;width:fit-content;">
                                Choose Image
                                <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display:none;">
                            </label>
                            {{-- <input type="button" id="removeImgBtn" value="Remove" class="btn-theme" style="background:#e0e0e0;color:#333;display:inline-block;width:fit-content;cursor:pointer;{{ !auth('charity')->user()->profile_image ? 'display:none;' : '' }}"> --}}
                            <small style="color:#888;margin:0;">JPG, PNG max 2MB</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: All Fields -->
            <div class="col-lg-8">

                <!-- Personal Information -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Full name" value="{{ auth('charity')->user()->name }}">
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Charity Number</label>
                            <input type="number" class="form-control" id="acc_no" name="acc_no" placeholder="Charity Number" value="{{ auth('charity')->user()->acc_no }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Website</label>
                            <input type="text" class="form-control" id="website" name="website" placeholder="Website" value="{{ auth('charity')->user()->website }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone number" value="{{ auth('charity')->user()->number }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="{{ auth('charity')->user()->email }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Address Line 1</label>
                            <input type="text" class="form-control" id="houseno" name="houseno" placeholder="Start typing your postcode..." value="{{ auth('charity')->user()->address }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" class="form-control" id="address_second_line" name="address_second_line" placeholder="Second line" value="{{ auth('charity')->user()->address_second_line }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Address Line 3</label>
                            <input type="text" class="form-control" id="address_third_line" name="address_third_line" placeholder="Third line" value="{{ auth('charity')->user()->address_third_line }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Town</label>
                            <input type="text" class="form-control" id="town" name="town" placeholder="Town" value="{{ auth('charity')->user()->town }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Postcode</label>
                            <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Postcode" value="{{ auth('charity')->user()->post_code }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Banking Details -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account name" value="{{ auth('charity')->user()->account_name }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account number" value="{{ auth('charity')->user()->account_number }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Sort Code</label>
                            <input type="text" class="form-control" id="account_sortcode" name="account_sortcode" placeholder="00-00-00" value="{{ auth('charity')->user()->account_sortcode }}">
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="row mt-3">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Repeat new password">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" value="Save Changes" class="btn-theme bg-primary">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <!-- Linked Email Addresses -->
    <div class="row mt-5">
        <div class="col-lg-10 px-3">

            <div class="pagetitle pb-2" style="font-size:1.1rem;">
                Linked Email Addresses
            </div>

            <!-- Add Email Form -->
            <form action="{{ route('charity.emailAccountStore') }}" method="POST" id="storeForm">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Add New Email</label>
                            <input type="email" class="form-control" id="newemail" name="newemail" placeholder="new@example.com">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <input type="submit" value="Add Email" class="btn-theme bg-primary">
                        </div>
                    </div>
                </div>
            </form>

            <!-- Edit Email Form (hidden by default) -->
            <form action="{{ route('charity.emailAccountUpdate') }}" method="POST" id="updateForm" style="display:none;">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Update Email</label>
                            <input type="email" class="form-control" id="upemail" name="upemail" placeholder="updated@example.com">
                            <input type="hidden" name="userDetailId" id="userDetailId">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div style="display:flex;gap:8px;">
                                <input type="submit" value="Update" class="btn-theme bg-primary">
                                <input type="button" value="Cancel" id="cancelEdit" class="btn-theme" style="background:#e0e0e0;color:#333;">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Email Table -->
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Email Address</th>
                        <th style="width:150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\UserDetail::where('charity_id', auth('charity')->user()->id)->whereNotNull('email_verified_at')->get() as $data)
                    <tr>
                        <td>{{ $data->date }}</td>
                        <td>{{ $data->email }}</td>
                        <td>
                            <button data-udid="{{ $data->id }}" data-email="{{ $data->email }}" class="btn btn-sm btn-primary emaileditBtn" style="margin-right:5px;">Edit</button>
                            <form action="{{ route('charity.emailDestroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this email?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/@ideal-postcodes/address-finder-bundled@4"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ── Postcode autocomplete ── */
    IdealPostcodes.AddressFinder.watch({
        apiKey: "ak_lt4ke30geFynIWbUB7nPMdpkvxGcP",
        outputFields: {
            line_1:    "#houseno",
            line_2:    "#address_second_line",
            line_3:    "#address_third_line",
            post_town: "#town",
            postcode:  "#postcode"
        }
    });

    /* ── Active nav state ── */
    $("#profileinfo").addClass('active is-expanded');
    $("#profile").addClass('active');

    /* ── Profile image preview ── */
    var defaultImg = "https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg";

    document.getElementById('profile_image').addEventListener('change', function (e) {
        var file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert("Image must be under 2MB.");
                this.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                document.getElementById('profileImgPreview').src = ev.target.result;
            };
            reader.readAsDataURL(file);
            document.getElementById('removeImgBtn').style.display = 'inline-block';
        }
    });

    document.getElementById('removeImgBtn').addEventListener('click', function () {
        document.getElementById('profileImgPreview').src = defaultImg;
        document.getElementById('profile_image').value = '';
        this.style.display = 'none';

        /* Add hidden input to tell backend to remove */
        var removeInput = document.createElement('input');
        removeInput.type = 'hidden';
        removeInput.name = 'remove_profile_image';
        removeInput.value = '1';
        this.closest('form').appendChild(removeInput);
    });

    /* ── Email edit toggle ── */
    var storeForm  = document.getElementById('storeForm');
    var updateForm = document.getElementById('updateForm');
    var cancelBtn  = document.getElementById('cancelEdit');

    document.querySelectorAll('.emaileditBtn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('upemail').value       = this.dataset.email;
            document.getElementById('userDetailId').value  = this.dataset.udid;
            storeForm.style.display  = 'none';
            updateForm.style.display = 'block';
        });
    });

    cancelBtn.addEventListener('click', function () {
        updateForm.style.display = 'none';
        storeForm.style.display  = 'block';
    });

    /* ── Auto-hide alerts after 4s ── */
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            el.style.transition = 'opacity .5s';
            el.style.opacity    = '0';
            setTimeout(function () { el.remove(); }, 500);
        });
    }, 4000);

});
</script>
@endsection