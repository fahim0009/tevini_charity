@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status no-print">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Topup</div>
            <a href="{{ route('donationlist') }}"><button type="button" class="btn btn-success">back</button></a>
        </div>

    </section>

    <section class="px-4 no-print">
            <div class="row my-3">
                <div class="ermsg"></div>
            </div>
        </section>

        <section class="px-4 no-print">
            <div class="row my-3">
                <div class="container">
                    <div class="col-md-12 my-3">
                        <p>**Please note that if you are topping up your account using a credit/debit card there will be an additional fee of 2% on top of the standard 5% commission fee alternatively you can top up by transfer to the following: Tevini Ltd S/C 40-52-40 A/C 00024463.</p>
                    </div>
                    
                </div>
            </div>
        </section>

         <!-- Image loader -->
         <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->
  <section class="no-print">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 my-3">
            <div class="row">
                <div class="col-md-6 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Details </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $topup->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $topup->email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>{{ $topup->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>{{ $topup->address }}</td>
                                </tr>
                                <tr>
                                    <td>Account</td>
                                    <td>{{ $topup->accountno }}</td>
                                </tr>
                                <tr>
                                    <td>Balance</td>
                                    <td>{{ $topup->balance }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-inline" method="POST" action="{{ route('topup.store') }}"  enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group my-2">
                                    <label for=""><small>Amount </small> </label>
                                    <input class="form-control mr-sm-2" type="text" value="@if($amount != 0){{$amount}}@endif" name="gbalance" id="gbalance" required placeholder="Amount">
                                </div>
                                <input type="hidden" name="topupid" id="topupid" value="{{ $topup->id }}">
                                <div class="form-group my-2">
                                    <label for=""><small>Commission Percentage</small> </label>
                                    <input class="form-control mr-sm-2" type="text" name="cc" id="cc" placeholder="">
                                </div>
                                <div class="form-group my-2">
                                    <label for=""><small>Commission </small> </label>
                                    <input class="form-control mr-sm-2" type="text" name="commission" id="commission" placeholder="" readonly>
                                </div>
                                  <div class="form-group my-2">
                                    <label for=""><small>Total Amount </small> </label>
                                    <input class="form-control mr-sm-2" type="text" readonly name="balance" id="balance" required placeholder="">
                                </div>
                                <div class="form-group my-2">
                                    <label for=""><small>Source</small> </label>
                                    <select name="source" id="source" class="form-control">
                                        <option value="Bank">Bank</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Card">Card</option>
                                    </select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for=""><small>Donation By</small> </label>
                                        <input class="form-control mr-sm-2" type="text" name="donationBy" id="donationBy" placeholder="">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for=""><small>Note</small> </label>
                                        <input class="form-control mr-sm-2" type="text" name="note" id="note" placeholder="">
                                    </div>
                                    <div class="form-group my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value id="receipt">
                                            <label class="form-check-label" for="receipt">
                                              I want receipt.
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value id="gift">
                                            <label class="form-check-label" for="gift">
                                              GIFT
                                            </label>
                                        </div>
                                    </div>  
                                    <div class="form-group my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value id="cleargift">
                                            <label class="form-check-label" for="cleargift">
                                            CLEAR EXPECTED GIFT AID
                                            </label>
                                        </div>
                                    </div>    
                                <div class="form-group my-2">
                                    <button type="button" id="topBal" class="my-2 btn btn-sm btn-info text-white">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>

  {{-- <div class="row">
  <div class="col-4 no-print">
    <button onclick="window.print()" class="fa fa-print btn btn-default float-end">Print</button>
</div>
</div> --}}

<div class="row" id='receiptp' style='display:none ;'>
<button onclick="window.print()" class="fa fa-print btn btn-default float-end">Print</button>
<div class="col-md-3 no-print"></div>
  <div class="col-md-6 mt-2 text-center">
    <div class="overflow">
        <table class="table table-custom shadow-sm bg-white">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Details </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Name</td>
                    <td>{{ $topup->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $topup->email }}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>{{ $topup->phone }}</td>
                </tr>
                <tr>
                    <td>Topup amount</td>
                    <td id="tamount"></td>
                </tr>
                <tr>
                    <td>Commission</td>
                    <td id="cmsn"></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td id="ttl"></td>
                </tr>
                <tr>
                    <td>Source</td>
                    <td id="src"></td>
                </tr>
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
//calculation start
$("#gbalance, #cc").keyup(function(){
  var total=0;
  var amount = Number($("#gbalance").val());
  var comn_rate = Number($("#cc").val());
  var commission_amount = ((amount*comn_rate)/100);
  var after_cmn_amount = amount - commission_amount;
  
  $('#commission').val(commission_amount);
  $('#balance').val(after_cmn_amount.toFixed(2));

});
//calculation end



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

var url = "{{URL::to('/admin/topupstore')}}";


$("#topBal").click(function(){

    $("#loading").show();

    var topupid = $("#topupid").val();
    var balance = $("#balance").val();
    var commission = $("#commission").val();
    var source = $("#source").val();
    var gbalance = $("#gbalance").val();
    var note = $("#note").val();
    var donationBy = $("#donationBy").val();
    var receipt = $("#receipt").prop('checked');
    var gift = $("#gift").prop('checked');
    var cleargift = $("#cleargift").prop('checked');

    console.log(gift);

        $.ajax({
            url: url,
            method: "POST",
            data: {topupid,balance,commission,source,gbalance,note,receipt,donationBy,gift,cleargift},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                }
            },
            complete:function(d){
                        $("#loading").hide();
                        if(receipt == true){
                        $("#receiptp").show();
                        $("#tamount").html("<span>"+balance+"</span>");
                        $("#cmsn").html("<span>"+commission+"</span>");
                        $("#ttl").html("<span>"+gbalance+"</span>");
                        $("#src").html("<span>"+source+"</span>");
                        }
                    },
            error: function (d) {
                console.log(d);
            }
        });

});



});

</script>
@endsection