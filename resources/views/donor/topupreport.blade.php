@extends('layouts.admin')
@section('content')
@php
use Illuminate\Support\Carbon;
use app\Models\Provoucher;
@endphp
@include('inc.user_menue')
<div class="rightSection" id="section-to-print">

    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="et:wallet"></span>
                <div class="mx-2">
                  Donor Report
                </div>
            </div>
        </section>
        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->
     <div class="ermsg"></div>

        <section class="px-4">
            <div class="row no-print">
            <div class="col-12">
                <button onclick="window.print()" class="fa fa-print btn btn-default float-end">Print</button>
            </div>
            </div>

            <div class="row my-3">
                <div class="col-md-12 mt-2 ">

                    <div class="text-start mb-4 px-2">

                        <p class="mb-1" id="charityname">{{$user->name}}</p>
                        <p class="mb-1" id="charityaddress">{{$user->street}}</p>
                        <p class="mb-1" id="charityaddress">{{$user->town}}</p>

                    </div>

                    <div class="d-flex justify-content-between no-print align-items-center flex-wrap">

                        <div class="text-start mb-1 flex-fill">
                            <form  action="{{route('donor.reportsearch', $user->id)}}" method ="POST" class="d-flex justify-content-around align-items-center flex-wrap">
                                @csrf
                                <div class="form-group my-2 mx-1 flex-fill">
                                    <label for=""><small>Date From </small> </label>
                                    <input class="form-control no-print mr-sm-2" type="date" id="fromdate" name="fromdate" placeholder="Search" aria-label="Search">
                                </div>

                                <div class="form-group my-2 mx-1 flex-fill">
                                    <label for=""><small>Date To </small> </label>
                                    <input class="form-control mr-sm-2 no-print" type="date" id="todate" name="todate" placeholder="Search" aria-label="Search">
                                </div>
                                <input type="hidden" name="charityid" id="charityid" class="charityid">

                                <div class="form-group my-2 mx-1 flex-fill">
                                    <button class="text-white btn-theme no-print ml-1 mt-4"  class="btn" name="search" title="Search" type="submit">Search</button>
                                </div>

                           </form>

                           <div  class="d-flex justify-content-around align-items-center flex-wrap">
                            @csrf

                            <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                            <input  type="hidden" id="efromdate" name="fromdate" value="{{$fromDate}}">
                            <input  type="hidden" id="etodate" name="todate" value="{{$toDate}}">
                            <div class="form-group my-2 mx-1 flex-fill">
                                <label for=""><small>Donor Mail</small> </label>
                                <input class="form-control mr-sm-2 no-print" type="text" value="{{$user->email}}" readonly>
                            </div>
                            <div class="form-group my-2 mx-1 flex-fill">
                        <button class="text-white btn-theme no-print ml-1 mt-4" id="sendMail"  class="btn" >Send Mail</button>
                            </div>
                       </div>
                        </div>
                    </div>
                    <div class="overflow mt-2">
                        <h4 class="text-center my-3">STATEMENT</h4>

                        @if ($fromDate !="")
                            <h5 class="text-center my-3">From  {{ Carbon::parse($fromDate)->format('d/m/Y') }} to  {{ Carbon::parse($toDate)->format('d/m/Y') }}</h5>
                        @endif
                        <div class="overflow">
                            <table class="table table-custom shadow-sm bg-white">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction Id</th>
                                        <th>transaction type</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Balance</th>
                                        <th>Report</th>
                                    </tr>
                                </thead>

                            <?php
                                $tbalance = 0;
                            ?>

                              @foreach ($tamount as $data)
                                    @if($data->commission != 0)
                                        @php
                                        $tbalance = $tbalance - $data->commission;
                                        @endphp
                                    @endif

                                    @php
                                    if($data->t_type == "In"){
                                        if($data->commission != 0){

                                        $tbalance = $tbalance + $data->amount + $data->commission;
                                        }else {

                                        $tbalance = $tbalance + $data->amount;
                                        }

                                    }
                                    @endphp
                                            @php
                                            if($data->t_type == "Out"){
                                            $tbalance = $tbalance - $data->amount;
                                            }
                                            @endphp
                                @endforeach
                            <tbody>
                                @foreach ($report as $data)
                                    @if($data->commission != 0)
                                    <tr>
                                        <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                        <td>{{$data->t_id}} </td>
                                        <td>Commission</td>
                                        <td></td>
                                        <td>-£{{$data->commission}}</td>
                                        <td>£{{ number_format($tbalance, 2) }}</td>
                                        <td></td> {{--  topup report button  --}}
                                        @php
                                        $tbalance = $tbalance + $data->commission;
                                        @endphp
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                        <td>{{$data->t_id}} </td>
                                        <td>{{$data->title}} </td>

                                            @if($data->t_type == "In")
                                                @if($data->commission != 0)
                                                    <td>£ {{ number_format($data->amount + $data->commission, 2) }} </td>
                                                    <td></td>
                                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                                @else
                                                    <td>£{{number_format($data->amount, 2)}} </td>
                                                    <td></td>
                                                    <td> £{{ number_format($tbalance, 2) }} </td>
                                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                                @endif
                                                <td> <a href="{{route('topup.reportShow',$data->id)}}"  class="text-white btn-theme no-print ml-1" style="text-decoration: none">Report</a></td> {{--  topup report button  --}}
                                            @elseif($data->t_type == "Out")
                                                <td></td>
                                                <td>-£{{number_format($data->amount, 2) }}</td>
                                                 <td> £{{ number_format($tbalance, 2) }} </td>
                                                 <td></td> {{--  topup report button  --}}
                                                 @if($data->pending != "0")
                                                 @php  $tbalance = $tbalance + $data->amount;  @endphp
                                                 @endif
                                            @endif

                                    </tr>
                                @endforeach
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Previous Balance</td>
                                    <td>£{{ number_format($tbalance, 2) }}</td>
                                    <td></td> {{--  topup report button  --}}
                                </tr>
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
<script type="text/javascript">
$(document).ready(function() {


//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

var url = "{{URL::to('/admin/donor-report-mail')}}";


$("#sendMail").click(function(){

    $("#loading").show();

    var fromdate = $("#efromdate").val();
    var todate = $("#etodate").val();
    var user_id = $("#user_id").val();

        $.ajax({
            url: url,
            method: "POST",
            data: {fromdate,todate,user_id},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
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
