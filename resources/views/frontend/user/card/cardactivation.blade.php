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
                    <input type="text" name="CardDisplayName" id="CardDisplayName" placeholder="CardDisplayName" class="form-control" required>
                </div>

                
                <div class="col-md-12">
                    <label for="PAN">Card Number</label>
                    <input type="text" name="PAN" id="PAN" placeholder="PAN" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <input type="checkbox" class="me-2" required>I have read and accept the cardholder  <a href="{{route('cardterms')}}" style="text-decoration: none;color:#212529" target="blank"> Terms & Conditions. </a><br>
                </div>

            </div>


            <div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <button class="d-block btn-theme bg-secondary mt-3">Submit</button>
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

@endsection