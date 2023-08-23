@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Mobile Verification
            </div>
            <p style="color: red">Input the code we sent to +44XXXXXX{{$MobileLstDgt}} to Get and Change your PIN. </p>
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


    <form  action="{{ route('send.sms') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-3">
            <div class="row">
                <div class="col-md-6">
                    <label for="Status">Code</label>
                    <input type="text" id="Code" name="Code" class="form-control">
                </div>

                {{-- <div class="col-md-12">
                    <p>Note* Status updated to LOST/STOLEN blocks the card permenantly.</p>
                </div> --}}
            </div>


            <div>
                <div class="col-lg-12">
                    <div class="row">

                        <button class="d-block btn-theme bg-secondary">Submit</button>
                        <a href="{{route('mobileVerify')}}" class="btn-theme bg-success">Re-send</a>
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