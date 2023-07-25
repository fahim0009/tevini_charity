@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                 Card Set Pin
            </div>
        </div>
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


    <form  action="{{route('cardSetPinstore')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-3">
            <div class="row">
                <div class="col-md-12">
                    <label for="PIN">PIN (Maximum 4 digit)</label>
                    <input type="number" name="PIN" id="PIN" placeholder="PIN" maxlength="4" class="form-control">
                </div>

                
                <div class="col-md-12">
                    <label for="PAN">Card Number</label>
                    <input type="text" name="PAN" id="PAN" placeholder="PAN" class="form-control">
                </div>

            </div>


            <div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <button class="d-block btn-theme bg-secondary mt-5">Submit</button>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>


@endsection

@section('script')


<script>

</script>

@endsection