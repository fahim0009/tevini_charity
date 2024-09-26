@extends('layouts.admin')
@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Pay</div>

            <a href="{{ route('charitylist') }}"><button type="button" class="btn btn-success">back</button></a>
        </div>
    </section>
<!-- Image loader -->
    {{-- <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
   </div> --}}
 <!-- Image loader -->

 <section class="px-4 no-print">
    <div class="row my-3">
        <div class="container">
            <div class="col-md-12 my-3">
                <p>**Please note that if you are topping up your account using a credit/debit card there will be an additional fee of 2% on top of the standard 5% commission fee alternatively you can top up by transfer to the following: Tevini Ltd S/C 40-52-40 A/C 00024463.</p>
            </div>
            
        </div>
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


  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 my-3">
            <div class="row">
                <div class="col-md-6 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Details </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $charity->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $charity->email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>{{ $charity->number }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>{{ $charity->address }}</td>
                                </tr>
                                <tr>
                                    <td>Account</td>
                                    <td>{{ $charity->acc_no }}</td>
                                </tr>
                                <tr>
                                    <td>Balance :</td>
                                    <td>{{ $charity->balance }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <form class="form-inline" id="payForm" method="POST" action="{{ route('charity.pay.store') }}"  enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="form-group my-2">
                                    <label for=""><small>Amount </small> </label>
                                    <input class="form-control mr-sm-2" type="text" name="balance" id="balance" required placeholder="Amount">
                                </div>
                                <div class="form-group my-2">
                                    <label for=""><small>Note </small> </label>
                                    <input class="form-control mr-sm-2" type="text" name="note" id="note" required placeholder="Note">
                                </div>
                                <div class="form-group my-2">
                                <label for=""><small>Source</small> </label>
                                <select name="source" id="source" class="form-control">
                                    <option value="Bank">Bank</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Card">Card</option>
                                </select>
                                </div>
                                
                                <div class="form-group my-2">
                                    <input type="checkbox" name="sendemail" >
                                    <label class="form-check-label" for="sendemail">
                                        I want to send email.
                                    </label>
                                </div>

                                <input type="hidden" name="topupid" id="topupid" value="{{ $charity->id }}">
                                <div class="form-group my-2">
                                    <button type="submit" id="payBtn" class="my-2 btn btn-sm btn-info text-white">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </section>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    $("#payForm").submit(function(){

        $("#payBtn").prop("disabled", true).css("cursor", "progress");


    });
    
 });
</script>
@endsection

