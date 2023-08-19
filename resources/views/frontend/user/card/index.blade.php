@extends('frontend.layouts.user')
@section('content')


@php
    $chkCardAvailable = \App\Models\CardProduct::where('user_id', Auth::user()->id)->first();
    $chkcardorder = \App\Models\CardOrder::where('user_id', Auth::user()->id)->first();
    $cardsts = \App\Models\CardStatus::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();
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

            @if (isset($cardsts))
                
                <div class="col">
                    <p><strong>Card Status : </strong>{{$cardsts->Status}}</p>
                </div>
            @endif
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
                <a href="{{route('cardSetPin')}}" class="d-block btn-theme bg-primary">Get Pin</a>
                <a href="{{route('cardStatusChange')}}" class="d-block btn-theme bg-primary">Change Status</a>
            @else
                

                @if (isset($chkcardorder))
                    <a href="{{route('orderCard')}}" class="d-block btn-theme bg-secondary">Order Card Details</a>
                    <a href="{{route('cardActivation')}}" class="d-block btn-theme bg-secondary">Card Activation</a>
                @else
                
                <a href="{{route('orderCard')}}" class="d-block btn-theme bg-secondary">Order Card</a>

                @endif


            @endif
            
            @else
            <a href="{{route('applyforcardholder')}}" class="d-block btn-theme bg-primary">Apply for cardholder</a>
            @endif
        @endif
    </div>

</div>
<div class="row mt-5">
    <div class="col-lg-12">
        <p>
            “The Tevini Prepaid Mastercard® is issued by PayrNet Limited and licensed by Mastercard International Incorporation. PayrNet Limited is authorised by the Financial Conduct Authority (FCA) to conduct electronic money service activities under the Electronic Money Regulations 2011 (Firm Reference Number 900594). Mastercard is a registered trademark, and the circles design is a trademark of Mastercard International Incorporated.”
        </p>
    </div>
</div>
@endsection

@section('script')
<script>

</script>
@endsection
