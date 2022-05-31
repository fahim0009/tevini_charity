@extends('layouts.admin')
@section('content')
<div class="rightSection">
    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Donation Records </div>
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

        <section class="px-4"  id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                    <div class="table-responsive">
                        <table class="table table-custom shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Beneficiary</th>
                                    <th>amount</th>
                                    <th>Annonymous Donation</th>
                                    <th>Standing Order</th>
                                    <th>Starting</th>
                                    <th>Interval</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($donation as $data)
                                    <tr>
                                        <td><span style="display:none;">{{ $data->id }}</span>{{$data->created_at->format('d/m/Y')}}</td>
                                        <td>{{$data->user->name}}</td>
                                        <td>{{$data->charity->name}}</td>
                                        <td>{{$data->amount}}</td>
                                        <td>@if ($data->ano_donation == "true")
                                            Yes
                                        @else
                                            No
                                        @endif</td>
                                        <td>@if ($data->standing_order == "true")
                                            Yes
                                        @else
                                            No
                                        @endif</td>
                                        <td>{{$data->starting}}</td>
                                        <td>{{$data->interval}}</td>
                                        <td>{{$data->mynote}}</td>
                                        <td>
                                            @if($data->status =="1")
                                            <button type="button" class="btn btn-sm btn-success">Confirm</button>
                                            @elseif($data->status =="3")
                                            <button type="button" class="btn btn-danger">Cancel</button>
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">No record found</td>
                                </tr>
                                @endforelse




                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')


@endsection
