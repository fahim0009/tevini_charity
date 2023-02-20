@extends('frontend.layouts.user')
@section('content')

@php
    $alltransactions = \App\Models\Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
            ])->orderBy('id','DESC')->get();

    
    $tamount = \App\Models\Usertransaction::where('user_id','=', auth()->user()->id)
                        ->where('status','=', '1')
                        ->orderBy('id','DESC')->get();
@endphp


<div class="row ">
    <div class="col-lg-6">
        <div class="user">
            Welcome, {{auth()->user()->name}}
        </div>
        <br>
        <h4 class="txt-dash">Account Balance</h4>
        <h2 class="amount">{{auth()->user()->balance}} GBP</h2>
        <div class="row my-2">
            <div class="col-lg-6 ">
                <img src="{{ asset('assets/user/images/card.png') }}" class="img-fluid mt-3 mb-2" alt="">
                <a href="#" class="d-block fs-14 txt-theme fw-bold">Order a card</a>
            </div>

            <div class="col-lg-6  pt-3 d-flex flex-column px-4">
                <a href="{{ route('user.makedonation') }}" class="btn-theme bg-primary">Make a
                    donation</a>
                <a href="{{ route('user.orderbook') }}" class="btn-theme bg-secondary">Order voucher books</a>
                <a href="{{ route('stripeDonation')}}" class="btn-theme bg-ternary">Top up account</a>
            </div>
        </div>
        <div class="  p-4 py-5 mt-2" style="background-color: #D9D9D9;">
            <div>
                <div class="txt-secondary fs-32 fw-bold  text-center">GIFT AID DONATIONS</div>  <br>
                <div class="txt-secondary fs-20"> Gift Aid donations for this Tax Year : £{{ $currentyramount }}</div>
                <div class="txt-secondary fs-20"> Gift Aid donations for last Tax Year : £{{ $totalamount }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row mb-5">
            <div class="col-lg-6">
                <div class="user">
                    My transactions
                </div>

            </div>
            <div class="col-lg-6 text-center">
                <a href="#" class="btn-theme bg-ternary">View all transactions</a>
            </div>
        </div>
        <div class="row titleBar my-3 ">
            <div class="col-lg-6">Description</div>
            <div class="col-lg-3">Amount</div>
            <div class="col-lg-3">Balance</div>
        </div>

        <!-- loop start -->
        <div class="row mb-4">
            <div class="date">
                Today
            </div>
            <div class="row">
                <div class="col-lg-6 mt-3">
                    <div class="info">Aim Habonim</div>
                    <span class="fs-16 txt-theme">Online donation</span>
                </div>
                <div class="col-lg-3 mt-3 d-flex align-items-center ">
                    <div class="info">-£18.00</div>
                </div>
                <div class="col-lg-3 mt-3 d-flex align-items-center ">
                    <span class="fs-16 txt-theme">£4.50</span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mt-3">
                    <div class="info">Initact Solutions Ltd.</div>
                    <span class="fs-16 txt-theme">Company donation</span>
                </div>
                <div class="col-lg-3 mt-3 d-flex align-items-center ">
                    <div class="info txt-primary">£20.00</div>
                </div>
                <div class="col-lg-3 mt-3 d-flex align-items-center ">
                    <span class="fs-16 txt-theme">£23.50</span>
                </div>
            </div>
        </div>

        <!-- end -->

        <!-- loop start -->

        <div class="row mb-4">
            <div class="date">
                21 January 23
            </div>
            <div class="row">
                <div class="col-lg-6 mt-3">
                    <div class="info">Aim Habonim</div>
                    <span class="fs-16 txt-theme">Online donation</span>
                </div>
                <div class="col-lg-3 mt-3 d-flex align-items-center ">
                    <div class="info">-£18.00</div>
                </div>
                <div class="col-lg-3 mt-3 d-flex align-items-center ">
                    <span class="fs-16 txt-theme">£4.50</span>
                </div>
            </div> 
        </div>

        <!-- end -->
        <!-- loop start -->

        <div class="row mb-4">
            <div class="date">
                20 January 23
            </div>
            <div class="row">
                <div class="col-lg-6 mt-2">
                    <div class="info">Bikur Cholim D’satamar</div>
                    <span class="fs-16 txt-theme">Online donation</span>
                </div>
                <div class="col-lg-3 mt-2 d-flex align-items-center ">
                    <div class="info">-£180.00</div>
                </div>
                <div class="col-lg-3 mt-2 d-flex align-items-center ">
                    <span class="fs-16 txt-theme">£203.50</span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mt-2">
                    <div class="info">Bikur Cholim D’satamar</div>
                    <span class="fs-16 txt-theme">Online donation</span>
                </div>
                <div class="col-lg-3 mt-2 d-flex align-items-center ">
                    <div class="info txt-primary">-£180.00</div>
                </div>
                <div class="col-lg-3 mt-2 d-flex align-items-center ">
                    <span class="fs-16 txt-theme">£203.50</span>
                </div>
            </div>
            
        </div>

        <!-- end -->

    </div>

</div>
@endsection
