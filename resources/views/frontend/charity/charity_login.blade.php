
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
                          @if(session()->has('error'))
                              <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                                  {{ session()->get('error') }}
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                          @endif
                          @error('email')
                                  <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                                      <strong>{{ $message }}</strong>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                  </div>
                          @enderror
                          @error('password')
                                  <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                                      <strong>{{ $message }}</strong>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                  </div>
                          @enderror



                          <div class="form-group">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
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
                              @if (Route::has('password.request'))
                                      <a class="theme-link" href="{{ route('password.request') }}">
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
                               <a href="{{ route('register') }}" class="theme-link"> Apply for an account</a>
                          </div>
                      </form>
                  </div>
                 
              </div>
          </div>
      </div>
  </div>
</section> 




@endsection
