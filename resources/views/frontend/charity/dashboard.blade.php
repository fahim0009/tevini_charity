@extends('frontend.layouts.charity')
@section('content')
<h1>Charity dashboard {{ auth('charity')->user()->name }}</h1>

@endsection
