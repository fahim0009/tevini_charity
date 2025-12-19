@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Stripe Topup List</div>
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
                                    <th>Donor Account</th>
                                    <th>amount</th>
                                    <th>Top-Up</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stripe as $data)
                                    <tr>
                                        <td>
                                            <span title="Day/Month/Year" style="cursor: help;">
                                                {{ $data->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>{{\App\Models\User::where('id','=', $data->donor_id)->first()->accountno}}</td>
                                        <td>£{{$data->amount}}</td>
                                        <td><a href="{{ route('topup',[$data->donor_id,'0']) }}">TopUp</a></td>
                                        <td>
                                            <select name="" id="stripeSts" class="form-control" @if($data->status == "1")disabled @endif>
                                                <option value="{{$data->id}}" @if($data->status == "0")Selected @endif>Pending</option>
                                                <option value="{{$data->id}}" @if($data->status == "1")Selected @endif>Complete</option>
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

var url = "{{URL::to('/admin/stripe-topup-status')}}";

$('select').on('change', function() {
     $("#loading").show();
    var id = this.value;
    $.ajax({
            url: url,
            method: "POST",
            data: {id},

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
