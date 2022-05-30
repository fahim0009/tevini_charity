@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Charity List </div>
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
                        <form action="{{ route('charity.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Name</label>
                               <input type="text" name="name" id="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror">
                         </div>
                         <div class="col my-3">
                             <label for="">Address</label>
                            <input type="text" name="address" id="address" placeholder="Address" class="form-control @error('address') is-invalid @enderror">
                         </div>
                        <div class="col my-3">
                             <label for="">Town</label>
                            <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror">
                         </div>
                        <div class="col my-3">
                             <label for="">Post Code</label>
                            <input type="text" name="post_code" id="post_code" placeholder="" class="form-control @error('address') is-invalid @enderror">
                         </div>
    
                    </div>




                    <div class="col-md-6  my-4  bg-white">


                        <div class="col my-3">
                            <label for="">Email</label>
                           <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror">
                        </div>
                        <div class="col my-3">
                            <label for="">Phone</label>
                           <input type="text" name="number" id="number" placeholder="Phone" class="form-control @error('number') is-invalid @enderror">
                        </div>

                        <div class="col my-3">
                            <label for="">Charity Number</label>
                           <input type="text" name="acc" id="acc" placeholder="account no" class="form-control @error('acc') is-invalid @enderror">
                        </div>
                        <div class="col my-3">
                            <label for="">Balance</label>
                           <input type="text" name="balance" id="balance" placeholder="balance" class="form-control">
                        </div>

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Create</button>
                        <a class="btn btn-warning mt-2 text-white" id="FormCloseBtn">close</a>
                    </div>
                    </form>
            </div>
        </section>


        <section id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Town</th>
                                    <th>Post Code</th>
                                    <th>Charity Number</th>
                                    <th>Balance</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->number}}</td>
                                        <td>{{$user->address}}</td>
                                        <td>{{$user->town}}</td>
                                        <td>{{$user->post_code}}</td>
                                        <td>{{$user->acc_no}}</td>
                                        <td>£{{$user->balance}}</td>
                                        <td>
                                        <div class="d-flex justify-content-center align-items-center flex-column text-white">
                                             <a class="text-decoration-none bg-success text-white py-1 px-3 rounded mb-1" href="{{ route('charity.pay',$user->id) }}" target="blank">
                                         Pay </a>
                                        <a class=" text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1" href="{{ route('charity.topup',$user->id) }}" target="blank">
                                        Top up </a>
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ route('charity.tranview', $user->id)}}"><i class="fa fa-eye" style="color: #09a311;font-size:16px;"></i></a>
                                        <a href="{{ route('charity.edit', encrypt($user->id))}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
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
        </section>


    </div>
</div>


@endsection

@section('script')

<script>
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



        var url = "{{URL::to('/admin/add-charity/delete')}}";
        // Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                    console.log(d);
                    if(d.success) {
                        success("Deleted Successfully!!");
                        //alert(d.message);
                        location.reload();
                    }
                },
                error:function(d){
                    console.log(d);
                }
            });
        });
        // Delete

    });





</script>
@endsection
