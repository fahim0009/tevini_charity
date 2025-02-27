@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Donor Edit </div>
            </div>
        </section>

        <div class="row justify-content-center p-5">
            <div class="card col-8 p-5">
                <form action="{{ route('donor.update', $users->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                    @csrf
                    <input type="hidden" name="donorid" id="donorid" class="form-control">
                    <div class="row">
                        
                        
                        <div class="col-md-12">
                            <div class="my-3">
                                <label for="">Account registration for:</label>
                                <select name="profile_type" id="profile_type"  class="form-control @error('profile_type') is-invalid @enderror">
                                    <option value="Company">Company</option>
                                    <option value="Personal">Personal</option>
                                </select>
                             </div>
                        </div>

                        <div class="row" id="companyDiv">
                            <div class="col-md-6">
                                    <label for="">Company Name</label>
                                    <input id="company_name" type="text" class="form-control" name="company_name" value="{{ $users->name }}">
                            </div>
    
                            <div class="col-md-6">
                                    <label for="">Your Name</label>
                                    <input id="company_last_name" type="text" class="form-control" name="company_last_name" value="{{ $users->surname }}">
                            </div>
                        </div>

                        <div class="row" id="personalDiv">
                            <div class="col-md-6">
                                <div class="  ">
                                    <label for="">First Name</label>
                                   <input type="text" name="fname" id="fname" placeholder="Name" class="form-control"  value="{{ $users->name }}">
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="  ">
                                    <label for="">Surname</label>
                                   <input type="text" name="surname" id="surname" placeholder="Surname" class="form-control" value="{{ $users->surname }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Phone</label>
                               <input type="text" name="phone" id="phone" placeholder="Phone" class="form-control @error('phone') is-invalid @enderror"  value="{{ $users->phone }}">
                            </div>
                        </div>

                        

                        

                        

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Address first line</label>
                               <input type="text" name="houseno" id="houseno" placeholder="Address first line" class="form-control @error('houseno') is-invalid @enderror "  value="{{ $users->houseno }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Address second line</label>
                               <input type="text" name="street" id="street" placeholder="Address second line" class="form-control @error('street') is-invalid @enderror"  value="{{ $users->street }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Address third line</label>
                               <input type="text" name="address_third_line" id="address_third_line" placeholder="Address third line" class="form-control @error('town') is-invalid @enderror"  value="{{ $users->address_third_line }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Town</label>
                               <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror"  value="{{ $users->town }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Postcode</label>
                               <input type="text" name="postcode" id="postcode" placeholder="Postcode" class="form-control @error('postcode') is-invalid @enderror"  value="{{ $users->postcode }}">
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="  ">
                                <label for="">Account No</label>
                               <input type="text" name="accountno" id="accountno" placeholder="Account no" class="form-control @error('email') is-invalid @enderror"  value="{{ $users->accountno }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="  ">
                                <label for="">Email</label>
                               <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror"  value="{{ $users->email }}">
                            </div>
                        </div>

                        <div class="col-md-6 ">
                            <div class="  ">
                               <label for="password">Password</label>
                              <input type="password" name="password" id="password" placeholder="Password" class="form-control">
                           </div>
                       </div>

                        <div class="col-md-6">
                            <div class="  ">
                                <label for="cpassword">Confirm Password</label>
                               <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password" class="form-control">
                            </div>
                        </div>

                        
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <button class="btn btn-theme mt-2 text-white">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>






    </div>
</div>


@endsection

@section('script')

<script>
    $(function() {
        $('#personalDiv').hide(); 
        $('#profile_type').change(function(){
            if($('#profile_type').val() == 'Personal') {
                $('#personalDiv').show(); 
                $('#companyDiv').hide(); 
            } else {
                $('#personalDiv').hide(); 
                $('#companyDiv').show(); 
            } 
        });
    });

</script>
@endsection
