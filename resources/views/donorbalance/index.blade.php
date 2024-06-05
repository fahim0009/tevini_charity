@extends('layouts.admin')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">View All Transactions</div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            <div class="stsermsg"></div>
            
                <div class="row my-2">
                    <div class="col-md-12 mt-2 text-center">
                        <div class="overflow">
                            <table class="table table-custom shadow-sm bg-white" id="exampleall">
                                <thead>
                                    <tr>
                                        <th>UID</th>
                                        <th>Donor Name</th>
                                        <th>Email</th>
                                        <th>Account No</th>
                                        <th>Account Balance </th>
                                        <th>Transaction Table Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $data)
                                    @if (isset($data->user_id))
                                    @php
                                        $user = \App\Models\User::where('id', $data->user_id)->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $data->user_id }}</td>
                                        <td>{{ $user->name}}</td>
                                        <td>{{ $user->email}}</td>
                                        <td>{{ $user->accountno}}</td>
                                        <td>£{{ number_format($user->balance, 2) }}</td>
                                        <td> 
                                            @if (number_format($user->balance, 2) == number_format($data->total_balance, 2) )
                                            <span class="text-decoration-none bg-success text-white py-1 px-3 rounded mb-1 d-block text-center">£{{ number_format($data->total_balance, 2) }}</span>
                                            @else
                                            <span class="text-decoration-none bg-danger text-white py-1 px-3 rounded mb-1 d-block text-center">£{{ number_format($data->total_balance, 2) }}</span>
                                            @endif
                                            
                                        </td>
                                        <td>
                                            @if (number_format($user->balance, 2) != number_format($data->total_balance, 2) )
                                                <button type="button"  data-id="{{$user->id}}" data-balance="{{$data->total_balance}}" class=" text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1 equalBtn">Make Equal </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                        
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>
  </section>
</div>
@endsection

@section('script')
<script>
    $(function() {
      $('.equalBtn').click(function() {
        $.ajaxSetup({
                headers: {'X-CSRF-Token': '{{csrf_token()}}'}
            });
            
        var url = "{{URL::to('/admin/get-donor-balance')}}";
          var id = $(this).data('id');
          var balance = $(this).data('balance');
           console.log(id, balance);
          $.ajax({
              type: "POST",
              dataType: "json",
              url: url,
              data: {'balance': balance, 'id': id},
              success: function(d){
                // console.log(data.success)
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
</script>
@endsection
