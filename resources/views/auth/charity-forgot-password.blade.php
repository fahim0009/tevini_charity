@extends('frontend.layouts.master')

@section('content')
<style>
    .auth-card {
        border: none;
        border-radius: 16px; /* Slightly rounder for a modern look */
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background: #ffffff;
    }
    .auth-header {
        padding: 2.5rem 2rem 1rem;
        text-align: center;
    }
    .auth-header h3 {
        font-weight: 800;
        color: #1a202c;
        letter-spacing: -0.025em;
    }
    .auth-header p {
        color: #718096;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    /* Icon Styling */
    .icon-wrapper {
        width: 64px;
        height: 64px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    .icon-wrapper svg {
        width: 32px;
        height: 32px;
    }
    /* Form Styling */
    .form-label {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .form-control {
        padding: 0.8rem 1rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease-in-out;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .btn-primary {
        background-color: #2563eb;
        border: none;
        padding: 0.8rem;
        border-radius: 10px;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card">
                <div class="auth-header">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3>{{ __('Reset Password') }}</h3>
                    <p>{{ __('No worries, we\'ll send you instructions to get back into your account.') }}</p>
                </div>

                <div class="card-body px-4 pb-5">
                    @if (session('status'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('charity.password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold text-muted">{{ __('Email Address') }}</label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   placeholder="e.g. alex@company.com"
                                   required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn-theme bg-primary d-block text-center mx-0 w-100">
                                {{ __('Send Reset Link') }}
                            </button>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center">
                                <svg class="me-1" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                {{ __('Back to Login') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection