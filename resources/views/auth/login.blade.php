
@extends('frontend.layouts.master')
@section('content')



<section class="login-panel contactForm logBg">
    <form method="POST" action="{{ route('login') }}">
        @csrf
      <div class="box">

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

          <h3 class="text-center">Login Here</h3> <hr>

          <div>
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
                <div id="iconsPass-0" class="pass-icons-container" style="top: 12.5px;"></div>
              </div>
          </div>
               <p class="mb-0 text-muted">Not yet register ? <a href="{{ route('register') }}" class="text-decoration-none">Sign Up</a></p>
          <div class='d-flex justify-content-between mt-3'>
                  <button class="btn btn-custom w-100">Login </button>
          </div>
          <div class='d-flex justify-content-center mt-2'>
            @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
            @endif
        </div>
      </div>
    </form>
  </section>



@endsection
