@extends('frontend.layouts.user')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icomoon-free:profile"></span> <div class="mx-2">Wating voucher records </div>
        </div>
    </section>
    <!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
   </div>
 <!-- Image loader -->
 <div class="ermsg"></div>
  <section class="px-4">
    <div class="row  my-3">

        <div class="col-md-12">

                {{-- Current order start  --}}

                          <section class="px-4"  id="contentContainer">
                            <div class="row my-3">

                                <div class="col-md-12 mt-2 text-center">
                                    <div class="overflow">
                                        <table class="table table-custom shadow-sm bg-white" id="example">
                                            <thead>
                                                <tr>                
                                                    <th>Date</th>
                                                    <th>Charity</th>
                                                    <th>Donor</th>
                                                    <th>Cheque No</th>
                                                    <th>Note</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                                @foreach ($wvouchers as $voucher)
        
                                                <tr>
                                                        {{-- <td><input class="form-check-input getvid" type="checkbox" name="voucherId[]" donor_id="{{ $voucher->user_id }}" charity_id="{{ $voucher->charity_id }}" value="{{ $voucher->id }}"></td> --}}
                                                        <td><span style="display:none;">{{ $voucher->id }}</span>{{ $voucher->created_at->format('d/m/Y')}} </td>
                                                        <td>{{ $voucher->charity->name}} </td>
                                                        <td>{{ $voucher->user->name }}</td>
                                                        <td>{{ $voucher->cheque_no}}</td>
                                                        <td>{{ $voucher->note}}</td>
                                                        <td>£{{ $voucher->amount}}</td>
                                                        <td>
                                                        @if($voucher->waiting == "Yes") Waiting @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-success rounded-pill vsrComplete" id="{{ $voucher->id }}" onclick="return confirm('Are you sure you want to confirm this?');" charity_id="{{ $voucher->charity_id }}" voucher_id="{{ $voucher->id }}" type="button">Confirm</button>
                                                            <button class="btn btn-danger rounded-pill vsrCancel" id="cancel{{ $voucher->id }}" charity_id="{{ $voucher->charity_id }}" voucher_id="{{  $voucher->id }}" type="button">Cancel</button>
                                                        </td>
        
                                                </tr>
                                                @endforeach
        
        
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>


                {{-- current order end  --}}


        </div>
    </div>
  </section>
</div>


@endsection

@section('script')
<script type="text/javascript">

    $(document).ready(function() {
    
      
    //header for csrf-token is must in laravel
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //
    
    // select and confirm
    var url = "{{URL::to('/user/waiting-completeBydonor')}}";
    
    $(".vsrComplete").click(function(){

        // confirm("Are you sure to confirm this?");
        $("#loading").show();
   
            var charity_id = $(this).attr('charity_id');
            var voucher_id = $(this).attr('voucher_id');
    
            $.ajax({
                url: url,
                method: "POST",
                data: {voucher_id,charity_id},
    
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
                            $("#"+voucher_id).attr('disabled',true);
                            window.setTimeout(function(){location.reload()},2000);
                        },
                error: function (d) {
                    console.log(d);
                }
            });
    
    });
    
    
    // select and cancel
    var urlc = "{{URL::to('/user/waiting-cancelBydonor')}}";
    
    $(".vsrCancel").click(function(){
        $("#loading").show();

        var charity_id = $(this).attr('charity_id');
        var voucher_id = $(this).attr('voucher_id');
    
            $.ajax({
                url: urlc,
                method: "POST",
                data: {voucher_id,charity_id},
    
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
                            $("#cancel"+voucher_id).attr('disabled',true);
                            window.setTimeout(function(){location.reload()},2000);
                        },
                error: function (d) {
                    console.log(d);
                }
            });
    
    });
    
    
    });
    </script>
@endsection
