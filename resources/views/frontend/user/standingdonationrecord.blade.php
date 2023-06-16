@extends('frontend.layouts.user')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icomoon-free:profile"></span> <div class="mx-2">Standing Donation records </div>
        </div>
    </section>
  <section class="px-4">
    <div class="row  my-3">

        <div class="col-md-12">

                {{-- Current order start  --}}

                          <section class="px-4"  id="contentContainer">
                            <div class="row my-3">
                                <div class="stsermsg"></div>
                                <div class="col-md-12 mt-2 text-center">
                                    <div class="overflow">
                                        <table class="table table-custom shadow-sm bg-white" id="example">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Beneficiary</th>
                                                    <th>amount</th>
                                                    <th>Annonymous Donation</th>
                                                    <th>Start Date</th>
                                                    <th>Charity Note</th>
                                                    <th>Note</th>
                                                    <th>View</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @forelse ($donation as $data)
                                                    <tr>
                                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                                        <td>{{$data->charity->name}}</td>
                                                        <td>£{{$data->amount}}</td>
                                                        <td>@if ($data->ano_donation == "true")
                                                            Yes
                                                        @else
                                                            No
                                                        @endif</td>
                                                        <td>{{$data->starting}}</td>
                                                        <td>{{$data->charitynote}}</td>
                                                        <td>{{$data->mynote}}</td>
                                                        <td>
                                                            <a href="{{ route('singlestanding', $data->id)}}"><i class="fa fa-eye" style="color: #09a311;font-size:16px;"></i></a>
                                                        </td>
                                                        <td style="text-align: center">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input standingdnstatus" type="checkbox" role="switch"  data-id="{{$data->id}}" @if ($data->status == 1) checked @endif >
                                                            </div>
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


                {{-- current order end  --}}


        </div>
    </div>
  </section>
</div>


@endsection

@section('script')
<script>
    $(document).ready(function () {
   //header for csrf-token is must in laravel
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
   
       $(function() {
         $('.standingdnstatus').change(function() {
           var url = "{{URL::to('/user/active-standingdonation')}}";
             var status = $(this).prop('checked') == true ? 1 : 0;
             var id = $(this).data('id');
   
             $.ajax({
                 url: url,
                 method: "POST",
                 data: {'status': status, 'id': id},
                 success: function(d){
                   if (d.status == 303) {
                           pagetop();
                           $(".stsermsg").html(d.message);
                           window.setTimeout(function(){location.reload()},2000)
                       }else if(d.status == 300){
                           pagetop();
                           $(".stsermsg").html(d.message);
                           window.setTimeout(function(){location.reload()},2000)
                       }
                   },
                   error: function (d) {
                       console.log(d);
                   }
             });
         })
       })
   
   });
   </script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#standingorder").addClass('active');
    });
</script>
@endsection
