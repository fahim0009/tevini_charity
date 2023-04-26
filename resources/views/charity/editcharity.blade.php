@extends('layouts.admin')

@section('content')

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Charity Edit </div>
            </div>
        </section>


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('charity.update', $users->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Name</label>
                               <input type="text" name="name" id="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" value="{{ $users->name }}">
                         </div>
                         <div class="col my-3">
                             <label for="">Address</label>
                            <input type="text" name="address" id="address" placeholder="Address" class="form-control @error('address') is-invalid @enderror"  value="{{ $users->address }}">
                         </div>
                        <div class="col my-3">
                             <label for="">Town</label>
                            <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror" value="{{ $users->town }}">
                         </div>
                        <div class="col my-3">
                             <label for="">Post Code</label>
                            <input type="text" name="post_code" id="post_code" placeholder="" class="form-control @error('address') is-invalid @enderror" value="{{ $users->post_code }}">
                         </div>


                    </div>
                    <div class="col-md-6  my-4  bg-white">


                        <div class="col my-3">
                            <label for="">Email</label>
                           <input type="text" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" value="{{ $users->email }}">
                        </div>
                        <div class="col my-3">
                            <label for="">Phone </label>
                           <input type="text" name="number" id="number" placeholder="number" class="form-control @error('number') is-invalid @enderror"  value="{{ $users->number }}">
                        </div>
                        <div class="col my-3">
                            <label for="">Charity Number</label>
                           <input type="text" name="acc_no" id="acc_no" placeholder="Account no" class="form-control @error('address') is-invalid @enderror"  value="{{ $users->acc_no }}">
                        </div>
                        <div class="col my-3">
                            <label for="password">Password</label>
                           <input type="password" name="password" id="password" placeholder="Password" class="form-control">
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
