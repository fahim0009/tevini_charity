@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    
    <div class="row mb-5">
        <div class="col-lg-6">
            <a href="{{ route('userCardService')}}" class="btn-theme bg-primary">Back</a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                 Change card status
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


    <form  action="{{route('cardStatusChangeStore')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-3">
            <div class="row">
                <div class="col-md-6">
                    <label for="Status">Status</label>
                    <select name="Status" id="Status" class="form-control">
                        <option value="">Select</option>
                        <option value="NORMAL">I have found my card - unfreeze</option>
                        <option value="STOLEN">STOLEN (Permanent block)</option>
                        <option value="LOST">LOST (Permanent block)</option>
                        <option value="SUSPEND">Apply a temporary freeze my card.</option>
                        
                    </select>
                </div>

                <div class="col-md-12">
                    <p>Note* Status updated to LOST/STOLEN blocks the card permenantly.</p>
                    {{-- <p>Note* Status updated BLOCK/SUSPENDED can be reversed back to normal.</p> --}}
                </div>
            </div>


            <div>
                <div class="col-lg-12">
                    <div class="form-group ">
                        <button class="d-block btn-theme bg-secondary">Submit</button>
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