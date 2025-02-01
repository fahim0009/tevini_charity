@extends('layouts.admin')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
<style>
    .chkCircle{
    height: 25px;
    width: 25px;
    vertical-align: middle;
    }

</style>
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')
  <section class="">
   <div class="dashboard-content">

    <section class="profile purchase-status px-4">
        <div class="title-section">
            <span class="iconify" data-icon="clarity:heart-solid"></span>
            <div class="mx-2">TDF Transfer</div>
        </div>

        <section class="px-4">
            <div class="row my-3">
                <div class="ermsg"></div>
            </div>
        </section>

        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
        </div>
        <!-- Image loader -->


        <div class="row mt-3">
            <div class="card">
                <div class="col-md-12 text-muted bg-white  p-5">
                    <form action="{{ route('donation.store') }}" method="POST" enctype="multipart/form-data" class="gdp-form px-5">
                            @csrf
                        <div class="row">
                            <div class="col-md-4 ">
                                <input type="text" placeholder="TDF Account Number" name="tdfaccount" id="tdfaccount" class="form-control @error('tdfaccount') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                
                                <input type="number" placeholder="Amount to Transfer" name="tdfamount" id="tdfamount" class="form-control @error('tdfamount') is-invalid @enderror" min="18">

                            </div>
                            <input type="hidden" value="{{$donor_id}}" id="donner_id">
                            <div class="col-md-12 my-2">
                                <input type="button" id="addBtn" value="Transfer to TDF" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row mt-3">
            <div class="card">
                <div class="col-md-12 text-muted bg-white  p-5">

                    @php
                        $tdftransactions = \App\Models\TdfTransaction::orderby('id','DESC')->where('user_id', $donor_id)->get();
                        $completedtdftransactions = \App\Models\TdfTransaction::orderby('id','DESC')->where('user_id', $donor_id)->where('status', 1)->get();
                    @endphp
                    
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="incoming-tab" data-toggle="tab" href="#incoming" role="tab" aria-controls="incoming" aria-selected="true">All Transactions</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="outgoing-tab" data-toggle="tab" href="#outgoing" role="tab" aria-controls="outgoing" aria-selected="false">Completed Transactions</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="incoming" role="tabpanel" aria-labelledby="incoming-tab">

                            <h3 class="text-center">{{ \App\Models\User::where('id', $donor_id)->first()->name }}</h3>
                            <h4 class="text-center">{{ \App\Models\User::where('id', $donor_id)->first()->email }}</h4>


                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>TDF Account</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tdftransactions as $data)
                                    <tr>
                                        <td>{{ $data->issue_date}} </td>
                                        <td>{{ $data->tdfaccount}} </td>
                                        <td>£{{ $data->tdf_amount}}</td>
                                        <td>@if($data->status =="0")
                                            Pending
                                            @elseif($data->status =="1")
                                            Complete
                                            @elseif($data->status =="3")
                                            Cancel
                                            @endif
                                        </td>

                                        <td>
                                            <select name="" id="" class="form-control">

                                                @if ($data->status == "0")
                                                    <option value="0|{{$data->id}}" @if($data->status == "0")Selected @endif>Pending</option> 
                                                    <option value="1|{{$data->id}}" @if($data->status == "1")Selected @endif>Complete</option> 
                                                    <option value="3|{{$data->id}}" @if($data->status == "3")Selected @endif>Cancel</option>
                                                @else
                                                    <option value="">
                                                        @if($data->status =="0")
                                                        Pending
                                                        @elseif($data->status =="1")
                                                        Complete
                                                        @elseif($data->status =="3")
                                                        Cancel
                                                        @endif    
                                                    </option> 
                                                @endif


                                                 
                                            </select> 
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="outgoing" role="tabpanel" aria-labelledby="outgoing-tab">

                            <h3 class="text-center">{{ \App\Models\User::where('id', $donor_id)->first()->name }}</h3>
                            <h4 class="text-center">{{ \App\Models\User::where('id', $donor_id)->first()->email }}</h4>


                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>TDF Account</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($completedtdftransactions as $data)
                                    <tr>
                                        <td>{{ $data->issue_date}} </td>
                                        <td>{{ $data->tdfaccount}} </td>
                                        <td>£{{ $data->tdf_amount}}</td>
                                        <td>@if($data->status =="0")
                                            Pending
                                            @elseif($data->status =="1")
                                            Complete
                                            @elseif($data->status =="3")
                                            Cancel
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>




    </section>
</div>
</section>
</div>
@endsection
@section('script')
<script>
     $(document).ready(function () {



        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            //  make doantion start
            $("#addBtn").click(function(){
                 $("#loading").show();
                    var donner_id= $("#donner_id").val();
                    var tdfamount= $("#tdfamount").val();
                    var tdfaccount= $("#tdfaccount").val();
                    var url = "{{URL::to('/admin/tdf-transfer')}}";
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {donner_id,tdfamount,tdfaccount},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        complete:function(data){
                            $("#loading").hide();
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });
            // make donation end



            var stsurl = "{{URL::to('/admin/tdf-transaction-status')}}";

            $('select').on('change', function() {
                $("#loading").show();
                var str =  this.value;
                var ret = str.split("|");
                var status = ret[0];
                var tdfid = ret[1];

                // console.log(status,did);
                $.ajax({
                        url: stsurl,
                        method: "POST",
                        data: {status,tdfid},

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
