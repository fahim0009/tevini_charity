@extends('frontend.layouts.user')
@section('content')


@php
    $chkCardAvailable = \App\Models\CardProduct::where('user_id', Auth::user()->id)->first();
@endphp

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

            {{-- <div class="col">
                <p><strong>Credit Limit : </strong>{{$data['CreditProfile']['CreditLimit']}}</p>
            </div> --}}
            <div class="col">
                <p><strong>Available Balance : </strong>{{$data['CreditProfile']['AvailableBalance']}}</p>
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
            <a href="{{route('cardholderUpdate')}}" class="d-block btn-theme bg-primary">Update cardholder </a>
            @if (isset($chkCardAvailable->cardNumber))
                <a href="{{route('cardSetPin')}}" class="d-block btn-theme bg-primary">Set Pin</a>
                <a href="{{route('cardStatusChange')}}" class="d-block btn-theme bg-primary">Change Status</a>
            @else
                
            <a href="{{route('orderCard')}}" class="d-block btn-theme bg-secondary">Order Card</a>
            <a href="{{route('cardActivation')}}" class="d-block btn-theme bg-secondary">Card Activation</a>
            @endif
            
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
