@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">New Donation List</div>
            </div>
            <div class="ermsg"></div>
        </section>
   
        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->

         <section class="px-4"  id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Beneficiary</th>
                                    <th>amount</th>
                                    <th>Annonymous Donation</th>
                                    <th>Charity Note</th>
                                    <!--<th>Topup</th>-->
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($donation as $data)
                                    <tr>
                                        <td>{{$data->created_at->format('d/m/Y')}}</td>
                                        <td>{{$data->user->name}}</td>
                                        <td>{{$data->charity->name}}</td>
                                        <td>£{{$data->amount}}</td>
                                        <td>@if ($data->ano_donation == "true")
                                            Yes
                                        @else
                                            No
                                        @endif
                                        </td>                              
                                        <td>{{$data->charitynote}}</td>
                                        <!--<td> <a href="{{ route('topup',[$data->user->id,$data->amount]) }}" target="blank">-->
                                        <!--<button type="button" class="btn btn-success">Add</button></a> </td>-->
                                        <td>{{$data->mynote}}</td>
                                        <td>Pending</td>
                                        <td> 
                                        <select name="" id="">
                                          <option value="0|{{$data->id}}" @if($data->status == "0")Selected @endif>Pending</option> 
                                          <option value="1|{{$data->id}}" @if($data->status == "1")Selected @endif>Complete</option> 
                                          <option value="3|{{$data->id}}" @if($data->status == "3")Selected @endif>Cancel</option> 
                                        </select> 
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
<script type="text/javascript">

$(document).ready(function() {



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

var url = "{{URL::to('/admin/donation-status')}}";

$('select').on('change', function() {
     $("#loading").show();
    var str =  this.value;
    var ret = str.split("|");
    var status = ret[0];
    var did = ret[1];

    $.ajax({
            url: url,
            method: "POST",
            data: {status,did},

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
