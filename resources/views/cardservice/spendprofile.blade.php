@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Spend Profile List</div>
            </div>
        </section>

        {{-- <section class="profile purchase-status">
            <div class="title-section">
                <a href="{{ route('cardprofile') }}" type="button" class="btn btn-info">Back</a>
            </div>
        </section> --}}
        <section class="profile purchase-status">
            <div class="title-section">
                <button id="newBtn" type="button" class="btn btn-info">Add New</button>
            </div>
        </section>


        @if(session()->has('success'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('success') }}</div>
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


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('spendprofilestore') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf

                        <div class="col my-3">
                            <label for="">ProfileName</label>
                           <input type="text" name="ProfileName" id="ProfileName" class="form-control" >
                        </div>

                        <div class="col my-3">
                            <label for="MaxSingleTxnValue">MaxSingleTxnValue</label>
                           <input type="number" name="MaxSingleTxnValue" id="MaxSingleTxnValue" class="form-control" value="10000" >
                        </div>

                        <div class="col my-3">
                            <label for="MaxDailyTxnTotalValue">MaxDailyTxnTotalValue</label>
                           <input type="number" name="MaxDailyTxnTotalValue" id="MaxDailyTxnTotalValue" class="form-control" value="10000" >
                        </div>

                        <div class="col my-3">
                            <label for="MaxDailyTotalTxns">MaxDailyTotalTxns</label>
                           <input type="number" name="MaxDailyTotalTxns" id="MaxDailyTotalTxns" class="form-control" value="10000" >
                        </div>

                        <div class="col my-3">
                            <label for="MinSingleTxnValue">MinSingleTxnValue</label>
                           <input type="number" name="MinSingleTxnValue" id="MinSingleTxnValue" class="form-control"  value="1">
                        </div>

                        <div class="col my-3">
                            <label for="MaxSpend4Days">MaxSpend4Days</label>
                           <input type="number" name="MaxSpend4Days" id="MaxSpend4Days" class="form-control"  value="100000">
                        </div>


                         
                    </div>
                    <div class="col-md-6  my-4  bg-white">


                        <div class="col my-3">
                            <label for="MaxWeeklyTxnValue">MaxWeeklyTxnValue</label>
                           <input type="number" name="MaxWeeklyTxnValue" id="MaxWeeklyTxnValue" class="form-control"  value="100000">
                        </div>

                        <div class="col my-3">
                            <label for="MaxMonthlyTxnValue">MaxMonthlyTxnValue</label>
                           <input type="number" name="MaxMonthlyTxnValue" id="MaxMonthlyTxnValue" class="form-control"  value="1000000">
                        </div>

                        <div class="col my-3">
                            <label for="DailyVelocity">DailyVelocity</label>
                           <input type="number" name="DailyVelocity" id="DailyVelocity" class="form-control"  value="10000">
                        </div>

                        <div class="col my-3">
                            <label for="WeeklyVelocity">WeeklyVelocity</label>
                           <input type="number" name="WeeklyVelocity" id="WeeklyVelocity" class="form-control"  value="100000">
                        </div>

                        <div class="col my-3">
                            <label for="MonthlyVelocity">MonthlyVelocity</label>
                           <input type="number" name="MonthlyVelocity" id="MonthlyVelocity" class="form-control"  value="1000000">
                        </div>

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Create</button>
                        <a class="btn btn-warning mt-2 text-white" id="FormCloseBtn">close</a>
                    </div>
                    </form>
            </div>
        </section>

        
        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>SpendProfileId</th>
                                    <th>ProfileName</th>
                                    <th>MaxSingleTxnValue</th>
                                    <th>MaxDailyTxnTotalValue</th>
                                    <th>MaxDailyTotalTxns</th>
                                    {{-- <th>MinSingleTxnValue</th>
                                    <th>MaxSpend4Days</th>
                                    <th>MaxTotalTxns4Days</th>
                                    <th>MaxWeeklyTxnValue</th>
                                    <th>MaxMonthlyTxnValue</th>
                                    <th>DailyVelocity</th>
                                    <th>WeeklyVelocity</th> --}}
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($data['SpendProfileList'] as $profile)
                                    <tr>
                                        <td>{{ $profile['SpendProfileId'] }}</td>
                                        <td>{{ $profile['ProfileName'] }}</td>
                                        <td>{{ $profile['MaxSingleTxnValue'] }}</td>
                                        <td>{{ $profile['MaxDailyTxnTotalValue'] }}</td>
                                        <td>{{ $profile['MaxDailyTotalTxns'] }}</td>
                                        {{-- <td>{{ $profile['MinSingleTxnValue'] }}</td>
                                        <td>{{ $profile['MaxSpend4Days'] }}</td>
                                        <td>{{ $profile['MaxTotalTxns4Days'] }}</td>
                                        <td>{{ $profile['MaxWeeklyTxnValue'] }}</td>
                                        <td>{{ $profile['MaxMonthlyTxnValue'] }}</td>
                                        <td>{{ $profile['DailyVelocity'] }}</td>
                                        <td>{{ $profile['WeeklyVelocity'] }}</td> --}}
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
    
    });
    
    </script>
@endsection
