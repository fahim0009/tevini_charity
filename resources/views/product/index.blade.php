@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Apply for card List (Product) </div>
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




        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>CardProgram</th>
                                    <th>Product Code</th>
                                    <th>Product Type</th>
                                    <th>CorporateLoadMaxBalance</th>
                                    <th>CorporateLoadMinLoadAmount</th>
                                    <th colspan="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['ProductList'] as $productFee)
                                    <tr>
                                        <td>{{ $productFee['CardProgram'] }}</td>
                                        <td>{{ $productFee['ProductCode'] }}</td>
                                        <td>{{ $productFee['ProductType'] }}</td>
                                        <td>{{ $productFee['CorporateLoadMaxBalance'] }}</td>
                                        <td>{{ $productFee['CorporateLoadMinLoadAmount'] }}</td>
                                        <td>
                                            <a href="{{ route('product.edit', ['id' => $productFee['ProductCode'] ]) }}" class="btn btn-primary">Edit</a>
                                        </td>
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


</script>

@endsection
