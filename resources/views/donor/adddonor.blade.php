@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Donor List </div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <button id="newBtn" type="button" class="btn btn-info">Add New</button>
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


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('donor.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Title</label>
                               <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror">
                         </div>
                         <div class="col my-3">
                            <label for=""> Name</label>
                           <input type="text" name="fname" id="fname" placeholder="Name" class="form-control @error('fname') is-invalid @enderror">
                        </div>
                         <div class="col my-3">
                             <label for="">Surname</label>
                            <input type="text" name="surname" id="surname" placeholder="Surname" class="form-control @error('surname') is-invalid @enderror">
                         </div>

                         <div class="col my-3">
                             <label for="">Postcode</label>
                            <input type="text" name="postcode" id="postcode" placeholder="Postcode" class="form-control @error('postcode') is-invalid @enderror">
                         </div>
                         <div class="col my-3">
                             <label for="">Phone</label>
                            <input type="text" name="phone" id="phone" placeholder="Phone" class="form-control @error('phone') is-invalid @enderror">
                         </div>
                    </div>
                    <div class="col-md-6  my-4  bg-white">
                        <div class="col my-3">
                            <label for="">Street</label>
                           <input type="text" name="street" id="street" placeholder="Street" class="form-control @error('street') is-invalid @enderror">
                        </div>
                        <div class="col my-3">
                            <label for="">House no</label>
                           <input type="text" name="houseno" id="houseno" placeholder="House no" class="form-control @error('houseno') is-invalid @enderror ">
                        </div>
                        <div class="col my-3">
                            <label for="">Town</label>
                           <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror">
                        </div>
                        <input type="hidden" name="donorid" id="donorid" class="form-control">

                        <div class="col my-3">
                            <label for="">Email</label>
                           <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror">
                        </div>
                        <div class="col my-3">
                            <label for="">Balance</label>
                           <input type="text" name="balance" id="balance" placeholder="balance" class="form-control">
                        </div>
                        <div class="col my-3">
                            <label for="">Account No</label>
                           <input type="text" name="accno" id="accno" placeholder="Account No" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Create</button>
                        <a class="btn btn-warning mt-2 text-white" id="FormCloseBtn">close</a>
                    </div>
                    </form>
            </div>
        </section>


        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th style="min-width:160px">Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Account</th>
                                    <th>Balance</th>
                                    <th>Overdrawn</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$user->name}} {{$user->surname}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>{{$user->town}}</td>
                                        <td>
                                            @if($user->accountno)
                                            {{$user->accountno}}
                                            @else
                                            <button type="button" user-id="{{$user->id}}" class="btn btn-primary acc" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                add
                                            </button>
                                            @endif
                                        </td>
                                        <td>??{{$user->balance}}</td>
                                        <td>??{{$user->overdrawn_amount}}
                                            <a overdrawn-id="{{$user->id}}" class="overdrawn" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="fa fa-edit" style="color: #2094f3;font-size:16px;"></i>
                                            </a>
                                        </td>
                                        <td>
                                        <a class="text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1 d-block text-center" href="{{ route('topup',[$user->id,0]) }}" target="blank">
                                         <small>Top Up</small> </a>
                                       <div class="py-1 text-center">
                                        <a href="{{ route('donor.profile', $user->id)}}"><i class="fa fa-eye" style="color: #09a311;font-size:16px;"></i></a>
                                        <a href="{{ route('donor.edit', encrypt($user->id))}}"><i class="fa fa-edit" style="color: #2094f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{$user->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                       </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse




                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </section>


    </div>
</div>


<!-- Button trigger modal -->


  <!-- Modal -->
  <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel2">Update Overdrawn </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsgod"></div>
            <div class="mb-3">
                <label for="overdrawnno" class="form-label">Overdrawn Amount</label>
                <input type="text" class="form-control" id="overdrawnno">
                <input type="hidden" class="form-control" value="" id="overdrawnid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="overdrawnBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->



  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Donner Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsg"></div>
            <div class="mb-3">
                <label for="updaccno" class="form-label">Account</label>
                <input type="text" class="form-control" id="updaccno">
                <input type="hidden" class="form-control" value="" id="donnerid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->


@endsection

@section('script')

<script>

window.onload = (event) => {
   let k = document.getElementById("example_wrapper");
   k.classList.add('px-0');
};


    $(document).ready(function () {



        $("#addThisFormContainer").hide();
        $("#newBtn").click(function(){
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);

        });
        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        function clearform(){
                $('#createThisForm')[0].reset();
            }

            setTimeout(function() {
                $('#successMessage').fadeOut('fast');
                $('#errMessage').fadeOut('fast');
            }, 3000);

     //header for csrf-token is must in laravel
     $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

        var urlDlt = "{{URL::to('/admin/donor/delete')}}";
        // Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Are you sure?')) return;
            donorId = $(this).attr('rid');
            $.ajax({
            url: urlDlt,
            method: "POST",
            data: {donorId:donorId},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });
        });
        // Delete


        //add account to donor
        $(".acc").click(function(){
		var userid = $(this).attr("user-id");
        $('#donnerid').val(userid);
	    });

        var url = "{{URL::to('/admin/add-account')}}";
        $("#addBtn").click(function(){
        var donnerId= $("#donnerid").val();
        var accno= $("#updaccno").val();
        console.log(donnerId);
        $.ajax({
            url: url,
            method: "POST",
            data: {donnerId:donnerId,accno:accno},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

            });

        //add account to donor END

        //add overdrawn
        $(".overdrawn").click(function(){
		var overdrawnid = $(this).attr("overdrawn-id");
        $('#overdrawnid').val(overdrawnid);
	    });

        var overdrawnurl = "{{URL::to('/admin/update-overdrawn')}}";
        $("#overdrawnBtn").click(function(){
        var overdrawnid= $("#overdrawnid").val();
        var overdrawnno= $("#overdrawnno").val();
        $.ajax({
            url: overdrawnurl,
            method: "POST",
            data: {overdrawnid:overdrawnid,overdrawnno:overdrawnno},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsgod").html(d.message);
                }else if(d.status == 300){
                    $(".ermsgod").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

            });

        // overdrawn END




    });





</script>
@endsection
