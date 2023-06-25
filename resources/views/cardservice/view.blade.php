@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Credit Profile View</div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <a href="{{ url()->previous() }}" type="button" class="btn btn-info">Back</a>
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
                        <form >
                            @csrf
                         <div class="col">
                           <p><strong>Credit Profile Id: </strong> {{$data['CreditProfile']['CreditProfileId']}} </p>
                        </div>
                         <div class="col">
                           <p><strong>Profile Name : </strong> {{$data['CreditProfile']['ProfileName']}} </p>
                        </div>

                         <div class="col">
                            <p><strong>Credit Limit : </strong>{{$data['CreditProfile']['CreditLimit']}}</p>
                        </div>
                         <div class="col">
                            <p><strong>Available Balance : </strong>{{$data['CreditProfile']['AvailableBalance']}}</p>
                        </div>

                         <div class="col my-3">
                            <p><strong>Is Pre Paid : </strong>{{($data['CreditProfile']['IsPrePaid'] == 1)?'Yes':'No'}}</p>
                        </div>

                        <div class="col">
                            <p><strong>Balance Due : </strong>{{$data['CreditProfile']['BalanceDue']}}</p>
                        </div>

                        <div class="col">
                            <p><strong>Amount Spent : </strong>{{$data['CreditProfile']['AmountSpent']}}</p>
                        </div>

                        <div class="col">
                            <p><strong>Payment Frequency : </strong>{{$data['CreditProfile']['PaymentFrequency']}}</p>
                        </div>

                        <div class="col">
                            <p><strong>Payment Terms : </strong>{{$data['CreditProfile']['PaymentTerms']}}</p>
                        </div>
                        <div class="col">
                            <p><strong>Payment Type : </strong>{{$data['CreditProfile']['PaymentType']}}</p>
                        </div>

                    </div>
                    <div class="col-md-6  my-4  bg-white">
                    </div>

                    </form>
            </div>
        </section>

    </div>
</div>

@endsection

@section('script')

@endsection
