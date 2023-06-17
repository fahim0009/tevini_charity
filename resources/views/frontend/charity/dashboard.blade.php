@extends('frontend.layouts.charity')
@section('content')
@php
    $pending_transactions = \App\Models\Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth('charity')->user()->id],
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
</div>

</div>
@endsection
