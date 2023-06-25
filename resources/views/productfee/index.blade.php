@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Product Fee List </div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <button id="newBtn" type="button" class="btn btn-info">Add New</button>
            </div>
        </section>



        @if(session()->has('message'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('message') }}</div>
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


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('productfee.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                         <div class="col my-3">
                            <label for="">Name</label>
                           <input type="text" name="Name" id="Name" placeholder="Name" class="form-control @error('Name') is-invalid @enderror">
                        </div>

                         <div class="col my-3">
                            <label for="">FinanceFee</label>
                           <input type="number" name="FinanceFee" id="FinanceFee" placeholder="FinanceFee" class="form-control @error('FinanceFee') is-invalid @enderror">
                        </div>

                         <div class="col my-3">
                            <label for="">CardFee</label>
                           <input type="number" name="CardFee" id="CardFee" placeholder="CardFee" class="form-control @error('CardFee') is-invalid @enderror">
                        </div>

                         <div class="col my-3">
                            <label for="">CardIsueeFee</label>
                           <input type="number" name="CardIsueeFee" id="CardIsueeFee" placeholder="CardIsueeFee" class="form-control @error('CardIsueeFee') is-invalid @enderror">
                        </div>

                         <div class="col my-3">
                            <label for="">FXFee</label>
                           <input type="number" name="FXFee" id="FXFee" placeholder="FXFee" class="form-control @error('FXFee') is-invalid @enderror">
                        </div>

                    </div>
                    <div class="col-md-6  my-4  bg-white">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Create</button>
                        <a class="btn btn-warning mt-2 text-white" id="FormCloseBtn">close</a>
                    </div>
                    </form>
            </div>
        </section>


        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>ProductFeeId</th>
                                    <th>Name</th>
                                    <th>FinanceFee</th>
                                    <th>CardFee</th>
                                    <th>CardIsueeFee</th>
                                    <th>FXFee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['ProductFees'] as $productFee)
                                    <tr>
                                        <td>{{ $productFee['ProductFeeId'] }}</td>
                                        <td>{{ $productFee['Name'] }}</td>
                                        <td>{{ $productFee['FinanceFee'] }}</td>
                                        <td>{{ $productFee['CardFee'] }}</td>
                                        <td>{{ $productFee['CardIsueeFee'] }}</td>
                                        <td>{{ $productFee['FXFee'] }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </section>
    </div>
</div>


@endsection

@section('script')

<script>

$(document).ready(function () {
    $("#addThisFormContainer").hide();
    $("#newBtn").click(function(){
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);

    });
    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });
    function clearform(){
        $('#createThisForm')[0].reset();
    }

});

</script>

@endsection
