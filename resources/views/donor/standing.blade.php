@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Standing Order</div>
            </div>
        </section>





        @if(session()->has('message'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('message') }}</div>
            </div>
        </section>
        @endif
        @if(session()->has('error'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
            </div>
        </section>
        @endif





        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
                <div class="stsermsg"></div>
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <div class="table-responsive">
                        <table class="table table-custom shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Beneficiary</th>
                                    <th>amount</th>
                                    <th>Annonymous Donation</th>
                                    <th>Starting</th>
                                    <th>Interval</th>
                                    <th>Number of Payments</th>
                                    <th>Charity Note</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($donation as $data)
                                    <tr>
                                        <td>{{$data->created_at}}</td>
                                        <td>{{$data->user->name}}</td>
                                        <td>{{$data->charity->name}}</td>
                                        <td>{{$data->amount}}</td>
                                        <td>@if ($data->ano_donation == "true")
                                            Yes
                                        @else
                                            No
                                        @endif</td>
                                        <td>{{$data->starting}}</td>
                                        <td>{{$data->interval}}</td>
                                        @if ($data->payments == 1)
                                        <td>{{$data->number_payments}}</td>
                                        @else
                                        <td>Continuous payments</td>
                                        @endif
                                        <td>{{$data->charitynote}}</td>
                                        <!--<td> <a href="{{ route('topup',[$data->user->id,$data->amount]) }}" target="blank">-->
                                        <!--    <button type="button" class="btn btn-success">Add</button></a> </td>-->
                                        <td>{{$data->mynote}}</td>
                                        <td style="text-align: center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input standingdnstatus" type="checkbox" role="switch"  data-id="{{$data->id}}" @if ($data->status == 1) checked @endif >
                                            </div>
                                        </td>
                                        {{-- <td>Pending</td> --}}

                                    </tr>
                                @empty
                                @endforelse




                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')
<script>
 $(document).ready(function () {
//header for csrf-token is must in laravel
 $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(function() {
      $('.standingdnstatus').change(function() {
        var url = "{{URL::to('/admin/active-standingdonation')}}";
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

@endsection
