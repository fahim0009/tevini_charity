@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Apply for card
            </div>
        </div>
    </div>
    <form  action="{{ route('applyforcardstore') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row ">
            <div class="col-lg-6  px-3">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="label" for="CardProgram">CardProgram</label>
                            <input type="text" class="form-control" name="CardProgram" id="CardProgram">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group ">
                            <label class="label" for="CardDesign">CardDesign</label>
                            <input type="text" class="form-control" name="CardDesign" id="CardDesign" >
                        </div>
                    </div>


                    
                    
                </div>
            </div>

            <div class="col-lg-6  px-3">
                <div class="row mt-4">


                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="label" for="ProductType">ProductType</label>
                            <input type="text" class="form-control" name="ProductType" id="ProductType">
                        </div>
                    </div>
                    
                    
                    <div class="col-lg-12">
                        <div class="form-group ">
                            <label class="label" for="CardProduct">CardProduct</label>
                            <input type="text" class="form-control" name="CardProduct" id="CardProduct">
                            <input type="hidden" class="form-control" name="SpendProfileId" id="SpendProfileId" value="{{$data['SpendProfile']['SpendProfileId']}}">
                            <input type="hidden" class="form-control" name="SpendProfileName" id="SpendProfileName" value="{{$data['SpendProfile']['ProfileName']}}">


                            <input type="hidden" class="form-control" name="CreditProfileId" id="CreditProfileId" value="{{Auth::user()->CreditProfileId}}">
                            <input type="hidden" class="form-control" name="CreditProfileName" id="CreditProfileName" value="{{Auth::user()->name}}">
                        </div>
                    </div>
                    
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