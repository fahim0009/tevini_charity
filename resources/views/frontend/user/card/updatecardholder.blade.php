@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Update card holder
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


    <form  action="{{route('cardholderUpdatePost')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row ">
            <div class="row">
                <div class="col-md-6">
                    <label for="">FirstName</label>
                    <input type="text" name="FirstName" id="FirstName" placeholder="FirstName" class="form-control" value="{{$data->FirstName}}">
                    <input type="hidden" name="CardHolderId" id="CardHolderId" class="form-control" value="{{$data->CardHolderId}}">
                    <input type="hidden" name="cardid" id="cardid" class="form-control" value="{{$data->id}}">
                </div>
                <div class="col-md-6">
                    <label for="">LastName</label>
                    <input type="text" name="LastName" id="LastName" placeholder="LastName" class="form-control" value="{{$data->LastName}}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" style="display: none">
                    <label for="">SecondSurname</label>
                    <input type="text" name="SecondSurname" id="SecondSurname" placeholder="SecondSurname" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="">UserName (**Unique username)</label>
                    <input type="text" name="UserName" id="UserName" placeholder="UserName" class="form-control" value="{{$data->UserName}}">

                    
                    <label for="">Email (**unique email)</label>
                    <input type="text" name="Email" id="Email" placeholder="Email" class="form-control @error('Email') is-invalid @enderror" value="{{$data->Email}}">
                </div>

                
                <div class="col-md-6">
                    <label for="">Password (**
                        Password must be at least 8 characters. Must have at least one uppercase ('A'-'Z'), one lowercase ('a'-'z') letter and number ('0'-'9'). No special characters allowed!)</label>
                    <input type="password" name="Password" id="Password" placeholder="Password" class="form-control">
                </div>


            </div>

        

            <div class="row">
                <div class="col-md-6">
                    <label for="">Mobile (** start +44)</label>
                    <input type="text" name="Mobile" id="Mobile" placeholder="Mobile" class="form-control" value="{{$data->Mobile}}">
                </div>
                <div class="col-md-6">
                    <label for="">LandlineTelephone</label>
                    <input type="text" name="LandlineTelephone" id="LandlineTelephone" placeholder="LandlineTelephone" class="form-control" value="{{$data->LandlineTelephone}}">
                </div>
            </div>

            <div class="row">
                

                <div class="col-md-4" style="display: none">
                    <label for="">SocialSecurityNumber</label>
                    <input type="number" name="SocialSecurityNumber" id="SocialSecurityNumber" placeholder="SocialSecurityNumber" class="form-control" value="12345678">
                </div>

                <div class="col-md-4" style="display: none">
                    <label for="">IdCardNumber</label>
                    <input type="number" name="IdCardNumber" id="IdCardNumber" placeholder="IdCardNumber" class="form-control" value="001">
                </div>
            </div>


            <div class="row">

                <div class="col-md-4" style="display: none">
                    <label for="">TaxIdCardNumber</label>
                    <input type="text" name="TaxIdCardNumber" id="TaxIdCardNumber" placeholder="TaxIdCardNumber" class="form-control" value="0002">
                </div>

                
                <div class="col-md-4">
                    <label for="">DateOfBirth</label>
                    <input type="datetime-local" name="DateOfBirth" id="DateOfBirth" placeholder="DateOfBirth" class="form-control" required value="{{$data->DateOfBirth}}">
                </div>

                <div class="col-md-4">
                    <label for="">Nationality</label>
                    <input type="text" name="Nationality" id="Nationality" placeholder="Nationality" class="form-control" value="{{$data->Nationality}}">
                </div>

                <div class="col-md-4">
                    <label for="">Title</label>
                    <input type="text" name="Title" id="Title" placeholder="Title" class="form-control" value="Test Title" value="{{$data->Title}}">
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <label for="">HouseNumberOrBuilding</label>
                    <input type="text" name="HouseNumberOrBuilding" id="HouseNumberOrBuilding" placeholder="HouseNumberOrBuilding" class="form-control" value="{{$data->HouseNumberOrBuilding}}">
                </div>

                <div class="col-md-4">
                    <label for="">Address1</label>
                    <input type="text" name="Address1" id="Address1" placeholder="Address1" class="form-control" value="{{$data->Address1}}">
                </div>

                <div class="col-md-4">
                    <label for="">Address2</label>
                    <input type="text" name="Address2" id="Address2" placeholder="Address2" class="form-control" value="{{$data->Address2}}">
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <label for="">City</label>
                    <input type="text" name="City" id="City" placeholder="City" class="form-control"   value="{{$data->City}}">
                </div>

                <div class="col-md-4">
                    <label for="">PostCode</label>
                    <input type="text" name="PostCode" id="PostCode" placeholder="PostCode" class="form-control" value="{{$data->PostCode}}">
                </div>

                <div class="col-md-4">
                    <label for="">State</label>
                    <input type="text" name="State" id="State" placeholder="State" class="form-control" value="{{$data->State}}">
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