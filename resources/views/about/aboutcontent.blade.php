@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">About Content </div>
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
                        <table class="table table-custom shadow-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Title1</th>
                                    <th>Title2</th>
                                    <th>Title3</th>
                                    <th>Turnover Title</th>
                                    <th>Profit Title</th>
                                    <th>Turnover Image</th>
                                    <th>Profit Image</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($about as $data)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$data->title1}}</td>
                                        <td>{{$data->title2}}</td>
                                        <td>{{$data->title3}}</td>
                                        <td>{{$data->turnover_title}}</td>
                                        <td>{{$data->profit_title}}</td>
                                        <td>{{$data->turnover_image}}</td>
                                        <td>{{$data->profit_image}}</td>
                                        <td>
                                        <a href="{{ route('aboutcontent.edit', encrypt($data->id))}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse




                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')

<script>
    $(document).ready(function () {


        $("#addThisFormContainer").hide();
        $("#newBtn").click(function(){
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);

        });
        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        function clearform(){
                $('#createThisForm')[0].reset();
            }

            setTimeout(function() {
                $('#successMessage').fadeOut('fast');
                $('#errMessage').fadeOut('fast');
            }, 3000);



       

    });





</script>
@endsection
