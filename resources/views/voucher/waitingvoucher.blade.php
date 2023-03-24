@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Waiting Voucher</div>
        </div>
    </section>
<!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
   </div>
 <!-- Image loader -->
    <div class="ermsg"></div>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">

                        <div class="col-md-1 my-1">
                        </div>

                        <div class="col-md-4 my-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label" for="checkAll">
                                  All Select
                                </label>
                            </div>
                            <button class="btn btn-primary rounded-pill" id="vsrComplete" type="button">Complete</button>
                            <button class="btn btn-danger rounded-pill" id="vsrCancel" type="button">Cancel</button>
                        </div>

                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Date</th>
                                            <th>Charity</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($wvouchers as $voucher)

                                        <tr>
                                                <td><input class="form-check-input getvid" type="checkbox" name="voucherId[]" charity_id="{{ $voucher->charity_id }}" value="{{ $voucher->id }}"></td>
                                                <td><span style="display:none;">{{ $voucher->id }}</span>{{ $voucher->created_at->format('d/m/Y')}} </td>
                                                <td>{{ $voucher->charity->name}} </td>
                                                <td>{{ $voucher->user->name }}</td>
                                                <td>{{ $voucher->cheque_no}}</td>
                                                <td>{{ $voucher->note}}</td>
                                                <td>£{{ $voucher->amount}}</td>
                                                <td>
                                                @if($voucher->status == "0") Pending @endif
                                                @if($voucher->status == "1") Complete @endif
                                                @if($voucher->status == "3") Cancel @endif
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
    </div>
  </section>
</div>
@endsection

@section('script')
<script type="text/javascript">

$(document).ready(function() {

    $("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
    });



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

// select and confirm
var url = "{{URL::to('/admin/waiting-vouchercomplete')}}";

$("#vsrComplete").click(function(){
    $("#loading").show();
    var voucherIds = [];
    $('.getvid:checkbox:checked').each(function(i){
        voucherIds[i] = $(this).val();
        });

    var charityIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        charityIds[i] = $(this).attr('charity_id');
    });        


        $.ajax({
            url: url,
            method: "POST",
            data: {voucherIds,charityIds},

            success: function (d) {
                console.log(d.message);

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


// select and cancel
var urlc = "{{URL::to('/admin/waiting-vouchercancel')}}";

$("#vsrCancel").click(function(){
    $("#loading").show();
    var voucherIds = [];
    $('.getvid:checkbox:checked').each(function(i){
        voucherIds[i] = $(this).val();
        });

    var charityIds = [];    
    $('.getvid:checkbox:checked').each(function(i){
        charityIds[i] = $(this).attr('charity_id');
    });    

        $.ajax({
            url: urlc,
            method: "POST",
            data: {voucherIds,charityIds},

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
