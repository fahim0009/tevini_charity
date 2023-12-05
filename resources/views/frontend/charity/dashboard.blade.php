@extends('frontend.layouts.charity')
@section('content')
@php
    $pending_transactions = \App\Models\Usertransaction::where([
            ['t_type','=', 'Out'],
            ['charity_id','=', auth('charity')->user()->id],
            ['pending','=', '0']
        ])->sum('amount');

use Illuminate\Support\Carbon;
@endphp
<div class="row ">

<div class="col-lg-6">
    <div class="user">
        Welcome, {{auth('charity')->user()->name}}
    </div>
    <br>
    <h4 class="txt-dash">Account Balance</h4>
    <h2 class="amount">{{auth('charity')->user()->balance}} GBP</h2>
    <p>Pending Balance: {{number_format($pending_transactions, 2)}} GBP</p>

    <div class="row my-2">

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



        <div class="col-lg-12">
            <form action="{{route('charity.urgent_request')}}" method="POST">
                @csrf
                <input type="hidden" id="charityname" name="charityname" value="{{auth('charity')->user()->name}}">
                <input type="hidden" id="charityamnt" name="charityamnt" value="{{auth('charity')->user()->balance}}">
                <input type="hidden" id="charityid" name="charityid" value="{{auth('charity')->user()->id}}">
                <button type="submit" class="btn-theme bg-primary d-block fs-14 txt-theme fw-bold">URGENT REQUEST</button>
            </form>
        </div>
    </div>


</div>



</div>
@endsection
