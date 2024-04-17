@extends('frontend.layouts.user')
@section('content')


<section class="auth py-4">
    <div class="container">
        <div class="row">
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
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data" >
                    @csrf
                <div class="row">
                    <div class="col-lg-3 py-5 text-center flex-column d-flex align-items-center">
                        <img class="img-fluid mt-3 mb-2" width="150px" src="@if(Auth::user()->photo){{asset('images/'.Auth::user()->photo)}} @else https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg @endif">
                        
                        <input type="file" id="image" name="image" class="txt-theme txt-secondary fs-14 my-2">
                    </div>
                    
                    <div class="col-lg-9"> 





                        


                        
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center">
                                        <select name="prefix_name" id="prefix_name"  class="form-control">
                                            <option value="">Please Select</option>
                                            <option value="Mr" @if (Auth::user()->prefix_name == "Mr") selected @endif>Mr</option>
                                            <option value="Mrs" @if (Auth::user()->prefix_name == "Mrs") selected @endif>Mrs</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6  ">
                                <div class="form-group mb-3">
                                    <label for="">Name</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" placeholder="first name" name="name" id="name" value="{{ Auth::user()->name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Surname</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" name="surname" id="surname" value="{{ Auth::user()->surname }}" placeholder="surname">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Phone</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" placeholder="enter phone number" id="phone" name="phone" value="{{ Auth::user()->phone }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Email</label>
                                    <div class="d-flex align-items-center">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="enter email id" value="{{ Auth::user()->email }}">
                                    </div>
                                </div>
                            </div>

                                                  
                            
                            <div class="col-lg-4">
                                <div class="form-group mb-3">
                                    <label for="houseno">Address First Line</label>
                                    <div class="d-flex align-items-center">
                                        <input id="houseno" type="text" class="form-control @error('houseno') is-invalid @enderror" name="houseno" value="{{ Auth::user()->houseno }}"  placeholder="Address Line 1" required autocomplete="houseno" autofocus>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-3">
                                    <label for="">Address Second Line</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" placeholder="Address Second Line" name="street" id="street" value="{{ Auth::user()->street }}" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-3">
                                    <label for="">Address Third Line</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" placeholder="Address Third Line" name="address_third_line" id="address_third_line" value="{{ Auth::user()->address_third_line }}" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Town</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" placeholder="enter town" name="town" id="town" value="{{ Auth::user()->town }}" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Postcode</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Enter postcode" value="{{ Auth::user()->postcode }}" readonly="readonly">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Password</label>
                                    <div class="d-flex align-items-center">
                                        <input type="password" id="password" name="password" class="form-control" placeholder="password" >
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

                            
                        </div>

                            <div class="col-lg-12">
                                <div class="form-group mt-4">
                                    <button class="btn-theme bg-primary updateBtn" id="updateBtn" type="submit">Update profile</button>
                                    {{-- <button class="btn-theme bg-primary editBtn" id="editBtn">Edit profile</button> --}}

                                </div>
                            </div>



                    </div>
                   
                </div>
            </form>
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
            apiKey: "ak_lt4ocv0eHLLo4meBRGHWK4HU0SBxa",
            outputFields: {
            line_1: "#houseno",
            line_2: "#street",
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

    $(document).ready(function () {

        // $(".updateBtn").hide();


        // $("body").delegate(".editBtn","click",function(event){
        //     event.preventDefault();
        //     $("#prefix_name").attr("readonly", false);
        //     $("#name").attr("readonly", false);
        //     $("#surname").attr("readonly", false);
        //     $("#phone").attr("readonly", false);
        //     $("#houseno").attr("readonly", false);
        //     // $("#street").attr("readonly", false);
        //     // $("#town").attr("readonly", false);
        //     // $("#postcode").attr("readonly", false);
        //     $("#email").attr("readonly", false);
        //     $("#password").attr("readonly", false);
        //     $("#cpassword").attr("readonly", false);
        //     $("#editBtn").hide();
        //     $("#updateBtn").show();
        // });

})

</script>



@endsection
