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


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('donor.update', $users->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Title</label>
                               <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror" value="{{ $users->title }}">
                         </div>
                         <div class="col my-3">
                             <label for="">Surname</label>
                            <input type="text" name="surname" id="surname" placeholder="Surname" class="form-control @error('surname') is-invalid @enderror" value="{{ $users->surname }}">
                         </div>
                         <div class="col my-3">
                             <label for="">Street</label>
                            <input type="text" name="street" id="street" placeholder="Street" class="form-control @error('street') is-invalid @enderror"  value="{{ $users->street }}">
                         </div>
                         <div class="col my-3">
                             <label for="">Postcode</label>
                            <input type="text" name="postcode" id="postcode" placeholder="Postcode" class="form-control @error('postcode') is-invalid @enderror"  value="{{ $users->postcode }}">
                         </div>
                         <div class="col my-3">
                             <label for="">Phone</label>
                            <input type="text" name="phone" id="phone" placeholder="Phone" class="form-control @error('phone') is-invalid @enderror"  value="{{ $users->phone }}">
                         </div>
                         <div class="col my-3">
                            <label for="password">Password</label>
                           <input type="password" name="password" id="password" placeholder="Password" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6  my-4  bg-white">

                        <div class="col my-3">
                            <label for="">First Name</label>
                           <input type="text" name="fname" id="fname" placeholder="Name" class="form-control @error('fname') is-invalid @enderror"  value="{{ $users->name }}">
                        </div>
                        <div class="col my-3">
                            <label for="">House no</label>
                           <input type="text" name="houseno" id="houseno" placeholder="House no" class="form-control @error('houseno') is-invalid @enderror "  value="{{ $users->houseno }}">
                        </div>
                        <div class="col my-3">
                            <label for="">Town</label>
                           <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror"  value="{{ $users->town }}">
                        </div>
                        <input type="hidden" name="donorid" id="donorid" class="form-control">

                        <div class="col my-3">
                            <label for="">Email</label>
                           <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror"  value="{{ $users->email }}">
                        </div>
                        <div class="col my-3">
                            <label for="">Account No</label>
                           <input type="text" name="accountno" id="accountno" placeholder="Account no" class="form-control @error('email') is-invalid @enderror"  value="{{ $users->accountno }}">
                        </div>
                        <div class="col my-3">
                            <label for="cpassword">Confirm Password</label>
                           <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Update</button>
                    </div>
                    </form>
            </div>
        </section>



    </div>
</div>


@endsection

@section('script')

<script>
    $(document).ready(function () {



    });

</script>
@endsection
