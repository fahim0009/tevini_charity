@extends('frontend.layouts.user')
@section('content')

<div class="row ">
    <div class="col-md-12">
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
    </div>
</div>

<div class="row ">
    <div class="col-lg-4">
        <div class="row my-2">
            <div class="col-lg-12 ">
                <img src="{{ asset('assets/user/images/card.png') }}" class="img-fluid mt-3 mb-2" alt="">
            </div>
        </div>
    </div>
    <div class="col-lg-4">

        

        @if ($data)
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
        @else
            <form  action="{{ route('cardprofile.store') }}" method="POST" enctype="multipart/form-data" >
                @csrf
                <button class="d-block btn-theme bg-secondary mt-5">Create credit profile</button>
            </form>
        @endif
        

    </div>

    
    <div class="col-lg-4">
        @if(session()->has('successmsg'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('successmsg') }}</div>
            </div>
        </section>
        @endif
        @if ($data)
            {{-- <a href="{{route('applyforcard')}}" class="d-block btn-theme bg-secondary mt-5">Apply for card</a> --}}

            @if ($CardHolderId)
            <a href="#" class="d-block btn-theme bg-primary">Update cardholder</a>
            <a href="{{route('orderCard')}}" class="d-block btn-theme bg-secondary">Order Card</a>
            
            <a href="{{route('cardActivation')}}" class="d-block btn-theme bg-secondary">Card Activation</a>
            
            @else
            <a href="{{route('applyforcardholder')}}" class="d-block btn-theme bg-primary">Apply for cardholder</a>
            @endif
        @endif
    </div>

</div>
@endsection

@section('script')


<script>

</script>

@endsection
