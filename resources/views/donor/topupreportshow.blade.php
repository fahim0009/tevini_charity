@extends('layouts.admin')
@section('content')
<style>
     * {
	 margin: 0;
	 padding: 0;
}
 .wrapper {
	 max-width: 960px;
	 margin: 0 auto;
	 box-shadow: rgba(0, 0, 0, 0.04) -1px 2px 20px 15px;
	 padding: 25px;
}
 .wrapper .heading .title {
	 text-align: right;
	 font-weight: 600;
	 font-size: 2.1rem;
	 text-transform: uppercase;
	 font-family: monospace;
	 letter-spacing: 1px;
	 color: #436784;
}
 .wrapper .heading .subHead {
	 margin: 20px 0;
	 display: flex;
	 justify-content: space-between;
}
 .wrapper .heading .subHead .left {
	 color: #436784;
	 text-transform: capitalize;
	 line-height: 1.3;
	 font-size: 16px;
	 font-family: sans-serif;
}
 .wrapper .heading .subHead .right {
	 text-align: right;
	 color: #436784;
	 text-transform: capitalize;
	 line-height: 1.3;
	 font-size: 16px;
	 font-family: sans-serif;
}
 .wrapper .heading .donated {
	 color: #436784;
	 font-family: sans-serif;
}
 .wrapper .tableData {
	 margin-top: 100px;
	 min-width: 400px;
	 overflow-x: auto;
}
 .wrapper .tableData table {
	 width: 100%;
	 text-align: center;
	 border-collapse: collapse;
}
 .wrapper .tableData table tr th {
	 background-color: #436784;
	 color: azure;
	 padding: 6px;
	 font-family: sans-serif;
	 border-right: 1px solid #fff;
}
 .wrapper .tableData table tr {
	 border-bottom: 1px solid #ebebeb;
}
 .wrapper .tableData table tr td {
	 padding: 6px;
	 color: #080808;
	 text-transform: capitalize;
}
 .wrapper .tableData table tr:nth-child(even) {
	 background: #436784 14;
}
 
</style>

<section class="profile purchase-status no-print">
    <div class="title-section">
        <span class="iconify" data-icon="et:wallet"></span>
        <div class="mx-2">
          Donation Report
        </div>
    </div>
</section>

        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
       <!-- Image loader -->
       <div class="ermsg"></div>

<section>
    <div class="row no-print">
        <div class="col-12">
            <button onclick="window.print()" class="fa fa-print btn btn-default float-end">Print</button>
        </div>
    </div>

    <div class="row my-3">
        <div class="col-md-8 mt-2 mx-auto">

            <div class="text-start mb-4 px-2 no-print">

            <p class="mb-1" id="charityname">{{$user->name}}</p>
            <p class="mb-1" id="charityaddress">{{$user->street}}</p>
            <p class="mb-1" id="charityaddress">{{$user->town}}</p>

            </div>

            <div class="d-flex justify-content-between no-print align-items-center flex-wrap">

            <div class="text-start mb-1 flex-fill">
               <div  class="d-flex justify-content-around align-items-center flex-wrap">
                @csrf

                <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                <input type="hidden" name="transaction_id" id="transaction_id" value="{{$transaction->id}}">
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
        </div>
    </div>


    <div class="wrapper">

        <div class="heading">
            <div class="title">
                Donation receipt
            </div>
            <div class="subHead">
                <div class="left">
                    Tevini Limited <br>
                    Registered charity no. 282079 <br>
                    5A Holmdale Terrace<br>
                    N156PP
    
                </div>
                <div class="right">
                    Date: <b>@php echo date('d-m-Y'); @endphp</b> <br>
                    Receipt <b>#@php echo(rand(100,999));  @endphp</b>
    
                </div>
            </div>
            <p class="donated">
               <b> Donation By: {{$transaction->donation_by ?? " "}}</b>
            </p>
        </div>
    
        <div class="tableData">
            <table>
               <thead>
                <tr>
                    <th>Date</th>
                    <th></th>
                    <th></th>
                    <th>Donation type</th>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                </tr>
               </thead>
               <tbody>
                <tr>
                    <td>@php echo date('d-m-Y'); @endphp</td>
                    <td></td>
                    <td></td>
                    <td>{{$source}}</td>
                    <td></td>
                    <td></td>
                    <td>£{{$balance+$commission}}</td>
                </tr>
                <tr> 
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b>£{{$balance+$commission}}</b></td>
                </tr>
               </tbody>
            </table>
        </div>
    
    </div>
</section>



@endsection


@section('script')
<script type="text/javascript">
$(document).ready(function() {


//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

var url = "{{URL::to('/admin/donor-topup-report-mail')}}";


    $("#sendMail").click(function(){

        $("#loading").show();

        var user_id = $("#user_id").val();
        var transaction_id = $("#transaction_id").val();

            $.ajax({
                url: url,
                method: "POST",
                data: {user_id,transaction_id},

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




