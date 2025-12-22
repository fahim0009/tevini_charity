@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">All TDF transaction data </div>
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
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>TDF Account</th>
                                    <th>User Name</th>
                                    <th>User Email</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $data)
                                    <tr>
                                        <td>{{ $data->issue_date}} </td>
                                        <td>{{ $data->tdfaccount}} </td>
                                        <td>{{ \App\Models\User::where('id', $data->user_id)->first()->name }} {{ \App\Models\User::where('id', $data->user_id)->first()->surname }}</td>
                                        <td>{{ \App\Models\User::where('id', $data->user_id)->first()->email }}</td>
                                        
                                        <td>£{{ $data->tdf_amount}}</td>
                                        <td>@if($data->status =="0")
                                            Pending
                                            @elseif($data->status =="1")
                                            Complete
                                            @elseif($data->status =="3")
                                            Cancel
                                            @endif
                                        </td>

                                        {{-- <td>
                                            <button type="button" class="btn btn-primary acc" data-bs-toggle="modal" data-bs-target="#exampleModal{{$data->id}}">
                                                View
                                            </button>
                                        </td> --}}
                                    </tr>

                                      <!-- Modal -->
                                    {{-- <div class="modal fade" id="exampleModal{{$data->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Transaction</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="ermsg"></div>
                                                <form action="{{route('tdfTransaction.update')}}" method="POST">
                                                @csrf

                                                    <div class="row">

                                                        <div class="mb-3">
                                                            <label for="amount" class="form-label">USD Amount</label>
                                                            <input type="number" class="form-control" id="amount" name="amount" required>
                                                            <input type="hidden" class="form-control" value="{{$data->id}}" id="tdfid" name="tdfid">
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="tdfamount" class="form-label">TDF Amount</label>
                                                            <input type="number" class="form-control" value="{{$data->tdf_amount}}" id="tdfamount" name="tdfamount" readonly>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                        </div>
                                        </div>
                                    </div> --}}
                                    <!-- Modal End -->

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
<script type="text/javascript">

$(document).ready(function() {
    
    
    
//header for csrf-token is must in laravel
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//

    var url = "{{URL::to('/admin/tdf-transaction-status')}}";

    $('select').on('change', function() {
        $("#loading").show();
        var str =  this.value;
        var ret = str.split("|");
        var status = ret[0];
        var tdfid = ret[1];

        // console.log(status,did);
        $.ajax({
                url: url,
                method: "POST",
                data: {status,tdfid},

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
