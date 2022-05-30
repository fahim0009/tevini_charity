@extends('frontend.layouts.master')


@section('content')



<section class="login-panel contactForm">

    <form method="POST" action="{{ route('register') }}">
        @csrf

         <div class="box">



                @error('name')
                    <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                        <small class="text-danger text-capitalize">
                            <strong>{{ $message }}</strong>
                        </small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                @error('email')
                    <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                        <small class="text-danger text-capitalize">
                            <strong>{{ $message }}</strong>
                        </small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                @error('password')
                    <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                        <small class="text-danger text-capitalize">
                            <strong>{{ $message }}</strong>
                        </small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror

        <h3 class="text-center">Create Account</h3> <hr>
        <div>
            <div class="form-group">

                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  placeholder="Name" required autocomplete="name" autofocus>

            </div>
            
                       
            <div class="form-group">

                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}"  placeholder="Contact Number" required autocomplete="phone" autofocus>

            </div>
            
            
                  <div class="form-group">

                <input id="houseno" type="text" class="form-control @error('houseno') is-invalid @enderror" name="houseno" value="{{ old('houseno') }}"  placeholder="House Number" required autocomplete="houseno" autofocus>

            </div>
 
                  <div class="form-group">

                <input id="streetname" type="text" class="form-control @error('streetname') is-invalid @enderror" name="streetname" value="{{ old('streetname') }}"  placeholder="Street name" required autocomplete="streetname" autofocus>

            </div>
            
                  <div class="form-group">

                <input id="town" type="text" class="form-control @error('town') is-invalid @enderror" name="town" value="{{ old('town') }}"  placeholder="Town" required autocomplete="town" autofocus>

            </div>
            
                  <div class="form-group">

                <input id="postcode" type="text" class="form-control @error('postcode') is-invalid @enderror" name="postcode" value="{{ old('postcode') }}"  placeholder="Post Code" required autocomplete="postcode" autofocus>

            </div>
            
            <div class="form-group">
                <input id="email" type="email"  placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">


            </div>
            <div class="form-group">

                <input id="password" type="password" placeholder="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">


                <div id="iconsPass-0" class="pass-icons-container" style="top: 12.5px;"></div>
            </div>
            <div class="form-group">

                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"  placeholder="Confirm password">
            </div>
        </div>

        <div class='d-flex justify-content-between mt-3'>
                <button class="btn btn-custom w-100">Sign Up</button>
        </div>
    </div>
    </form>
    

</section>




@endsection
