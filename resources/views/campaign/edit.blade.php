@extends('layouts.admin')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Campaign Edit </div>
            </div>
        </section>


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('campaign.update', $data->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf

                            <div class="col my-3">
                                <label for="">Charity</label>
                                <select name="charity_id" id="charity_id" class="form-control @error('charity_id') is-invalid @enderror">
                                <option value="{{$data->charity_id}}">{{$data->charity->name}}</option>
                                @foreach (\App\Models\Charity::orderby('id','DESC')->get() as $charity)
                                <option value="{{$charity->id}}">{{$charity->name}}</option>
                                @endforeach
                                </select>
                         </div>

                        <div class="col my-3">
                                <label for="">Title</label>
                               <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror" value="{{ $data->campaign_title }}">
                         </div>

                         <div class="col my-3">
                            <label for="start_date">Start Date</label>
                           <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ $data->start_date }}">
                        </div>

                        
                        <div class="col my-3">
                            <label for="">End Date</label>
                           <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ $data->end_date }}">
                        </div>
                         
                         
                    </div>
                    <div class="col-md-6  my-4  bg-white">

                        
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
    $('#charity_id').select2({
      width: '100%',
      placeholder: "Select an Option",
      allowClear: true
    });

  </script>

@endsection
