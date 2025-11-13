@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">About Content </div>
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
                        <form action="{{ route('about.helpstore') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Title</label>
                               <input type="text" name="title" id="title" placeholder="title" class="form-control @error('title') is-invalid @enderror">
                         </div>
                         <div class="col my-3">
                             <label for="">Image</label>
                            <input type="file" name="image" id="image" placeholder="Image" class="form-control @error('image') is-invalid @enderror">
                         </div>
                         <div class="col my-3">
                             <label for="">Description</label>
                            <textarea name="description" id="description" cols="30" rows="10" class="form-control @error('description') is-invalid @enderror"></textarea>
                         </div>
                    </div>



                    
                    <div class="col-md-6  my-4  bg-white">

                       
                        
                        
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

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($abouthelp as $data)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$data->title}}</td>
                                        <td>{{$data->image}}</td>
                                        <td>{{$data->description}}</td>
                                        <td>
                                        <a href="{{ route('abouthelp.edit', encrypt($data->id))}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>

                            {{-- <a onclick="confirm_modal('{{route('deletedonor', $user->id)}}');"><i class="fa fa-trash-o" style="color: red;font-size:16px;" aria-hidden="true"></i></a> --}}
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



        var url = "{{URL::to('/admin/about-help/delete')}}";
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
                    if(d.success) {
                        success("Deleted Successfully!!");
                        location.reload();
                    }
                },
                error:function(d){
                        alert(d.message);
                }
            });
        });
        // Delete

    });





</script>
@endsection
