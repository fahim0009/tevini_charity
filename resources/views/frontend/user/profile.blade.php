@extends('frontend.layouts.user')
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
                    <img class="img-fluid mt-3 mb-2" width="150px" src="@if(Auth::user()->photo){{asset('images/'.Auth::user()->photo)}} @else https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg @endif">
                     
                    {{-- <a href="#" class="txt-theme txt-secondary fs-14 my-2">Update profile picture</a> --}}
                    <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data" >
                        @csrf
                    <input type="file" id="image" name="image" class="txt-theme txt-secondary fs-14 my-2">
                </div>

                <div class="col-lg-9 border-left-lg  pt-3  ">
                    <div class="col-lg-11 mx-auto">
                        


                            <div class="row mt-4">
                                <div class="col-lg-6  ">
                                    <div class="form-group mb-3">
                                        <label for="">Name</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="first name" name="name" id="name" value="{{ Auth::user()->name }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Surname</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" name="surname" id="surname" value="{{ Auth::user()->surname }}" placeholder="surname" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Phone</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="enter phone number" id="phone" name="phone" value="{{ Auth::user()->phone }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Email</label>
                                        <div class="d-flex align-items-center">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="enter email id" value="{{ Auth::user()->email }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-lg-6" style="display: none">
                                    <div class="form-group mb-3">
                                        <label for="">Image</label>
                                        <div class="d-flex align-items-center">
                                            <input type="file" id="image" name="image" class="form-control">
                                        </div>
                                    </div>
                                </div> --}}
                                
                                
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">House Number</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="enter address" name="houseno" id="houseno" value="{{ Auth::user()->houseno }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Street</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" placeholder="Street number" name="street" id="street" value="{{ Auth::user()->street }}" readonly="readonly">
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
                                            <input type="text" class="form-control" id="postcode" name="postcode" placeholder="enter postcode" value="{{ Auth::user()->postcode }}" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Password</label>
                                        <div class="d-flex align-items-center">
                                            <input type="password" id="password" name="password" class="form-control" placeholder="password" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="">Confirm password</label>
                                        <div class="d-flex align-items-center">
                                            <input type="password" id="cpassword" name="cpassword" class="form-control" placeholder="Confirm password" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mt-4">
                                        <button class="btn-theme bg-primary updateBtn" id="updateBtn" type="submit">Update profile</button>
                                        <button class="btn-theme bg-primary editBtn" id="editBtn">Edit profile</button>

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


<script type="text/javascript">
  $(document).ready(function() {
      $("#profileinfo").addClass('active');
      $("#profileinfo").addClass('is-expanded');
      $("#profile").addClass('active');
  });
</script>


<script type="text/javascript">

    $(document).ready(function () {

        $(".updateBtn").hide();


        $("body").delegate(".editBtn","click",function(event){
            event.preventDefault();
            $("#name").attr("readonly", false);
            $("#surname").attr("readonly", false);
            $("#phone").attr("readonly", false);
            $("#houseno").attr("readonly", false);
            $("#street").attr("readonly", false);
            $("#town").attr("readonly", false);
            $("#postcode").attr("readonly", false);
            $("#email").attr("readonly", false);
            $("#password").attr("readonly", false);
            $("#cpassword").attr("readonly", false);
            $("#editBtn").hide();
            $("#updateBtn").show();
        });

})

</script>
@endsection
