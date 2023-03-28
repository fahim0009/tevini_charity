@extends('frontend.layouts.charity')
@section('content')

<div class="row ">

<div class="col-lg-6">
    <div class="user">
        Welcome, {{auth('charity')->user()->name}}
    </div>
    <br>
    <h4 class="txt-dash">Account Balance</h4>
    <h2 class="amount">{{auth('charity')->user()->balance}} GBP</h2>
</div>

</div>
@endsection
