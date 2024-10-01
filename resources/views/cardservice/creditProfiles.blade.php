@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Credit Profile List </div>
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

        

        <section class="px-4"  id="">
            <div class="row my-3">
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Credit Profile Id</th>
                                    <th>Profile Name</th>
                                    <th>email</th>
                                    <th>Credit Limit</th>
                                    <th>Available Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($data as $profile)
                                    <tr>
                                        <td>{{ $profile->CreditProfileId }}</td>
                                        <td>{{ $profile->name }}</td>
                                        <td>{{ $profile->email }}</td>
                                        <td>{{ $profile->overdrawn_amount }}</td>
                                        <td>{{ $profile->balance }}</td>
                                        <td>
                                            <a href="{{ route('cardprofileview', ['id' => $profile->CreditProfileId ]) }}" class="btn btn-success" type="button">View</a>
                                            <a href="{{ route('cardprofileedit', ['id' => $profile->CreditProfileId ]) }}" class="btn btn-primary">Update Balance</a>
                                            {{-- <a href="{{ route('cardprofilelimite', ['id' => $profile->CreditProfileId ]) }}" class="btn btn-primary">Update Limit</a> --}}
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


        // $('#cardprofileTable').DataTable();
$('#cardprofileTable').DataTable({
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[ 0, "desc" ]], //or asc 
        });

    $(document).ready(function () {


        


    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
        $('#errMessage').fadeOut('fast');
    }, 3000);

     //header for csrf-token is must in laravel
     $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

    });

</script>


@endsection
