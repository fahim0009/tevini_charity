@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row mb-3">
        <div class="col-lg-6">
            <a href="{{ route('userCardService')}}" class="btn-theme bg-primary">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                 Card Activation
            </div>
        </div>
    </div>
    <!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." style="height: 225px;" />
    </div>
    <!-- Image loader -->

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


    <form  action="{{route('cardActivationstore')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-3">
            <div class="row">
                <div class="col-md-12">
                    <label for="CardDisplayName">Card Name</label>
                    <input type="text" name="CardDisplayName" id="CardDisplayName" placeholder="CardDisplayName" class="form-control @error('CardDisplayName') is-invalid @enderror">
                    @error('CardDisplayName')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                
                <div class="col-md-12">
                    <label for="PAN">Card Number</label>
                    <input type="text" name="PAN" id="PAN" placeholder="PAN" class="form-control @error('PAN') is-invalid @enderror"  maxlength="16">
                    @error('PAN')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <input type="checkbox" class="me-2 @error('terms') is-invalid @enderror" name="terms" value="1">I have read and accept the cardholder  <a href="{{route('cardterms')}}" style="text-decoration: none;color:#212529" target="blank"> Terms & Conditions. </a><br>
                    @error('terms')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

            </div>


            <div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <button class="d-block btn-theme bg-secondary mt-3 submitBtn">Submit</button>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>


@endsection

@section('script')


<script type="text/javascript">

    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);        
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });

    
  
  </script>

<script>
    $(function() {
      $('.submitBtn').click(function() {
        
        $("#loading").show();

      })
    })
</script>
@endsection