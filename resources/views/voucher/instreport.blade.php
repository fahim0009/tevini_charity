@extends('layouts.admin')
@section('content')
@php
use Illuminate\Support\Carbon;
use app\Models\Provoucher;
@endphp
<div class="rightSection" id="section-to-print">

    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="et:wallet"></span>
                <div class="mx-2">
                  Reports
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
                <div class="col-8 d-flex justify-content-between no-print align-items-center flex-wrap">

        <div class="text-start mb-1 flex-fill">
        <div class="d-flex justify-content-around align-items-center flex-wrap">
            <div class="form-group my-2 mx-1 flex-fill">
                    <label for=""><small>Mail To </small> </label>
                    <input class="form-control mr-sm-2 no-print" type="input" id="mail" name="mail" value="{{ $charity->email}}" readonly>
                </div>
                <input type="hidden" name="batch_id" id="batch_id" class="charityid" value="{{ $batch_id }}">

                <div class="form-group my-2 mx-1 flex-fill">
                    <button class="text-white btn-theme no-print ml-1 mt-4" id="sendMail" title="Search" type="submit">Send</button>
                </div>
            </div>
        </div>


                </div>
            <div class="col-4">
                <button onclick="window.print()" class="fa fa-print btn btn-default float-end">Print</button>
            </div>
            </div>

            <div class="row my-3">
                <div class="col-md-12 mt-2 ">

                    <div class="text-start mb-4 px-2">

                        <p class="mb-1" id="charityname"> {{ $charity->name}} </p>
                        <p class="mb-1" id="charityaddress">{{ $charity->address}}</p>

                    </div>



                    <div class="overflow mt-2">
                        <h4 class="text-center my-3">STATEMENT</h4>

                            <h5 class="text-center my-3">Todays</h5>

                            <table class="table table-custom statement shadow-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Voucher number</th>
                                    <th>Donor Name</th>
                                    <th>Amount </th>
                                    <th>Balance </th>
                                    <th>Notes </th>
                                    <th>Status </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = $total;
                                     $tbalance = 0;
                                @endphp
                                @foreach ($remittance as $data)
                                        <tr>
                                        <td><span style="display:none;">{{ $data->id }}</span>{{ $data->created_at->format('d/m/Y')}} </td>
                                        <td>Vouchers </td>
                                        <td>{{$data->cheque_no}}</td>
                                        <td>{{$data->user->name}}</td>
                                        <td> £{{ number_format($data->amount, 2) }}</td>
                                        @if($data->status == 1)
                                        <td> £{{ number_format($total+$tbalance, 2) }} </td>
                                        <?php $tbalance = $tbalance - $data->amount;?>
                                        @else
                                        <td> £{{ number_format('0', 2) }} </td>
                                        @endif
                                        <td>
                                        <!--Acc: No: {{$data->donor_acc}}; <br>-->
                                            <!--Voucher No:*****-->
                                           {{$data->note}}
                                        </td>
                                        <td>
                                            @if($data->status == 1)
                                            Complete
                                            @elseif($data->status == 0)
                                            Pending
                                            @elseif($data->status == 3)
                                            Cancel
                                            @endif
                                        </td>
                                        </tr>
                                @endforeach



                            </tbody>
                        </table>
                        <h6 class="text-center my-4">
                            THANK YOU. Your account is now in credit and the statement is for your
                            information only.
                            </h6>
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

var url = "{{URL::to('/admin/pvr-mail')}}";


$("#sendMail").click(function(){

    $("#loading").show();

    var mail = $("#mail").val();
    var batch_id = $("#batch_id").val();

        $.ajax({
            url: url,
            method: "POST",
            data: {mail,batch_id},

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
