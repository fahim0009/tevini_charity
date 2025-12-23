
@extends('frontend.layouts.home')
@section('content')




<section class="auth py-4">
  <div class="container">
     
      <div class="row mt-5">
          <div class="col-lg-10 mx-auto">
              <div class="row">
                  <div class="col-lg-8 d-flex align-items-center justify-content-center">
                      <img src="{{ asset('assets/front/images/log in page 1.svg') }}" alt="" class="w-100">
                  </div>
                  <div class="col-lg-4"> 
                     <div class="d-flex flex-column align-items-center">
                          <img src="{{ asset('assets/front/images/logo.svg') }}" width="190px"  class=" mx-auto d-none d-sm-block  d-lg-block"> 
                     </div>
                     <br> 
                        <form method="POST" action="{{ route('charity.login') }}" class="form-custom mt-5">
                          @csrf
                          <div class="title text-center mb-5 txt-secondary">Charity Login</div>
                          
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-check-circle me-2 mt-1"></i>
                                        <div>{{ session('status') }}</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session()->has('error') || isset($message))
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                                        <div>{{ session()->get('error') ?? $message }}</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif



                          <div class="form-group">
                            <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autofocus placeholder="Email or Account Number">
                          </div>
                          <div class="form-group">
                            <input id="password" type="password"  placeholder="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                          </div>
                          <div class="form-group d-flex justify-content-center">
                              <span class="txt-primary fs-20 me-2 ">or</span>
                              @if (Route::has('charity.password.request'))
                                      <a class="theme-link" href="{{ route('charity.password.request') }}">
                                          {{ __('Forgot Your Password?') }}
                                      </a>
                              @endif
                          </div>
                          <br>
                          <div class="form-group">
                              <button type="submit" class="btn-theme bg-primary d-block text-center mx-0 w-100">Login </button>
                          </div>
                          <div class="form-group d-flex justify-content-center">
                              <span class="txt-primary fs-20 me-2 ">or</span>
                               <a href="{{ route('charity.register') }}" class="theme-link"> Apply for an account</a>
                          </div>
                      </form>
                  </div>
                 
              </div>
          </div>
      </div>
  </div>
</section> 




@endsection
