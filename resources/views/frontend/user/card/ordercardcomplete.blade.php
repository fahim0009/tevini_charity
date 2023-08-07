@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Order Card
            </div>
        </div>
    </div>


    <section class="px-4">
        <div class="row my-3">
            <div class="alert alert-success" id="successMessage"> You have already ordered card {{$order->created_at}}. Here is details. Thank you.</div>
        </div>
    </section>


    <form  action="" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-3">
            <div class="row">
                <div class="col-md-12" style="display: none">
                    <label for="">Title</label>
                    <input type="text" name="Title" id="Title" placeholder="Title" class="form-control" value="Test Title" readonly value="{{$CardHolderData->Title}}">
                    <input type="hidden" name="CardholderId" id="CardholderId" value="{{$CardHolderData->CardHolderId}}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="">FirstName</label>
                    <input type="text" name="FirstName" id="FirstName" placeholder="FirstName" class="form-control" value="{{$CardHolderData->FirstName}}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="LastName">Last Name</label>
                    <input type="text" name="LastName" id="LastName" placeholder="LastName" class="form-control" value="{{$CardHolderData->LastName}}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="Address1">Address1</label>
                    <input type="text" name="Address1" id="Address1" placeholder="Address1" class="form-control" value="{{$CardHolderData->Address1}}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="Address2">Address2</label>
                    <input type="text" name="Address2" id="Address2" placeholder="Address2" class="form-control" value="{{$CardHolderData->Address2}}" readonly>
                </div>

                {{-- <div class="col-md-12">
                    <label for="Address3">Address3</label>
                    <input type="text" name="Address3" id="Address3" placeholder="Address3" class="form-control" value="Address3">
                </div> --}}
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label for="">HouseNumberOrBuilding</label>
                    <input type="text" name="HouseNumberOrBuilding" id="HouseNumberOrBuilding" placeholder="HouseNumberOrBuilding" class="form-control" value="{{$CardHolderData->HouseNumberOrBuilding}}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="">City</label>
                    <input type="text" name="City" id="City" placeholder="City" class="form-control" value="{{$CardHolderData->City}}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="">County</label>
                    <input type="text" name="State" id="State" placeholder="State" class="form-control" value="{{$CardHolderData->State}}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="">PostCode</label>
                    <input type="text" name="PostCode" id="PostCode" placeholder="PostCode" class="form-control" value="{{$CardHolderData->PostCode}}" readonly>
                </div>
            </div>

            

            <div class="row">
                <div class="col-md-6">
                    <label for="">RecipientEmail</label>
                    <input type="email" name="RecipientEmail" id="RecipientEmail" placeholder="RecipientEmail" class="form-control" value="{{$CardHolderData->Email}}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="">Dob </label>
                    <input type="date" name="Dob" id="Dob" placeholder="Dob" class="form-control" value="{{$CardHolderData->DateOfBirth}}" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" style="display: none">
                    <label for="SecondSurname">SecondSurname</label>
                    <input type="text" name="SecondSurname" id="SecondSurname" placeholder="SecondSurname" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label for="NameOnCard">NameOnCard</label>
                    <input type="text" name="NameOnCard" id="NameOnCard" placeholder="NameOnCard" class="form-control"   value="{{$order->NameOnCard}}" readonly>
                </div>
                <div class="col-md-6" style="display: none">
                    <label for="AdditionalCardEmbossData">AdditionalCardEmbossData</label>
                    <input type="text" name="AdditionalCardEmbossData" id="AdditionalCardEmbossData" placeholder="AdditionalCardEmbossData" class="form-control"  value="{{$order->AdditionalCardEmbossData}}" readonly>
                </div>


            </div>
            
            <div>
                <div class="col-lg-12 mt-4">
                    <div class="form-group ">
                        
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