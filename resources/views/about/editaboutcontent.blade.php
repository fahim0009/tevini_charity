@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">About Content Edit </div>
            </div>
        </section>


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('aboutcontent.update', $about->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Title 1</label>
                               <input type="text" name="title1" id="title1" placeholder="Title1" class="form-control @error('title1') is-invalid @enderror" value="{{ $about->title1 }}">
                         </div>
                         <div class="col my-3">
                            <label for="">Title 2</label>
                           <input type="text" name="title2" id="title2" placeholder="Title2" class="form-control @error('title2') is-invalid @enderror" value="{{ $about->title2 }}">
                        </div>
                        <div class="col my-3">
                            <label for="">Title 3</label>
                           <input type="text" name="title3" id="title3" placeholder="Title3" class="form-control @error('title3') is-invalid @enderror" value="{{ $about->title3 }}">
                        </div>
                        

                    </div>
                    <div class="col-md-6  my-4  bg-white">
                        
                        <div class="col my-3">
                            <label for="">Turnover title</label>
                           <input type="text" name="turnover_title" id="turnover_title" placeholder="Turnover Title" class="form-control @error('turnover_title') is-invalid @enderror" value="{{ $about->turnover_title }}">
                        </div>
                        <div class="col my-3">
                            <label for="">Turnover Image</label>
                           <input type="file" name="turnover_image" id="turnover_image" placeholder="Image" class="form-control">
                        </div>
                        <div class="col my-3">
                            <label for="">Profit title </label>
                           <input type="text" name="profit_title" id="profit_title" placeholder="Profit Title" class="form-control @error('profit_title') is-invalid @enderror" value="{{ $about->profit_title }}">
                        </div>
                         <div class="col my-3">
                            <label for="">Profit Image</label>
                           <input type="file" name="profit_image" id="profit_image" placeholder="Image" class="form-control">
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
