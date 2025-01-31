@extends('frontend.layouts.charity')
@section('content')


<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                My profile
            </div>

            @if(session()->has('message'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('message') }}</div>
            </div>
        </section>
        @endif
        @if(session()->has('error'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
            </div>
        </section>
        @endif
            
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-12">
            <div class="row ">
                <div class="col-lg-3 py-5 text-center flex-column d-flex align-items-center">
                    {{-- <img src="../dashboard/images/profile.png" class="img-fluid mt-3 mb-2" alt=""> --}}
                    <img class="img-fluid mt-3 mb-2" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
                     
                    {{-- <a href="#" class="txt-theme txt-secondary fs-14 my-2">Update profile picture</a> --}}
                    {{-- <input type="file" id="image" name="image" class="txt-theme txt-secondary fs-14 my-2"> --}}
                </div>

                <div class="col-lg-9 border-left-lg  pt-3  ">
                    <div class="col-lg-11 mx-auto">
                        
                    <form action="{{ route('charity_profileUpdate') }}" method="POST" enctype="multipart/form-data" >
                        @csrf
                            <div class="row mt-4">
                                <div class="col-lg-6  ">
                                    <div class="form-group mb-3">
                                        <label for="">Name</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="first name" name="name" id="name" value="{{ auth('charity')->user()->name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Phone</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="enter phone number" id="phone" name="phone" value="{{ auth('charity')->user()->number }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label for="">Email</label>
                                        <div class="d-flex align-items-center">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="enter email id" value="{{ auth('charity')->user()->email }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                                             
                                
                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label for="">Address First Line</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="enter address" name="houseno" id="houseno" value="{{ auth('charity')->user()->address }}">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label for="">Address Second Line</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="Address Second Line" name="address_second_line" id="address_second_line" value="{{ auth('charity')->user()->address_second_line }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group mb-3">
                                        <label for="">Address Third Line</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="Address Third Line" name="address_third_line" id="address_third_line" value="{{ auth('charity')->user()->address_third_line }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                 <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Town</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="enter town" name="town" id="town" value="{{ auth('charity')->user()->town }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Postcode</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" id="postcode" name="postcode" placeholder="enter postcode" value="{{ auth('charity')->user()->post_code }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label for="">Account Name</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Enter account name" value="{{ auth('charity')->user()->account_name }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Account Number</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Enter account number" value="{{ auth('charity')->user()->account_number }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Account Sortcode</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" id="account_sortcode" name="account_sortcode" placeholder="Enter account sortcode" value="{{ auth('charity')->user()->account_sortcode }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Password</label>
                                        <div class="d-flex align-items-center">
                                            <input type="password" id="password" name="password" class="form-control" placeholder="password">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Confirm password</label>
                                        <div class="d-flex align-items-center">
                                            <input type="password" id="cpassword" name="cpassword" class="form-control" placeholder="Confirm password">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mt-4">
                                        <button class="btn-theme bg-primary updateBtn" id="updateBtn" type="submit">Update profile</button>
                                        {{-- <button class="btn-theme bg-primary editBtn" id="editBtn">Edit profile</button> --}}

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                  
                 
                </div> 
            </div>
        </div>
    </div>
</div>



  @endsection
@section('script')

<script src="https://cdn.jsdelivr.net/npm/@ideal-postcodes/address-finder-bundled@4"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        IdealPostcodes.AddressFinder.watch({
            apiKey: "ak_lt4ke30geFynIWbUB7nPMdpkvxGcP",
            outputFields: {
            line_1: "#houseno",
            line_2: "#address_second_line",
            line_3: "#address_third_line",
            post_town: "#town",
            postcode: "#postcode"
        }
    });
});
</script>

<script type="text/javascript">
  $(document).ready(function() {
      $("#profileinfo").addClass('active');
      $("#profileinfo").addClass('is-expanded');
      $("#profile").addClass('active');
  });
</script>


<script type="text/javascript">


</script>
@endsection
