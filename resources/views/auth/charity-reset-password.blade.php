@extends('frontend.layouts.master')

@section('content')
<style>
    .auth-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .auth-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #edf2f7;
        padding: 1.5rem;
    }
    .auth-header h4 {
        margin-bottom: 0;
        font-weight: 700;
        color: #334155;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }
    .btn-reset {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-reset:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
    }
    .input-group-text {
        background-color: transparent;
        border-right: none;
    }
    .form-control {
        border-radius: 8px;
        padding: 0.6rem 0.75rem;
    }
    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25 dark-gray;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card auth-card">
                <div class="auth-header text-center">
                    <h4>{{ __('Reset Password') }}</h4>
                    <p class="text-muted small mb-0 mt-1">Please enter your new credentials below</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('charity.password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-4">
                            <label for="email" class="form-label text-uppercase small">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                <input id="email" type="email" class="form-control bg-light @error('email') is-invalid @enderror" 
                                       name="email" value="{{ $email ?? old('email') }}" required readonly>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label text-uppercase small">{{ __('New Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" placeholder="••••••••" required autofocus>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label text-uppercase small">{{ __('Confirm Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle text-muted"></i></span>
                                <input id="password-confirm" type="password" class="form-control" 
                                       name="password_confirmation" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 pt-2">
                            <button type="submit" class="btn btn-success btn-reset shadow-sm">
                                <i class="fas fa-save me-2"></i> {{ __('Update Password') }}
                            </button>
                            <a href="{{ url('/') }}" class="btn btn-link btn-sm text-decoration-none text-muted mt-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Homepage
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection