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


        <section class="px-4 py-5 bg-light" id="addThisFormContainer">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0 text-secondary fw-bold">Create Product Fee</h5>
                                <small class="text-muted">Enter the details below to set up a new fee structure.</small>
                            </div>
                            
                            <div class="card-body p-4">
                                <form action="{{ route('productfee.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                                    @csrf
                                    
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <label for="Name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" name="Name" id="Name" 
                                                placeholder="e.g. Standard Subscription" 
                                                class="form-control @error('Name') is-invalid @enderror" required>
                                            @error('Name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr class="my-4 text-muted opacity-25">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="FinanceFee" class="form-label fw-semibold">Finance Fee (%)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                                <input type="number" step="0.01" name="FinanceFee" id="FinanceFee" 
                                                    class="form-control @error('FinanceFee') is-invalid @enderror" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="CardFee" class="form-label fw-semibold">Card Transaction Fee</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                                <input type="number" step="0.01" name="CardFee" id="CardFee" 
                                                    class="form-control @error('CardFee') is-invalid @enderror" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="CardIsueeFee" class="form-label fw-semibold">Card Issuance Fee</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="CardIsueeFee" id="CardIsueeFee" 
                                                    class="form-control @error('CardIsueeFee') is-invalid @enderror" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="FXFee" class="form-label fw-semibold">FX Fee (Foreign Exchange)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                                                <input type="number" step="0.01" name="FXFee" id="FXFee" 
                                                    class="form-control @error('FXFee') is-invalid @enderror" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-5">
                                        <button type="button" class="btn btn-light px-4 border" id="FormCloseBtn">Cancel</button>
                                        <button type="submit" class="btn btn-secondary px-5 shadow-sm">
                                            <i class="bi bi-check2-circle me-1"></i> Create Product Fee
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </div>
                </div>
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
