@extends('frontend.layouts.user')

@section('content')


<div class="dashboard-content py-2 px-4">




    <a href="{{ route('user.donationcal')}}" class="btn btn-primary">Back</a>
    <fieldset >


        <legend>DONATION DETAILS:</legend>
        <div class="row">
            <div class="col-md-12 mt-2 text-center">


                <div class="overflow">
                    <table class="table table-custom shadow-sm bg-white" id="exampleIn">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Income by</th>
                                <th>Income Amount</th>
                                <th>Donation Amount</th>
                            </tr>
                        </thead>
                        <tbody>


                            @foreach ($donation as $data)
                                <tr>
                                    <td>{{ date('d-M, Y', strtotime($data->date)) }}</td>
                                    <td>One-Off</td>
                                    <td>{{$data->income_amount}}</td>
                                    <td> {{$data->donation_amount}}</td>
                                </tr>
                            @endforeach



                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </fieldset>



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
