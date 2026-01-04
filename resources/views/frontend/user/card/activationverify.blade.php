@extends('frontend.layouts.user')
@section('content')

<div class="content d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="card border-0 shadow-lg" style="border-radius: 1.25rem;">
                    <div class="card-body p-4 p-md-5" style="background-color: #fdf3ee">
                        
                        <div class="text-center mb-4">
                            <div class="verification-icon-badge mb-3">
                                <i class="fas fa-shield-alt fa-3x text-primary"></i>
                            </div>
                            <h3 class="fw-bold h4">Mobile Verification</h3>
                            <p class="text-muted">
                                We've sent a verification code to <br>
                                <span class="text-dark fw-bold">+44XXXXXX{{$MobileLstDgt}}</span>
                            </p>
                        </div>

                        @if(session()->has('success'))
                            <div class="alert alert-success border-0 small text-center mb-4" id="successMessage">
                                <i class="fas fa-check-circle me-1"></i> {{ session()->get('success') }}
                            </div>
                        @endif

                        @if(session()->has('error'))
                            <div class="alert alert-danger border-0 small text-center mb-4" id="errMessage">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session()->get('error') }}
                            </div>
                        @endif

                        <form action="{{ route('activation.sms') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="Code" class="form-label small fw-bold text-uppercase text-muted tracking-wider">Enter 6-Digit Code</label>
                                <input type="text" 
                                       id="Code" 
                                       name="Code" 
                                       class="form-control form-control-lg text-center fw-bold otp-input" 
                                       placeholder="······" 
                                       maxlength="6" 
                                       required 
                                       autocomplete="one-time-code">
                                <div class="form-text text-center mt-2">
                                    Input the code to get and change your PIN.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm py-3">
                                    Confirm & Verify
                                </button>
                                <a href="{{route('activationVerify')}}" class="btn btn-light btn-sm mt-2 text-primary fw-bold">
                                    <i class="fas fa-sync-alt me-1"></i> Resend Code
                                </a>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for the badge icon */
    .verification-icon-badge {
        background: #f0f7ff;
        width: 80px;
        height: 80px;
        line-height: 80px;
        border-radius: 50%;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Modern Input Styling */
    .otp-input {
        letter-spacing: 0.5rem;
        font-size: 1.5rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        border-radius: 0.75rem;
    }

    .otp-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1);
        background-color: #fff;
    }

    .tracking-wider {
        letter-spacing: 0.08em;
    }

    /* Button Styling */
    .btn-primary {
        background-color: #4e73df;
        border: none;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        transform: translateY(-1px);
    }
</style>

@endsection

@section('script')
<script>
    // Focus the input automatically on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('Code').focus();
    });
</script>
@endsection