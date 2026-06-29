@extends('frontend.layouts.charity')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">My Profile</div>
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

            <!-- Left: Profile Card -->
            <div class="col-lg-4">
                <div class="profile-sidebar-card" style="background:#FDF3EE;border-radius:16px;padding:32px 24px;text-align:center;box-shadow:0 2px 20px rgba(0,0,0,0.06);position:sticky;top:30px;">
                    
                    <!-- Avatar with camera overlay -->
                    <div class="avatar-wrapper" style="position:relative;width:160px;height:160px;margin:0 auto 20px;">
                        <div id="profileImgWrap" style="width:160px;height:160px;border-radius:50%;overflow:hidden;border:4px solid #113250;box-shadow:0 4px 20px rgba(17,50,80,0.15);">
                            <img id="profileImgPreview" src="{{ auth('charity')->user()->profile_image ? asset(auth('charity')->user()->profile_image) : 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg' }}" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <label for="profile_image" class="avatar-overlay" style="position:absolute;bottom:6px;right:6px;width:42px;height:42px;background:#113250;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.2);transition:transform .2s,background .2s;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                            <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display:none;">
                        </label>
                    </div>

                    <h4 style="margin:0 0 4px;font-size:1.15rem;font-weight:600;color:#1a2a3a;">{{ auth('charity')->user()->name }}</h4>
                    <p style="margin:0 0 4px;font-size:.85rem;color:#888;">{{ auth('charity')->user()->email }}</p>
                    @if(auth('charity')->user()->acc_no)
                        <span style="display:inline-block;background:rgba(17,50,80,0.08);color:#113250;font-size:.78rem;font-weight:600;padding:4px 14px;border-radius:20px;margin-top:6px;">Acc #{{ auth('charity')->user()->acc_no }}</span>
                    @endif

                    <div style="margin-top:20px;padding-top:18px;border-top:1px solid #f0f0f0;">
                        <small style="color:#aaa;font-size:.75rem;">JPG or PNG, max 2MB</small>
                    </div>
                </div>
            </div>

            <!-- Right: Form Cards -->
            <div class="col-lg-8">

                <div class="profile-form-card" style="background:#FDF3EE;border-radius:16px;padding:28px 30px;margin-bottom:20px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
                    <div class="card-section-title" style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                        <div style="width:36px;height:36px;background:rgba(17,50,80,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#113250" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M2 12h20"/>
                                <path d="M12 2c3.5 3.5 3.5 16.5 0 20"/>
                                <path d="M12 2C8.5 5.5 8.5 18.5 12 22"/>
                            </svg>
                        </div>
                        <h5 style="margin:0;font-size:1rem;font-weight:600;color:#1a2a3a;">Website</h5>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Website</label>
                                <input type="text" class="form-control" id="website" name="website" placeholder="Website" value="{{ auth('charity')->user()->website }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                                <span style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Charities with websites receive more donor engagement -  increased donations by 35% after launching a website. </span>
                                <p style="font-size:.92rem;font-weight:700;color:#18988B;margin-bottom:6px;">If you don’t yet have a website, FREE website support is available through Tevini’s trusted technology partner, <a href="https://www.mentosoftware.co.uk/#contact" target="blank" style="font-size:1rem;font-weight:600;color:#0D2137">Mento Software</a>. </p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Card 1: Charity Information -->
                <div class="profile-form-card" style="background:#FDF3EE;border-radius:16px;padding:28px 30px;margin-bottom:20px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
                    <div class="card-section-title" style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                        <div style="width:36px;height:36px;background:rgba(17,50,80,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#113250" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <h5 style="margin:0;font-size:1rem;font-weight:600;color:#1a2a3a;">Charity Information</h5>
                    </div>

                    <div class="row">
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full name" value="{{ auth('charity')->user()->name }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;transition:border .2s;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Charity Number</label>
                                <input type="number" class="form-control" id="acc_no" name="acc_no" placeholder="Charity Number" value="{{ auth('charity')->user()->acc_no }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone number" value="{{ auth('charity')->user()->number }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="{{ auth('charity')->user()->email }}" readonly style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;background:#f8f9fa;color:#888;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Address -->
                <div class="profile-form-card" style="background:#FDF3EE;border-radius:16px;padding:28px 30px;margin-bottom:20px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
                    <div class="card-section-title" style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                        <div style="width:36px;height:36px;background:rgba(17,50,80,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#113250" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <h5 style="margin:0;font-size:1rem;font-weight:600;color:#1a2a3a;">Address</h5>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Address Line 1</label>
                                <input type="text" class="form-control" id="houseno" name="houseno" placeholder="Start typing your postcode..." value="{{ auth('charity')->user()->address }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Address Line 2</label>
                                <input type="text" class="form-control" id="address_second_line" name="address_second_line" placeholder="Second line" value="{{ auth('charity')->user()->address_second_line }}" readonly style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;background:#f8f9fa;color:#888;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Address Line 3</label>
                                <input type="text" class="form-control" id="address_third_line" name="address_third_line" placeholder="Third line" value="{{ auth('charity')->user()->address_third_line }}" readonly style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;background:#f8f9fa;color:#888;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Town</label>
                                <input type="text" class="form-control" id="town" name="town" placeholder="Town" value="{{ auth('charity')->user()->town }}" readonly style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;background:#f8f9fa;color:#888;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Postcode</label>
                                <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Postcode" value="{{ auth('charity')->user()->post_code }}" readonly style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;background:#f8f9fa;color:#888;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Banking Details -->
                <div class="profile-form-card" style="background:#FDF3EE;border-radius:16px;padding:28px 30px;margin-bottom:20px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
                    <div class="card-section-title" style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                        <div style="width:36px;height:36px;background:rgba(17,50,80,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#113250" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <h5 style="margin:0;font-size:1rem;font-weight:600;color:#1a2a3a;">Banking Details</h5>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Account Name</label>
                                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account name" value="{{ auth('charity')->user()->account_name }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account number" value="{{ auth('charity')->user()->account_number }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Sort Code</label>
                                <input type="text" class="form-control" id="account_sortcode" name="account_sortcode" placeholder="00-00-00" value="{{ auth('charity')->user()->account_sortcode }}" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Change Password -->
                <div class="profile-form-card" style="background:#FDF3EE;border-radius:16px;padding:28px 30px;margin-bottom:20px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
                    <div class="card-section-title" style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                        <div style="width:36px;height:36px;background:rgba(17,50,80,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#113250" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <h5 style="margin:0;font-size:1rem;font-weight:600;color:#1a2a3a;">Change Password</h5>
                        <span style="font-size:.75rem;color:#aaa;margin-left:auto;">Leave blank to keep current</span>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Confirm Password</label>
                                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Repeat new password" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="text-align:right;padding:0 0 10px;">
                    <input type="submit" value="Save Changes" class="btn-theme bg-primary" style="padding:12px 36px;border-radius:10px;font-size:.92rem;font-weight:600;letter-spacing:.3px;transition:transform .15s,box-shadow .15s;cursor:pointer;">
                </div>

            </div>
        </div>
    </form>

    <!-- Linked Email Addresses -->
    <div class="row mt-5">
        <div class="col-lg-12 px-3">

            <div class="profile-form-card" style="background:#FDF3EE;border-radius:16px;padding:28px 30px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
                
                <div class="card-section-title" style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                    <div style="width:36px;height:36px;background:rgba(17,50,80,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#113250" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <h5 style="margin:0;font-size:1rem;font-weight:600;color:#1a2a3a;">Linked Email Addresses</h5>
                </div>

                <!-- Add Email Form -->
                <form action="{{ route('charity.emailAccountStore') }}" method="POST" id="storeForm">
                    @csrf
                    <div class="row align-items-end" style="margin-bottom:20px;">
                        <div class="col-lg-7">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Add New Email</label>
                                <input type="email" class="form-control" id="newemail" name="newemail" placeholder="new@example.com" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <input type="submit" value="+ Add Email" class="btn-theme bg-primary" style="width:100%;padding:10px 20px;border-radius:10px;font-size:.88rem;font-weight:600;">
                        </div>
                    </div>
                </form>

                <!-- Edit Email Form (hidden by default) -->
                <form action="{{ route('charity.emailAccountUpdate') }}" method="POST" id="updateForm" style="display:none;">
                    @csrf
                    <div class="row align-items-end" style="margin-bottom:20px;background:#fff8e1;padding:16px;border-radius:10px;border:1px solid #ffe082;">
                        <div class="col-lg-7">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:.82rem;font-weight:500;color:#666;margin-bottom:6px;">Update Email</label>
                                <input type="email" class="form-control" id="upemail" name="upemail" placeholder="updated@example.com" style="border-radius:10px;border:1.5px solid #e0e0e0;padding:10px 14px;font-size:.9rem;">
                                <input type="hidden" name="userDetailId" id="userDetailId">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div style="display:flex;gap:8px;">
                                <input type="submit" value="Update" class="btn-theme bg-primary" style="flex:1;padding:10px 16px;border-radius:10px;font-size:.88rem;font-weight:600;">
                                <input type="button" value="Cancel" id="cancelEdit" class="btn-theme" style="flex:1;background:#e0e0e0;color:#333;padding:10px 16px;border-radius:10px;font-size:.88rem;font-weight:600;cursor:pointer;">
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Email Table -->
                <div style="border-radius:12px;overflow:hidden;border:1px solid #eee;">
                    <table class="table" style="margin-bottom:0;">
                        <thead>
                            <tr style="background:#f8f9fb;">
                                <th style="font-size:.82rem;font-weight:600;color:#666;border-bottom:2px solid #eee;padding:14px 16px;">Date</th>
                                <th style="font-size:.82rem;font-weight:600;color:#666;border-bottom:2px solid #eee;padding:14px 16px;">Email Address</th>
                                <th style="width:150px;font-size:.82rem;font-weight:600;color:#666;border-bottom:2px solid #eee;padding:14px 16px;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\UserDetail::where('charity_id', auth('charity')->user()->id)->whereNotNull('email_verified_at')->get() as $data)
                            <tr style="transition:background .15s;">
                                <td style="padding:14px 16px;font-size:.88rem;color:#555;">{{ $data->date }}</td>
                                <td style="padding:14px 16px;font-size:.88rem;color:#1a2a3a;font-weight:500;">{{ $data->email }}</td>
                                <td style="padding:14px 16px;text-align:right;">
                                    <button data-udid="{{ $data->id }}" data-email="{{ $data->email }}" class="btn btn-sm emaileditBtn" style="margin-right:6px;background:rgba(17,50,80,0.08);color:#113250;border:none;border-radius:8px;padding:5px 14px;font-size:.8rem;font-weight:600;cursor:pointer;transition:background .15s;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;vertical-align:-1px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </button>
                                    <form action="{{ route('charity.emailDestroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this email?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:rgba(220,53,69,0.08);color:#dc3545;border:none;border-radius:8px;padding:5px 14px;font-size:.8rem;font-weight:600;cursor:pointer;transition:background .15s;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;vertical-align:-1px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if (\App\Models\UserDetail::where('charity_id', auth('charity')->user()->id)->whereNotNull('email_verified_at')->count() === 0)
                            <tr>
                                <td colspan="3" style="padding:30px 16px;text-align:center;color:#aaa;font-size:.88rem;">No linked email addresses yet.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>

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
        }
    });

    /* Hover effect on avatar overlay */
    var overlay = document.querySelector('.avatar-overlay');
    if (overlay) {
        overlay.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.1)';
            this.style.background = '#0d2137';
        });
        overlay.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
            this.style.background = '#113250';
        });
    }

    /* Submit button hover */
    var submitBtn = document.querySelector('input[type="submit"][value="Save Changes"]');
    if (submitBtn) {
        submitBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 4px 15px rgba(17,50,80,0.3)';
        });
        submitBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }

    /* Form input focus styling */
    document.querySelectorAll('.profile-form-card input[type="text"], .profile-form-card input[type="email"], .profile-form-card input[type="number"], .profile-form-card input[type="password"]').forEach(function (input) {
        input.addEventListener('focus', function () {
            this.style.borderColor = '#113250';
            this.style.boxShadow = '0 0 0 3px rgba(17,50,80,0.08)';
        });
        input.addEventListener('blur', function () {
            if (!this.readOnly) {
                this.style.borderColor = '#e0e0e0';
            }
            this.style.boxShadow = 'none';
        });
    });

    /* Table row hover */
    document.querySelectorAll('.profile-form-card table tbody tr').forEach(function (row) {
        row.addEventListener('mouseenter', function () {
            this.style.background = '#f8f9fb';
        });
        row.addEventListener('mouseleave', function () {
            this.style.background = '';
        });
    });

    /* Edit/Delete button hover */
    document.querySelectorAll('.profile-form-card table button').forEach(function (btn) {
        btn.addEventListener('mouseenter', function () {
            var bg = this.style.background;
            if (bg.includes('113,50,80')) {
                this.style.background = 'rgba(17,50,80,0.16)';
            } else if (bg.includes('220,53,69')) {
                this.style.background = 'rgba(220,53,69,0.16)';
            }
        });
        btn.addEventListener('mouseleave', function () {
            var bg = this.style.background;
            if (bg.includes('113,50,80')) {
                this.style.background = 'rgba(17,50,80,0.08)';
            } else if (bg.includes('220,53,69')) {
                this.style.background = 'rgba(220,53,69,0.08)';
            }
        });
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
    // setTimeout(function () {
    //     document.querySelectorAll('.alert').forEach(function (el) {
    //         el.style.transition = 'opacity .5s';
    //         el.style.opacity    = '0';
    //         setTimeout(function () { el.remove(); }, 500);
    //     });
    // }, 4000);

});
</script>
@endsection