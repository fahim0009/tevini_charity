@extends('layouts.admin')

@section('content')



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

<script>
    $(document).ready(function () {



    });

</script>
@endsection
