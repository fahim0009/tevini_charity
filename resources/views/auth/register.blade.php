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
                    {{-- @error('password')
                        <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                            <small class="text-danger text-capitalize">
                                <strong>{{ $message }}</strong>
                            </small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror --}}
                        <form method="POST" class="form-custom" action="{{ route('register') }}">
                                @csrf

                            <div class="form-group">
                                <label for="">Account registration for:</label>
                                <select name="profile_type" id="profile_type"  class="form-control @error('profile_type') is-invalid @enderror" required>
                                    <option value="">Select personal or company*</option>
                                    <option value="Company" @if (old('profile_type') == 'Company') selected @endif >Company</option>
                                    <option value="Personal"  @if (old('profile_type') == 'Personal') selected @endif>Personal</option>
                                </select>
                                @error('profile_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            
                            <div @if (old('profile_type') == 'Company')  @else  id="companyDiv" @endif class="companyDiv">
                                <div class="form-group">
                                    <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}"  placeholder="Company Name" autocomplete="company_name" autofocus>

                                    @error('company_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror


                                </div>
                                <div class="form-group">
                                    <input id="company_last_name" type="text" class="form-control @error('company_last_name') is-invalid @enderror" name="company_last_name" value="{{ old('company_last_name') }}"  placeholder="Your Name" autocomplete="company_last_name" autofocus>

                                    @error('company_last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                            </div>
                            
                            <div @if (old('profile_type') == 'Personal')  @else  id="personalDiv" @endif class="personalDiv">
                                <div class="form-group">
                                    <select name="prefix_name" id="prefix_name"  class="form-control @error('prefix_name') is-invalid @enderror">
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  placeholder="Name" autocomplete="name" autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}"  placeholder="Surname" autocomplete="surname" autofocus>
                                    @error('surname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <script>
                                if (old('profile_type') == 'Company') {
                                    $('#companyDiv').show();
                                    $('#personalDiv').hide();
                                } else if (old('profile_type') == 'Personal') {
                                    $('#companyDiv').hide();
                                    $('#personalDiv').show();
                                }

                            </script>
                            


                            <div class="form-group">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}"  placeholder="Contact Number" required autocomplete="phone" maxlength="13" autofocus> 
                                {{-- <small>*Example:+440123456789</small> --}}
                                
                                <small>Example: 441234567890</small>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input id="houseno" type="text" class="form-control @error('houseno') is-invalid @enderror" name="houseno" value="{{ old('houseno') }}"  placeholder="Address Line 1" required autocomplete="houseno" autofocus>
                            </div>

                            <div class="form-group">
                                <input id="streetname" type="text" class="form-control @error('streetname') is-invalid @enderror" name="streetname" value="{{ old('streetname') }}"  placeholder="Address Line 2" required autocomplete="streetname" autofocus readonly>
                            </div>

                            
                            <div class="form-group">
                                <input id="address_third_line" type="text" class="form-control @error('address_third_line') is-invalid @enderror" name="address_third_line" value="{{ old('address_third_line') }}"  placeholder="Address Line 3" required autocomplete="address_third_line" autofocus readonly>
                            </div>


                            <div class="form-group">

                                <input id="town" type="text" class="form-control @error('town') is-invalid @enderror" name="town" value="{{ old('town') }}"  placeholder="Town" required autocomplete="town" autofocus readonly>
                
                            </div>
                            
                            <div class="form-group">
                
                                <input id="postcode" type="text" class="form-control @error('postcode') is-invalid @enderror" name="postcode" value="{{ old('postcode') }}"  placeholder="Post Code" required autocomplete="postcode" autofocus readonly>
                
                            </div>
                            
                            <div class="form-group">
                                <input id="email" type="email"  placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                
                
                            </div>
                            <div class="form-group">
                
                                <input id="password" type="password" placeholder="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" value="">
                                @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                <div id="iconsPass-0" class="pass-icons-container" style="top: 12.5px;"></div>
                            </div>
                            <div class="form-group">
                
                                <input id="password-confirm" type="password" class="form-control  @error('password') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password"  placeholder="Confirm password" value="">
                                @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>

                            <div class="col-lg-12 mt-3">
                                <p class="para mb-3 text-muted fs-6 ">
                                    <input type="checkbox" class="me-2" required>I agree to the <a href="{{route('terms')}}" style="text-decoration: none;color:#212529"> Terms & Conditions. </a><br>
                                </p>
                            </div>



                            <div class="form-group">
                                <button class="btn-theme bg-primary d-block text-center mx-0 w-100" type="submit" id="submitBtn">
                                    Sign up
                                    <span id="spinner" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true" style="display: none;"></span>
                                </button>
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

@section('script')
<script src="https://cdn.jsdelivr.net/npm/@ideal-postcodes/address-finder-bundled@4"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        IdealPostcodes.AddressFinder.watch({
            apiKey: "ak_lt4ke30geFynIWbUB7nPMdpkvxGcP",
            outputFields: {
            line_1: "#houseno",
            line_2: "#streetname",
            line_3: "#address_third_line",
            post_town: "#town",
            postcode: "#postcode"
        }
    });
});

$(function() {
    $('#personalDiv').hide(); 
    $('#companyDiv').hide(); 
    $('#profile_type').change(function(){
        if($('#profile_type').val() == 'Personal') {
            $('#personalDiv').show(); 
            $('.personalDiv').show(); 
            $('#companyDiv').hide(); 
            $('.companyDiv').hide(); 
        } else {
            $('#personalDiv').hide(); 
            $('.personalDiv').hide(); 
            $('#companyDiv').show(); 
            $('.companyDiv').show(); 
        } 
    });
});

$('#submitBtn').on('click', function() {
    $(this).prop('disabled', true);
    $('#spinner').show();
    $(this).closest('form').submit();
});

$('form').on('submit', function() {
    $('#spinner').show();
});

$(document).ajaxComplete(function() {
    $('#spinner').hide();
});
</script>
@endsection
