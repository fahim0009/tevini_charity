
@extends('frontend.layouts.home')
@section('content')


<section class="auth py-4">
    <div class="container">
       
        <div class="row my-5">
            <div class="col-lg-10 mx-auto authBox">
                <div class="row">
                    
                    <div class="title text-center mb-5 txt-secondary">Create Charity Account</div>
                        
                        @if($errors->any())
                        <p class="alert alert-success"> {{$errors->first()}}</p>
                        @endif
                    <h4>
                        
                        
                    </h4>
                    <div class="row">
                        <div class="col-lg-10  mx-auto">
                            <div class="pagetitle pb-2 mb-2">
                                Charity info
                            </div>
                            <form method="POST" action="{{ route('charity.registration') }}"  enctype="multipart/form-data">
                                @csrf

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="name" style="font-size: 23px">Charity Name </label>
                                </div>
                                <div class="col-8">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Charity Name" autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="phone" style="font-size: 23px">Charity Number </label>
                                </div>
                                <div class="col-8">
                                    <input id="phone" type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" placeholder="Charity Number" autofocus>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-7">
                                    <label for="postcode" style="font-size: 23px">Upload copy of bank statement for varification</label>
                                </div>
                                <div class="col-5">
                                    <input id="bank_statement" type="file" class="form-control @error('bank_statement') is-invalid @enderror" name="bank_statement" value="{{ old('bank_statement') }}" autocomplete="bank_statement" placeholder="Post code" autofocus>
                                    @error('bank_statement')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="pagetitle pb-2 mb-2">
                                Address
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="address" style="font-size: 23px">Address First Line </label>
                                </div>
                                <div class="col-8">
                                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address" placeholder="Address First Line" autofocus>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="address_second_line" style="font-size: 23px">Address Second Line </label>
                                </div>
                                <div class="col-8">
                                    <input id="address_second_line" type="text" class="form-control @error('address_second_line') is-invalid @enderror" name="address_second_line" value="{{ old('address_second_line') }}" autocomplete="address_second_line" placeholder="Address Second Line" autofocus readonly>
                                    @error('address_second_line')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="address_third_line" style="font-size: 23px">Address Second Line </label>
                                </div>
                                <div class="col-8">
                                    <input id="address_third_line" type="text" class="form-control @error('address_third_line') is-invalid @enderror" name="address_third_line" value="{{ old('address_third_line') }}"  autocomplete="address_third_line" placeholder="Address Third Line" autofocus readonly>
                                    @error('address_third_line')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="town" style="font-size: 23px">Town </label>
                                </div>
                                <div class="col-8">
                                    <input id="town" type="text" class="form-control @error('town') is-invalid @enderror" name="town" value="{{ old('town') }}"  autocomplete="town" placeholder="Town" autofocus readonly>
                                    @error('town')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="postcode" style="font-size: 23px">Post code </label>
                                </div>
                                <div class="col-8">
                                    <input id="postcode" type="text" class="form-control @error('postcode') is-invalid @enderror" name="postcode" value="{{ old('postcode') }}" autocomplete="postcode" placeholder="Post code" autofocus readonly>
                                    @error('postcode')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="pagetitle pb-2 mb-2">
                                Credentials
                            </div>


                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="email" style="font-size: 23px"> Email </label>
                                </div>
                                <div class="col-8">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="password" style="font-size: 23px">Password </label>
                                </div>
                                <div class="col-8">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="password" placeholder="Password" autofocus>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-4">
                                    <label for="confirm_password" style="font-size: 23px">Confirm Password </label>
                                </div>
                                <div class="col-8">
                                    <input id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" required autocomplete="confirm_password" placeholder="Confirm Password" autofocus>
                                    @error('confirm_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <p class="para mb-3 text-muted fs-6 ">
                                    <input type="checkbox" class="me-2" required>I agree to the <a href="{{route('terms')}}" style="text-decoration: none;color:#212529"> Terms & Conditions. </a><br>
                                </p>
                            </div>
                            

                            <div class="form-group  text-center">
                                <button type="submit" class="btn-theme bg-primary text-center mx-0 ">Sign up</button>
                            </div>


                        </form>

                        </div>
                    </div>


                    <div class="col-lg-12"> 

                        <div class="form-group d-flex justify-content-center">
                            <span class="txt-primary fs-20 me-2 ">or</span>
                             <a href="{{ route('charity_loginshow')}}" class="theme-link"> log into another account</a>
                        </div>

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
            line_1: "#address",
            line_2: "#address_second_line",
            line_3: "#address_third_line",
            post_town: "#town",
            postcode: "#postcode"
        }
    });
});
</script>
@endsection
