@extends('layouts.admin')

@section('content')

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">


<div class="dashboard-content">
    <section class="profile purchase-status no-print">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Email</div>
            <a href="{{ route('donor') }}"><button type="button" class="btn btn-success">back</button></a>
        </div>

        @if(session()->has('success'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('success') }}</div>
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

    </section>

    <section class="px-4 no-print">
            <div class="row my-3">
                <div class="ermsg"></div>
            </div>
        </section>

         <!-- Image loader -->
         <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->
  <section class="no-print">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 my-3">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">

                    <form class="form-inline" method="POST" action=""  enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class="col-md-12">
                                
                                    <div class="form-group my-2">
                                        <label for=""><small>Subject</small> </label>
                                        <input class="form-control mr-sm-2" type="text" name="subject" id="subject" value="{{old('subject')}}">
                                        @if ($errors->has('subject'))
                                            <span class="text-danger">{{ $errors->first('subject') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group my-2">
                                        <label for=""><small>Body</small> </label>
                                        <textarea class="form-control mr-sm-2" name="body" id="body" cols="30" rows="10">{{old('body')}}</textarea>
                                        
                                        @if ($errors->has('body'))
                                            <span class="text-danger">{{ $errors->first('body') }}</span>
                                        @endif
                                    </div>
                                <div class="form-group my-2">
                                    <button type="submit" id="topBal" class="my-2 btn btn-sm btn-info text-white">Send Email</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>
  </section>



</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>

$(document).ready(function() {
  $('#body').summernote();
});

$(document).ready(function () {

//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//



    $("#topBal").click(function(){

        $("#loading").show();

    });



});

</script>
@endsection