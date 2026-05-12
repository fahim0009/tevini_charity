@extends('frontend.layouts.charity')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap');

    :root {
        --sage:       #113250;
        --sage-light: #113250;
        --sage-pale:  #E8E1D9;
        --clay:       #c97b5a;
        --cream:      #faf8f4;
        --ink:        #1e2a26;
        --muted:      #113250;
        --border:     #dde8e5;
        --card-bg:    #ffffff;
        --danger:     #d94f4f;
        --radius:     16px;
        --shadow-sm:  0 2px 8px rgba(74,124,111,.08);
        --shadow-md:  0 8px 32px rgba(74,124,111,.12);
    }


    /* ── page shell ─────────────────────────────── */
    .cp-page {
        min-height: 100vh;
        padding: 2.5rem 2rem 4rem;
    }

    .cp-header {
        display: flex;
        align-items: baseline;
        gap: .75rem;
        margin-bottom: 2rem;
        animation: fadeUp .5s ease both;
    }
    .cp-header h1 {
        font-family: 'DM Serif Display', serif;
        font-size: 2rem;
        color: var(--ink);
        line-height: 1.1;
    }
    .cp-header span {
        font-size: .8rem;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--sage-light);
        font-weight: 500;
    }

    /* ── alerts ─────────────────────────────────── */
    .cp-alert {
        border-radius: var(--radius);
        padding: .85rem 1.25rem;
        font-size: .875rem;
        margin-bottom: 1.5rem;
        animation: fadeUp .4s ease both;
    }
    .cp-alert.success { background: #113250; border-left: 4px solid var(--sage); color: #ffffff; }
    .cp-alert.danger  { background: #fdecea; border-left: 4px solid var(--danger); color: #8b2020; }
    .cp-alert ul { padding-left: 1.1rem; }

    /* ── two-column grid ─────────────────────────── */
    .cp-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.75rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .cp-grid { grid-template-columns: 1fr; }
    }

    /* ── sidebar card ────────────────────────────── */
    .cp-sidebar {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow-md);
        padding: 2rem 1.5rem;
        text-align: center;
        position: sticky;
        top: 1.5rem;
        animation: fadeUp .45s .05s ease both;
    }

    .avatar-ring {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        margin: 0 auto 1.25rem;
        padding: 4px;
        background: linear-gradient(135deg, var(--sage), var(--sage-light));
        box-shadow: 0 4px 20px rgba(74,124,111,.25);
    }
    .avatar-ring img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
    }

    .sidebar-name {
        font-family: 'DM Serif Display', serif;
        font-size: 1.2rem;
        color: var(--ink);
        margin-bottom: .25rem;
    }
    .sidebar-email {
        font-size: .8rem;
        color: var(--muted);
        margin-bottom: 1.5rem;
        word-break: break-all;
    }

    .sidebar-meta {
        background: var(--sage-pale);
        border-radius: 10px;
        padding: .9rem 1rem;
        text-align: left;
    }
    .sidebar-meta-row {
        display: flex;
        align-items: center;
        gap: .6rem;
        font-size: .8rem;
        color: var(--muted);
        padding: .35rem 0;
        border-bottom: 1px solid var(--border);
    }
    .sidebar-meta-row:last-child { border-bottom: none; }
    .sidebar-meta-row svg { flex-shrink: 0; color: var(--sage); }
    .sidebar-meta-row strong { color: var(--ink); font-weight: 500; }

    /* ── main panel ──────────────────────────────── */
    .cp-main {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        animation: fadeUp .45s .1s ease both;
    }

    /* ── section card ────────────────────────────── */
    .cp-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        border: 1px solid var(--border);
    }
    .cp-card-header {
        padding: 1.1rem 1.5rem;
        background: var(--sage-pale);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: .65rem;
    }
    .cp-card-header svg { color: var(--sage); }
    .cp-card-header h2 {
        font-size: .875rem;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--sage);
    }
    .cp-card-body { padding: 1.5rem; }

    /* ── form elements ───────────────────────────── */
    .form-row {
        display: grid;
        gap: 1rem;
    }
    .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
    .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    @media (max-width: 600px) {
        .form-row.cols-2, .form-row.cols-3 { grid-template-columns: 1fr; }
    }

    .field { display: flex; flex-direction: column; gap: .4rem; }
    .field label {
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .field input {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: .65rem .9rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem;
        color: var(--ink);
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .field input:focus {
        border-color: var(--sage-light);
        box-shadow: 0 0 0 3px rgba(106,171,156,.18);
    }
    .field input[readonly] {
        background: var(--sage-pale);
        color: var(--muted);
        cursor: not-allowed;
    }

    /* ── buttons ─────────────────────────────────── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border: none;
        border-radius: 10px;
        padding: .65rem 1.4rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s, opacity .15s;
        text-decoration: none;
    }
    .btn:hover  { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.12); }
    .btn:active { transform: translateY(0); }

    .btn-primary {
        background: var(--sage);
        color: #fff;
        box-shadow: 0 2px 8px rgba(74,124,111,.3);
    }
    .btn-primary:hover { background: #3d6b5f; }

    .btn-clay {
        background: var(--clay);
        color: #fff;
        box-shadow: 0 2px 8px rgba(201,123,90,.3);
    }

    .btn-ghost {
        background: var(--sage-pale);
        color: var(--sage);
        border: 1.5px solid var(--border);
    }
    .btn-ghost:hover { background: var(--border); }

    .btn-danger {
        background: #fdecea;
        color: var(--danger);
        border: 1.5px solid #f3c2bf;
    }
    .btn-danger:hover { background: var(--danger); color: #fff; }

    .btn-sm { padding: .4rem .9rem; font-size: .8rem; border-radius: 8px; }

    /* ── form footer ─────────────────────────────── */
    .form-footer {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
    }

    /* ── email table ─────────────────────────────── */
    .email-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .85rem;
    }
    .email-table thead tr {
        background: var(--sage-pale);
    }
    .email-table th {
        padding: .7rem 1rem;
        text-align: left;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--sage);
    }
    .email-table td {
        padding: .75rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .email-table tbody tr:last-child td { border-bottom: none; }
    .email-table tbody tr:hover { background: var(--sage-pale); }
    .action-group { display: flex; gap: .5rem; align-items: center; }

    /* ── tab toggle ──────────────────────────────── */
    .email-forms { position: relative; }
    .form-panel { display: none; }
    .form-panel.active { display: block; }

    /* ── animations ──────────────────────────────── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="cp-page">

    {{-- ── Page Header ── --}}
    <div class="cp-header">
        <h1>My Profile</h1>
        <span>Charity Account</span>
    </div>

    {{-- ── Alerts ── --}}
    @if(session()->has('message') && session()->get('status') != 200)
        <div class="cp-alert success">{{ session()->get('message') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="cp-alert danger">{{ session()->get('error') }}</div>
    @endif

    <div class="cp-grid">

        {{-- ══════ SIDEBAR ══════ --}}
        <aside class="cp-sidebar">
            <div class="avatar-ring">
                <img src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg"
                     alt="Profile picture">
            </div>
            <div class="sidebar-name">{{ auth('charity')->user()->name }}</div>
            <div class="sidebar-email">{{ auth('charity')->user()->email }}</div>

            <div class="sidebar-meta">
                <div class="sidebar-meta-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <div><div style="font-size:.7rem;color:var(--muted)">Town</div>
                    <strong>{{ auth('charity')->user()->town ?? '—' }}</strong></div>
                </div>
                <div class="sidebar-meta-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    <div><div style="font-size:.7rem;color:var(--muted)">Phone</div>
                    <strong>{{ auth('charity')->user()->number ?? '—' }}</strong></div>
                </div>
                <div class="sidebar-meta-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><polyline points="2 10 12 15 22 10"/></svg>
                    <div><div style="font-size:.7rem;color:var(--muted)">Postcode</div>
                    <strong>{{ auth('charity')->user()->post_code ?? '—' }}</strong></div>
                </div>
                <div class="sidebar-meta-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h18v13a2 2 0 01-2 2H5a2 2 0 01-2-2V3z"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
                    <div><div style="font-size:.7rem;color:var(--muted)">Account</div>
                    <strong>{{ auth('charity')->user()->account_name ?? '—' }}</strong></div>
                </div>
            </div>
        </aside>

        {{-- ══════ MAIN PANEL ══════ --}}
        <main class="cp-main">

            <form action="{{ route('charity_profileUpdate') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ── Personal Info ── --}}
                <div class="cp-card">
                    <div class="cp-card-header">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <h2>Personal Information</h2>
                    </div>
                    <div class="cp-card-body">
                        <div class="form-row cols-2">
                            <div class="field">
                                <label>Full Name</label>
                                <input type="text" name="name" id="name" placeholder="Full name" value="{{ auth('charity')->user()->name }}">
                            </div>
                            <div class="field">
                                <label>Phone</label>
                                <input type="text" name="phone" id="phone" placeholder="Phone number" value="{{ auth('charity')->user()->number }}">
                            </div>
                        </div>
                        <div class="form-row" style="margin-top:1rem">
                            <div class="field">
                                <label>Email Address</label>
                                <input type="email" name="email" id="email" placeholder="Email address" value="{{ auth('charity')->user()->email }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Address ── --}}
                <div class="cp-card">
                    <div class="cp-card-header">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <h2>Address</h2>
                    </div>
                    <div class="cp-card-body">
                        <div class="form-row" style="margin-bottom:1rem">
                            <div class="field">
                                <label>Address First Line</label>
                                <input type="text" name="houseno" id="houseno" placeholder="Start typing your postcode…" value="{{ auth('charity')->user()->address }}">
                            </div>
                        </div>
                        <div class="form-row cols-2" style="margin-bottom:1rem">
                            <div class="field">
                                <label>Address Second Line</label>
                                <input type="text" name="address_second_line" id="address_second_line" placeholder="Second line" value="{{ auth('charity')->user()->address_second_line }}" readonly>
                            </div>
                            <div class="field">
                                <label>Address Third Line</label>
                                <input type="text" name="address_third_line" id="address_third_line" placeholder="Third line" value="{{ auth('charity')->user()->address_third_line }}" readonly>
                            </div>
                        </div>
                        <div class="form-row cols-2">
                            <div class="field">
                                <label>Town</label>
                                <input type="text" name="town" id="town" placeholder="Town" value="{{ auth('charity')->user()->town }}" readonly>
                            </div>
                            <div class="field">
                                <label>Postcode</label>
                                <input type="text" name="postcode" id="postcode" placeholder="Postcode" value="{{ auth('charity')->user()->post_code }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Banking ── --}}
                <div class="cp-card">
                    <div class="cp-card-header">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                        <h2>Banking Details</h2>
                    </div>
                    <div class="cp-card-body">
                        <div class="form-row" style="margin-bottom:1rem">
                            <div class="field">
                                <label>Account Name</label>
                                <input type="text" name="account_name" id="account_name" placeholder="Account name" value="{{ auth('charity')->user()->account_name }}">
                            </div>
                        </div>
                        <div class="form-row cols-2">
                            <div class="field">
                                <label>Account Number</label>
                                <input type="text" name="account_number" id="account_number" placeholder="Account number" value="{{ auth('charity')->user()->account_number }}">
                            </div>
                            <div class="field">
                                <label>Sort Code</label>
                                <input type="text" name="account_sortcode" id="account_sortcode" placeholder="00-00-00" value="{{ auth('charity')->user()->account_sortcode }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Security ── --}}
                <div class="cp-card">
                    <div class="cp-card-header">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        <h2>Change Password</h2>
                    </div>
                    <div class="cp-card-body">
                        <div class="form-row cols-2">
                            <div class="field">
                                <label>New Password</label>
                                <input type="password" name="password" id="password" placeholder="Leave blank to keep current">
                            </div>
                            <div class="field">
                                <label>Confirm Password</label>
                                <input type="password" name="cpassword" id="cpassword" placeholder="Repeat new password">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end">
                    <button type="submit" class="btn-theme bg-primary" id="updateBtn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>

            {{-- ══════ EMAIL ACCOUNTS ══════ --}}
            <div class="cp-card">
                <div class="cp-card-header">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <h2>Linked Email Addresses</h2>
                </div>
                <div class="cp-card-body">

                    {{-- Status messages --}}
                    @if(session()->has('status') && session()->get('status') == 200)
                        <div class="cp-alert success" style="margin-bottom:1rem">{{ session()->get('message') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="cp-alert danger" style="margin-bottom:1rem">
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    {{-- Add / Edit forms --}}
                    <div class="email-forms" style="margin-bottom:1.5rem">

                        <form action="{{ route('charity.emailAccountStore') }}" method="POST" id="storeForm" class="form-panel active">
                            @csrf
                            <div class="form-row cols-2" style="align-items:flex-end">
                                <div class="field">
                                    <label>Add New Email</label>
                                    <input type="email" name="newemail" id="newemail" placeholder="new@example.com">
                                </div>
                                <div>
                                    <button type="submit" class="btn-theme bg-primary">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Add Email
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form action="{{ route('charity.emailAccountUpdate') }}" method="POST" id="updateForm" class="form-panel">
                            @csrf
                            <div class="form-row cols-2" style="align-items:flex-end">
                                <div class="field">
                                    <label>Update Email</label>
                                    <input type="email" name="upemail" id="upemail" placeholder="updated@example.com">
                                    <input type="hidden" name="userDetailId" id="userDetailId">
                                </div>
                                <div style="display:flex;gap:.6rem">
                                    <button type="submit" class="btn btn-clay">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Update
                                    </button>
                                    <button type="button" class="btn btn-ghost" id="cancelEdit">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Table --}}
                    <table class="email-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Email Address</th>
                                <th style="width:140px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\UserDetail::where('charity_id', auth('charity')->user()->id)->whereNotNull('email_verified_at')->get() as $data)
                            <tr>
                                <td style="color:var(--muted);font-size:.8rem">{{ $data->date }}</td>
                                <td>{{ $data->email }}</td>
                                <td>
                                    <div class="action-group">
                                        <button
                                            data-udid="{{ $data->id }}"
                                            data-email="{{ $data->email }}"
                                            class="btn btn-ghost btn-sm emaileditBtn">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Edit
                                        </button>
                                        <form action="{{ route('charity.emailDestroy', $data->id) }}" method="POST"
                                              style="display:inline"
                                              onsubmit="return confirm('Delete this email?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
            {{-- end email card --}}

        </main>
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

    /* ── Email edit toggle ── */
    const storeForm  = document.getElementById('storeForm');
    const updateForm = document.getElementById('updateForm');
    const cancelBtn  = document.getElementById('cancelEdit');

    document.querySelectorAll('.emaileditBtn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('upemail').value       = this.dataset.email;
            document.getElementById('userDetailId').value  = this.dataset.udid;
            storeForm.classList.remove('active');
            updateForm.classList.add('active');
        });
    });

    cancelBtn.addEventListener('click', function () {
        updateForm.classList.remove('active');
        storeForm.classList.add('active');
    });

    /* ── Auto-hide alerts after 4 s ── */
    setTimeout(function () {
        document.querySelectorAll('.cp-alert').forEach(function (el) {
            el.style.transition = 'opacity .5s';
            el.style.opacity    = '0';
            setTimeout(function () { el.remove(); }, 500);
        });
    }, 4000);

});
</script>
@endsection