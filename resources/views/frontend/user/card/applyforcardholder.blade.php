@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Apply for card holder
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


    <form  action="{{ route('applyforcardholderstore') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row ">
            <div class="row">
                <div class="col-md-6">
                    <label for="">FirstName</label>
                    <input type="text" name="FirstName" id="FirstName" placeholder="FirstName" class="form-control @error('FirstName') is-invalid @enderror" value="{{Auth::user()->name}}">
                    @error('FirstName')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="">LastName</label>
                    <input type="text" name="LastName" id="LastName" placeholder="LastName" class="form-control @error('LastName') is-invalid @enderror" value="{{Auth::user()->surname}}">
                    @error('LastName')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" style="display: none">
                    <label for="">SecondSurname</label>
                    <input type="text" name="SecondSurname" id="SecondSurname" placeholder="SecondSurname" class="form-control">
                </div>
            </div>

        

            <div class="row">
                <div class="col-md-6">
                    
                    <label for="">Email (**unique email)</label>
                    <input type="text" name="Email" id="Email" placeholder="Email" class="form-control @error('Email') is-invalid @enderror" value="{{Auth::user()->email}}">
                    @error('Email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                @php
                    $number = Auth::user()->phone;
                    $num = substr($number, 0, 3);
                    $num2 = substr($number, 3, 2);
                @endphp

                <div class="col-md-6">
                    <label for="">Mobile (** start +44)</label>
                    <small style="color: red">**Don't use 0 after +44</small>
                    <input type="text" name="Mobile" id="Mobile" placeholder="Mobile" class="form-control @error('Mobile') is-invalid @enderror" value="@if ($num != "+44")+44 @else{{$number}}@endif" maxlength="13">
                    @error('Mobile')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="">DateOfBirth</label>
                    <input type="date" name="DateOfBirth" id="DateOfBirth" placeholder="DateOfBirth" class="form-control @error('DateOfBirth') is-invalid @enderror" value="{{ old('DateOfBirth') }}">
                    @error('DateOfBirth')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                
                <div class="col-md-6">
                    <label for="">HouseNumberOrBuilding</label>
                    <input type="text" name="HouseNumberOrBuilding" id="HouseNumberOrBuilding" placeholder="HouseNumberOrBuilding" class="form-control @error('HouseNumberOrBuilding') is-invalid @enderror" value="{{Auth::user()->houseno}}" required>
                    @error('HouseNumberOrBuilding')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-md-6" style="display: none">
                    <label for="">LandlineTelephone</label>
                    <input type="text" name="LandlineTelephone" id="LandlineTelephone" placeholder="LandlineTelephone" class="form-control" value="">
                </div>
            </div>

            <div class="row">
                

                <div class="col-md-4" style="display: none">
                    <label for="">SocialSecurityNumber</label>
                    <input type="number" name="SocialSecurityNumber" id="SocialSecurityNumber" placeholder="SocialSecurityNumber" class="form-control" value="">
                </div>

                <div class="col-md-4" style="display: none">
                    <label for="">IdCardNumber</label>
                    <input type="number" name="IdCardNumber" id="IdCardNumber" placeholder="IdCardNumber" class="form-control" value="">
                </div>
            </div>


            <div class="row">

                <div class="col-md-4" style="display: none">
                    <label for="">TaxIdCardNumber</label>
                    <input type="text" name="TaxIdCardNumber" id="TaxIdCardNumber" placeholder="TaxIdCardNumber" class="form-control" value="">
                </div>
                <div class="col-md-4"  style="display: none">
                    <label for="">Nationality</label>
                    <input type="text" name="Nationality" id="Nationality" placeholder="Nationality" class="form-control">
                </div>
                <div class="col-md-4" style="display: none">
                    <label for="">Title</label>
                    <input type="text" name="Title" id="Title" placeholder="Title" class="form-control" value="">
                </div>
            </div>

            <div class="row">


                <div class="col-md-6">
                    <label for="">Address1</label>
                    <input type="text" name="Address1" id="Address1" placeholder="Address1" class="form-control @error('Address1') is-invalid @enderror" value="{{Auth::user()->street}}">
                    @error('Address1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="">Address2</label>
                    <input type="text" name="Address2" id="Address2" placeholder="Address2" class="form-control" value="">
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <label for="">City</label>
                    <input type="text" name="City" id="City" placeholder="City" class="form-control"  value="{{Auth::user()->town}}" required>
                </div>

                <div class="col-md-4">
                    <label for="">PostCode</label>
                    <input type="text" name="PostCode" id="PostCode" placeholder="PostCode" class="form-control" value="{{Auth::user()->postcode}}" maxlength="8" required>
                </div>

                <div class="col-md-4">
                    <label for="">County</label>
                    <input type="text" name="State" id="State" placeholder="State" class="form-control @error('State') is-invalid @enderror" value="{{ old('State') }}">
                    @error('State')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>





            <div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        <button class="d-block btn-theme bg-secondary mt-5 submitBtn">Submit</button>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>


@endsection

@section('script')
<script>
    $(function() {
      $('.submitBtn').click(function() {
        
        $("#loading").show();

      })
    })
</script>

<script>

</script>

@endsection