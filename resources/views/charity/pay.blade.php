@extends('layouts.admin')

@section('content')


<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Pay</div>

            <a href="{{ route('charitylist') }}"><button type="button" class="btn btn-success">back</button></a>
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
                    <form class="form-inline" method="POST" action="{{ route('charity.pay.store') }}"  enctype="multipart/form-data">
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
                                <input type="hidden" name="topupid" id="topupid" value="{{ $charity->id }}">
                                    <div class="form-group my-2">
                                    <button type="submit" class="my-2 btn btn-sm btn-info text-white">Add</button>
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
