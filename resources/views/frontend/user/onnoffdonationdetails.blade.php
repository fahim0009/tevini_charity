@extends('frontend.layouts.user')

@section('content')


<div class="dashboard-content py-2 px-4">




    <a href="{{ route('user.donationcal')}}" class="btn-theme bg-secondary text-white">Back</a>


    <div class="row mb-5 mt-3">
        
        <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">DONATION DETAILS: </div> <br>
        <div class="col-lg-12 mt-2">
            <div class="stsermsg"></div>
            <div class="data-container">
                <table class="table table-theme mt-4" id="exampleIn">
                    <thead>
                        <tr> 
                            <th scope="col">Date</th>
                            <th scope="col">Income by</th>
                            <th scope="col">Income Amount</th>
                            <th scope="col">Donation Amount</th>
                            <th scope="col">Description</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($donation as $data)
                            <tr>
                                <td class="fs-16 txt-secondary">{{ date('d-M, Y', strtotime($data->date)) }}</td>
                                <td class="fs-16 txt-secondary">One-Off</td>
                                <td class="fs-16 txt-secondary">{{$data->income_amount}}</td>
                                <td class="fs-16 txt-secondary"> {{$data->donation_amount}}</td>
                                <td class="fs-16 txt-secondary"> {{$data->income_title}}</td>
                            </tr>
                        @endforeach
    
                    </tbody>
                </table>
            </div>
        </div>
    </div>









</div>
@endsection


@section('script')
<script>
$(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //


    });
</script>

@endsection
