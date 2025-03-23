@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Balance Transfer Transaction </div>
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
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="complete-tab" data-toggle="tab" href="#complete" role="tab" aria-controls="complete" aria-selected="false">Complete</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cancel-tab" data-toggle="tab" href="#cancel" role="tab" aria-controls="cancel" aria-selected="false">Cancel</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                <table class="table table-donor shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transferred From</th>
                                            <th>Transferred To</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pending as $data)
                                            @php
                                                $transferTo = \App\Models\User::where('id', $data->transfer_to)->first();
                                                $transferFrom = \App\Models\User::where('id', $data->transfer_from)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $data->date ?? ""}} </td>
                                                <td>{{ $transferFrom->email ?? ""}}</td>
                                                <td>{{ $transferTo->email ?? ""}}</td>
                                                <td>£{{ $data->amount}}</td>
                                                <td>Pending</td>
                                                <td>
                                                    <select name="" id="" class="form-control">
                                                        <option value="0|{{$data->id}}" @if($data->status == "0")Selected @endif>Pending</option> 
                                                        <option value="1|{{$data->id}}" @if($data->status == "1")Selected @endif>Complete</option> 
                                                        <option value="2|{{$data->id}}" @if($data->status == "2")Selected @endif>Cancel</option> 
                                                    </select> 
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="complete" role="tabpanel" aria-labelledby="complete-tab">
                                <table class="table table-donor shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transferred From</th>
                                            <th>Transferred To</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($complete as $data)
                                            @php
                                                $transferTo = \App\Models\User::where('id', $data->transfer_to)->first();
                                                $transferFrom = \App\Models\User::where('id', $data->transfer_from)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $data->date ?? ""}} </td>
                                                <td>{{ $transferFrom->email ?? ""}}</td>
                                                <td>{{ $transferTo->email ?? ""}}</td>
                                                <td>£{{ $data->amount}}</td>
                                                <td>Complete</td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="cancel" role="tabpanel" aria-labelledby="cancel-tab">
                                <table class="table table-donor shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transferred From</th>
                                            <th>Transferred To</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cancel as $data)
                                            @php
                                                $transferTo = \App\Models\User::where('id', $data->transfer_to)->first();
                                                $transferFrom = \App\Models\User::where('id', $data->transfer_from)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $data->date ?? ""}} </td>
                                                <td>{{ $transferFrom->email ?? ""}}</td>
                                                <td>{{ $transferTo->email ?? ""}}</td>
                                                <td>£{{ $data->amount}}</td>
                                                <td>Cancel</td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </section>
    </div>
</div>


@endsection

@section('script')
<script type="text/javascript">

$(document).ready(function() {
    
    
    
//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

    var url = "{{URL::to('/admin/balance-transfer-status')}}";

    $('select').on('change', function() {
        $("#loading").show();
        var str =  this.value;
        var ret = str.split("|");
        var status = ret[0];
        var id = ret[1];

        // console.log(status,id);
        $.ajax({
                url: url,
                method: "POST",
                data: {status,id},

                success: function (d) {
                    if (d.status == 303) {
                    }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                        window.setTimeout(function(){location.reload()},500)
                    }
                },
                complete:function(d){
                $("#loading").hide();
                },
                error: function (d) {
                    console.log(d);
                }
            });

    });

});
</script>

@endsection
