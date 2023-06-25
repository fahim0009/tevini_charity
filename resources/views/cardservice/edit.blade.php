@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Available Balance Update</div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <a href="{{ route('cardprofile') }}" type="button" class="btn btn-info">Back</a>
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
                        <form action="{{ route('cardprofile.update') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                            <input type="hidden" name="CreditProfileId" value="{{$data['CreditProfile']['CreditProfileId']}}">
                         <div class="col my-3">
                            <label for="">Name</label>
                           <input type="text" name="CreditProfileName" value="{{$data['CreditProfile']['ProfileName']}}" class="form-control @error('Name') is-invalid @enderror" readonly>
                        </div>

                         <div class="col my-3">
                            <label for="">Available Balance</label>
                           <input type="number" name="AvailableBalance" id="AvailableBalance" placeholder="AvailableBalance" class="form-control @error('AvailableBalance') is-invalid @enderror">
                        </div>

                         <div class="col my-3">
                            <label for="">Comment</label>
                            <textarea name="comment" id="comment" placeholder="comment" class="form-control @error('comment') is-invalid @enderror" cols="30" rows="6"></textarea>
                           {{-- <input type="text" name="comment" id="comment" placeholder="comment" class="form-control @error('comment') is-invalid @enderror"> --}}
                        </div>


                    </div>
                    <div class="col-md-6  my-4  bg-white">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Update</button>
                    </div>
                    </form>
            </div>
        </section>

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
