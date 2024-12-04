@extends('layouts.admin')
@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')

    <div class="rightSection">
        <div class="dashboard-content">
            <section class="profile purchase-status">
                <div class="title-section">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    <div class="mx-2">User Profiles ({{$donor_id}})</div>
                </div>
            </section>
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
            <section class="px-4">
                <div class="row my-3">

                  <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data" >
                    @csrf
                    <div class="col-md-12 ">
                        <div class="row mx-auto">

                            <div class="col-md-3 border-right">
                                <div class="d-flex flex-column align-items-center text-center p-3 py-5" style="position:relative">
                                    <img class="rounded-circle mt-5" width="150px"
                                        src="@if($profile_data->photo){{asset('images/'.$profile_data->photo)}}@else https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg @endif"><span
                                        class="font-weight-bold">{{ $profile_data->name }}</span>
                                        <span class="text-black-50">{{$profile_data->email }}</span>
                                        {{-- <span> Balance : {{number_format($donorUpBalance, 2) }}</span> --}}
                                        <span> Balance : {{number_format($userTransactionBalance->balance, 2) }}</span>
                                        <span> Gift in Previous Year: {{ $totalamount }}</span>
                                        <span>  Gift in Current Year: {{$currentyramount }}</span>
                                        {{-- <input type="file" id="image" name="image" class="profile-upload"> --}}
                                </div>
                            </div>
                            <div class="col-md-5 border-right">
                                <div class="p-3 py-4 text-muted">
                                    <div class="row mt-2">
                                        <div class="col-md-6"><label><small>Name</small></label><input type="text"
                                                class="form-control" placeholder="first name" name="name" id="name" value="{{ $profile_data->name }}" readonly="readonly"></div>

                                        <div class="col-md-6"><label><small>Surname</small></label><input
                                                type="text" class="form-control" name="surname" id="surname" value="{{ $profile_data->surname }}" placeholder="surname" readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12 mb-3"><label><small>Mobile
                                                    Number</small></label><input type="text" class="form-control"
                                                placeholder="enter phone number" id="phone" name="phone" value="{{ $profile_data->phone }}" readonly="readonly"></div>

                                        <div class="col-md-12 mb-3"><label><small>House No</small></label>
                                          <input type="text" class="form-control" placeholder="enter address" name="houseno" id="houseno" value="{{ $profile_data->houseno }}" readonly="readonly">
                                          </div>

                                              <div class="col-md-12 mb-3"><label><small>Street</small></label>
                                          <input type="text" class="form-control" placeholder="Street number" name="street" id="street" value="{{ $profile_data->street }}" readonly="readonly">
                                          </div>


                                        <div class="col-md-12 mb-3"><label><small>Town</small></label><input type="text" class="form-control" placeholder="enter town" name="town" id="town" value="{{ $profile_data->town }}" readonly="readonly"></div>

                                        <div class="col-md-12 mb-3"><label><small>Postcode</small></label><input
                                                type="text" class="form-control" id="postcode" name="postcode" placeholder="enter postcode" value="{{ $profile_data->postcode }}" readonly="readonly"></div>

                                        <div class="col-md-12 mb-3"><label><small>Email ID</small></label><input
                                                type="email" class="form-control" id="email" name="email" placeholder="enter email id" value="{{ $profile_data->email }}" readonly="readonly"></div>


                                        {{-- <div class="col-md-12 mb-3"><label><small>Password</small></label><input
                                                  type="password" id="password" name="password" class="form-control" placeholder="password" readonly="readonly"></div>

                                          <div class="col-md-12 mb-3"><label><small>Confirm Password</small></label><input
                                                      type="password" id="cpassword" name="cpassword" class="form-control" placeholder="Confirm password" readonly="readonly"></div>
       --}}
                                    </div>
                                    {{-- <div class="mt-3"><button class="btn-theme text-white updateBtn" id="updateBtn" type="submit">Update
                                            Profile</button></div>
                                      <div class="mt-3"><button class="btn-theme text-white editBtn" id="editBtn" type="submit">Edit
                                              Profile</button></div> --}}
                                </div>
                            </div>


                        </div>
                    </div>


                  </form>
                </div>
            </section>
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
