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
                                        <span> Balance : {{ $userTransactionBalance->balance < 0 ? '-' : '' }}£{{ number_format(abs($userTransactionBalance->balance), 2) }}</span>
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



            <!---------- User email account  ---------->


            <section class="profile purchase-status">
                <div class="title-section">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    <div class="mx-2">More email account</div>
                </div>
            </section>

            <section class="card m-3">
                <div class="row  my-3 mx-0 ">
                    <div class="col-md-12 ">


                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif



                        <div class="col-md-12" id="emailCreateForm">
                            <form class="form-inline" action="{{route('newUserCredentialStore')}}" method="POST">
                                @csrf         

                                <div class="row justify-content-center">

                                    <div class="col-md-4">
                                        <div class="form-group my-2">
                                            <label for="newemail"><small>Email</small> </label>
                                            <input class="form-control mr-sm-2" id="newemail" name="newemail" type="email"  value="">
                                            <input id="donor_id" name="donor_id" type="hidden"  value="{{$donor_id}}">
                                        </div>
                                    </div>

                                    <div class="col-md-4 d-flex align-items-center">
                                        <div class="form-group d-flex mt-4">
                                        <button class="text-white btn-theme ml-1" type="submit">Add</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        
                    </div>
                </div>
            </section>



            <section class="card m-3">
                <div class="row  my-3 mx-0 ">
                    <div class="col-md-12 ">
                        <div class="stsermsg"></div>
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Email</th>
                                            <th>Action </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach (\App\Models\UserDetail::where('user_id', $donor_id)->get() as $data)
                                            <tr>
                                                <td>{{ $data->date }}</td>
                                                <td>{{ $data->email }}</td>
                                                <td class="text-right">
                                                    <button data-udid="{{$data->id}}" data-email="{{$data->email}}" class="btn btn-sm btn-primary mr-1 editBtn" >Edit</button>

                                                    <form action="{{ route('useremail.destroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this email?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </section>  

            <!-- Edit Modal -->
            <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <form method="POST" id="editEmailForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                    <h5 class="modal-title" id="editEmailModalLabel">Edit Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
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

<script>
$(document).ready(function() {
    $('.editBtn').on('click', function() {
        let udid = $(this).data('udid');
        let email = $(this).data('email');

        // Use Laravel route name (replace :id dynamically)
        let updateRoute = "{{ route('useremail.update', ':id') }}";
        updateRoute = updateRoute.replace(':id', udid);

        // Set values in modal
        $('#editEmail').val(email);
        $('#editEmailForm').attr('action', updateRoute);

        // Show modal
        $('#editEmailModal').modal('show');
    });
});
</script>


@endsection
