@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Campaign List </div>
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
                        <form action="{{ route('campaign.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                            
                        <div class="col my-3">
                                <label for="">Charity</label>
                                <select name="charity_id" id="charity_id" class="form-control @error('charity_id') is-invalid @enderror">
                                <option value="">Please Select</option>
                                @foreach (\App\Models\Charity::orderby('id','DESC')->get() as $charity)
                                <option value="{{$charity->id}}">{{$charity->name}}</option>
                                @endforeach
                                </select>
                         </div>
                         <div class="col my-3">
                            <label for="">Title</label>
                           <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror">
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
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 "> 
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Charity</th>
                                    <th>Campaign Title</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sl =0;
                                @endphp
                                @forelse ($data as $key => $item)
                                    <tr>
                                        <td>{{ $sl++}}</td>
                                        <td>{{$item->charity->name }}</td>
                                        <td>{{$item->campaign_title}}</td>
                                        <td>
                                    
                                        <a href="{{ route('campaign.edit', encrypt($item->id))}}"><i class="fa fa-edit" style="color: #2094f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{$item->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                       
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

        var urlDlt = "{{URL::to('/admin/campaign/delete')}}";
        // Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Are you sure?')) return;
            campaignId = $(this).attr('rid');
            $.ajax({
            url: urlDlt,
            method: "POST",
            data: {campaignId:campaignId},
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

    });

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
    $('#charity_id').select2({
      width: '100%',
      placeholder: "Select an Option",
      allowClear: true
    });
  </script>
@endsection
