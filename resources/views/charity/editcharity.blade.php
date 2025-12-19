@extends('layouts.admin')

@section('content')

<section class="px-4 py-5" id="addThisFormContainer">
    <div class="container">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
            <span class="iconify fs-3 text-primary" data-icon="fluent:contact-card-28-regular"></span>
            <h4 class="ms-3 mb-0 fw-bold text-dark">Edit Charity Profile</h4>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('charity.update', $users->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted small">Organization Details</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Charity Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $users->name }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $users->email }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="number" class="form-control" value="{{ $users->number }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" name="address" class="form-control mb-2" value="{{ $users->address }}" placeholder="Street">
                                <div class="row g-2">
                                    <div class="col-7">
                                        <input type="text" name="town" class="form-control" value="{{ $users->town }}" placeholder="Town">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="post_code" class="form-control" value="{{ $users->post_code }}" placeholder="Post Code">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 border-start-md">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted small">Financial & Security</h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Charity Registration Number</label>
                                <input type="text" name="acc_no" class="form-control" value="{{ $users->acc_no }}">
                            </div>

                            <div class="bg-light p-3 rounded border">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Bank Statement</label>
                                    <input type="file" name="bank_statement" id="bank_statement" class="form-control @error('bank_statement') is-invalid @enderror">
                                </div>
                            </div>


                            <div class="row g-2 mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Bank Account Name</label>
                                    <input type="text" name="account_name" class="form-control" value="{{ $users->account_name }}">
                                </div>
                                <div class="col-7">
                                    <label class="form-label fw-semibold">Account Number</label>
                                    <input type="text" name="account_number" class="form-control" value="{{ $users->account_number }}">
                                </div>
                                <div class="col-5">
                                    <label class="form-label fw-semibold">Sort Code</label>
                                    <input type="text" name="account_sortcode" class="form-control" value="{{ $users->account_sortcode }}">
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded border">
                                <p class="small text-muted mb-2"><i class="fas fa-info-circle me-1"></i> Leave password fields blank if you don't want to change it.</p>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                                </div>
                                <div>
                                    <label class="form-label fw-semibold">Confirm Password</label>
                                    <input type="password" name="cpassword" class="form-control" placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">Last updated: {{ $users->updated_at->diffForHumans() }}</small>
                        <div>
                            <a href="{{ route('charitylist')}}" class="btn btn-light px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script>
    $(document).ready(function () {



    });

</script>
@endsection
