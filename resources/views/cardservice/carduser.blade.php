@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">All Card User </div>
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
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $data)
                                    <tr>
                                        <td>{{ \App\Models\User::where('id', $data->user_id)->first()->name }}</td>
                                        <td>{{ \App\Models\User::where('id', $data->user_id)->first()->email }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary acc" data-bs-toggle="modal" data-bs-target="#exampleModal{{$data->id}}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                      <!-- Modal -->
                                    <div class="modal fade" id="exampleModal{{$data->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Transaction Date</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="ermsg"></div>
                                                <form action="{{route('cardservice.tran')}}">
                                                @csrf

                                                    <div class="row">

                                                        <div class="mb-3">
                                                            <label for="fromDate" class="form-label">From Date</label>
                                                            <input type="date" class="form-control" id="fromDate" name="fromDate" required>
                                                            <input type="hidden" class="form-control" value="{{$data->CardProxyId}}" id="CardProxyId" name="CardProxyId">
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="endDate" class="form-label">End Date</label>
                                                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                        </div>
                                        </div>
                                    </div>
                                    <!-- Modal End -->

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
