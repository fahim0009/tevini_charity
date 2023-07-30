@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Authorisation </div>
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
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Utid</th>
                                    <th>messageID</th>
                                    <th>instCode</th>
                                    <th>txnType</th>
                                    <th>msgType</th>
                                    <th>tlogId</th>
                                    <th>view</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $data)
                                    <tr>
                                        <td>{{ $data->Utid }}</td>
                                        <td>{{ $data->messageID }}</td>
                                        <td>{{ $data->instCode }}</td>
                                        <td>{{ $data->txnType }}</td>
                                        <td>{{ $data->msgType }}</td>
                                        <td>{{ $data->tlogId }}</td>
                                        <td>
                                            <a href="{{route('expiredDetails', $data->id)}}" class="btn btn-success">View</a>
                                        </td>
                                    </tr>
                                @empty
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

<script>


</script>

@endsection
