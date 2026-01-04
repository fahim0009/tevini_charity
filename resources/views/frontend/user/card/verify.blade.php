@extends('frontend.layouts.user')
@section('content')

<div class="content d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm mt-4" style="background-color:#fdf3ee">
            <div class="card-body p-4 p-md-5">
                
                <div class="text-center mb-4">
                    <div class="icon-circle bg-light-primary mb-3 mx-auto">
                        <i class="fas fa-mobile-alt fa-2x text-primary"></i>
                    </div>
                    <h3 class="fw-bold">Mobile Verification</h3>
                    <p class="text-muted small">
                        Please enter the 6-digit code sent to <br>
                        <span class="text-dark fw-bold">+44XXXXXX{{$MobileLstDgt}}</span>
                    </p>
                </div>

                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert">
                        <small>{{ session()->get('success') }}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-4" role="alert">
                        <small>{{ session()->get('error') }}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('send.sms') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="Code" class="form-label small fw-bold text-uppercase tracking-wider">Verification Code</label>
                        <input type="text" 
                               id="Code" 
                               name="Code" 
                               class="form-control form-control-lg text-center fw-bold" 
                               placeholder="0 0 0 0 0 0" 
                               maxlength="6" 
                               style="letter-spacing: 0.5rem;"
                               required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            Verify & Continue
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">Didn't receive the code?</p>
                    <a href="{{route('mobileVerify')}}" class="btn btn-link btn-sm text-decoration-none fw-bold">
                        <i class="fas fa-redo me-1"></i> Resend SMS
                    </a>
                </div>

            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('user.dashboard')}}" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

<style>
    /* Professional styling additions */
    .bg-light-primary { background-color: #eef2ff; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.25 margin-left: rgba(78, 115, 223, 0.25); }
    .tracking-wider { letter-spacing: 0.05em; }
    .card { border-radius: 15px; }
    .btn-primary { background-color: #4e73df; border: none; padding: 12px; }
    .btn-primary:hover { background-color: #2e59d9; }
</style>

@endsection