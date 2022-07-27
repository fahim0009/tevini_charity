@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Pending Voucher</div>
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

                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Charity</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            {{-- <th>Voucher type</th> --}}
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($cvouchers as $voucher)

                                        <tr>
                                                <td><span style="display:none;">{{ $voucher->id }}</span>{{ $voucher->created_at->format('d/m/Y')}} </td>
                                                <td>{{ $voucher->charity->name}} </td>
                                                <td>{{ $voucher->user->name }}</td>
                                                <td>{{ $voucher->cheque_no}}</td>
                                                {{-- <td>{{ $voucher->voucher_type}}</td> --}}
                                                <td>{{ $voucher->note}}</td>
                                                <td>£{{ $voucher->amount}}</td>
                                                <td>
                                                <select name="" id="vsts" class="ms-2 form-control" @if($voucher->status == "3") disabled @endif>
                                                <option value="0" vid="{{$voucher->id}}"  @if($voucher->status == "0") selected @endif>Pending</option>
                                                <option value="1" vid="{{$voucher->id}}"  @if($voucher->status == "1") selected @endif>Complete</option>
                                                <option value="3" vid="{{$voucher->id}}"  @if($voucher->status == "3") selected @endif>Cancel</option>
                                                </select>
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



//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

var url = "{{URL::to('/admin/voucher-status')}}";

$('select').on('change', function() {
    $("#loading").show();
    var status =  this.value;
    var vid = $('option:selected', this).attr('vid');

    $.ajax({
            url: url,
            method: "POST",
            data: {status,vid},

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
