@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">About Help Edit </div>
            </div>
        </section>


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('abouthelp.update', $about->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Title</label>
                               <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror" value="{{ $about->title }}">
                         </div>
                         <div class="col my-3">
                             <label for="">Image</label>
                            <input type="file" name="image" id="image" placeholder="Image" class="form-control">
                         </div>
                         <div class="col my-3">
                             <label for="">Description</label>
                            <textarea name="description" id="description" cols="30" rows="10" class="form-control">{{ $about->description }}</textarea>
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
