@extends('frontend.layouts.home')
@section('content')


<section class="auth py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-center mt-5">
                <img src="{{ asset('assets/front/images/logo.svg') }}" class="mx-auto">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="row">
                    <div class="col-lg-8 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/images/log in page 1.svg') }}" alt="" class="w-100">
                    </div>
                    <div class="col-lg-4"> 
                        <div class="title mb-5">Create an account</div>
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
                        <form method="POST" class="form-custom" action="{{ route('register') }}">
                                @csrf




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

                            <div class="col-lg-12 mt-3">
                                <p class="para mb-3 text-muted fs-6 ">
                                    <input type="checkbox" class="me-2" required>I agree to the <a href="{{route('terms')}}" style="text-decoration: none;color:#212529"> Terms & Conditions. </a><br>
                                </p>
                            </div>



                            <div class="form-group">
                                <button class="btn-theme bg-primary d-block text-center mx-0 w-100">Sign up</button>
                            </div>


                            <div class="form-group d-flex justify-content-center">
                                <span class="txt-primary fs-20 me-2 ">or</span>
                                 <a href="{{ route('login')}}" class="theme-link"> log into another account</a>
                            </div>
                        </form>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</section> 






@endsection
