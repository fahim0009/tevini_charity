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
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="transactionOut-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Transaction In</button>
                  <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-transcationIn" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Transcation Out</button>

                  <button class="nav-link" id="nav-report-tab" data-bs-toggle="tab" data-bs-target="#nav-reportTab" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Report</button>

                  
                  <button class="nav-link" id="nav-ledger-tab" data-bs-toggle="tab" data-bs-target="#nav-ledger" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Ledger</button>

                  <button class="nav-link" id="nav-pendingVoucher-tab" data-bs-toggle="tab" data-bs-target="#nav-pendingVoucher" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Pending Voucher</button>

                  
                  <button class="nav-link" id="nav-email-tab" data-bs-toggle="tab" data-bs-target="#nav-email" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Email</button>

                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">


                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="container">
                           <div class="row">
                            <div class="col-md-9">
                                <form class="form-inline" action="{{route('charity.tranview_search', $id)}}" method ="POST">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="fromDate"><small>Date From </small> </label>
                                                <input class="form-control mr-sm-2" id="fromDate" name="fromDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="toDate"><small>Date To </small> </label>
                                                <input class="form-control mr-sm-2" id="toDate" name="toDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-5 d-flex align-items-center">
                                            <div class="form-group d-flex mt-4">
                                            <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>

                           </div>
                        </div>
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Id</th>
                                            <th>Transaction Type</th>
                                            <th>Voucher Number</th>
                                            <th>Note </th>
                                            <th>Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($intransactions as $transaction)
                                        <tr>
                                                <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                                <td>{{ $transaction->t_id }}</td>
                                                <td>{{ $transaction->title}}</td>
                                                <td>{{ $transaction->cheque_no}}</td>
                                                <td>{{ $transaction->note}}</td>
                                                <td>{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-transcationIn" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="row my-2">
                        <div class="col-md-12 my-3">
                            <div class="col-md-12 my-3">
                                <div class="container">
                               <div class="row">
                                <div class="col-md-9">

                                <form class="form-inline" action="{{route('charity.tranview_search', $id)}}" method ="POST">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="fromDate"><small>Date From </small> </label>
                                                <input class="form-control mr-sm-2" id="fromDate" name="fromDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="toDate"><small>Date To </small> </label>
                                                <input class="form-control mr-sm-2" id="toDate" name="toDate" type="date" placeholder="Search" aria-label="Search">
                                            </div>
                                        </div>
                                        <div class="col-md-5 d-flex align-items-center">
                                            <div class="form-group d-flex mt-4">
                                            <button class="text-white btn-theme ml-1" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                </div>
                                </div>

                               </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="exampleIn">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Id</th>
                                            <th>Charity Name</th>
                                            <th>Source</th>
                                            <th>Note </th>
                                            <th>Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($outtransactions as $transaction)
                                        <tr>
                                            <td><span style="display:none;">{{ $transaction->id }}</span>{{ Carbon::parse($transaction->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $transaction->t_id }}</td>
                                            <td>@if($transaction->charity_id){{ $transaction->charity->name}}@endif</td>
                                            <td>{{ $transaction->name}}</td>
                                            <td>{{ $transaction->note}}</td>
                                            <td>{{ $transaction->amount}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-reportTab" role="tabpanel" aria-labelledby="nav-report-tab">
                    <div class="row my-2">
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Date</th>
                                            <th>Report </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($reports as $key => $report)

                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $report->created_at }}</td>
                                            <td><a class="text-white btn-theme ml-1" href="{{route('instreport', $report->id)}}" style="text-decoration: none">Report</a></td>
                                        </tr>
                                            
                                        @endforeach
                                        
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="nav-ledger" role="tabpanel" aria-labelledby="nav-ledger-tab">
                    <div class="row my-2">
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="">
                                    <thead>
                                        <tr>
                                            <th>Total In</th>
                                            <th>Total Out</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <tr>
                                            <td>{{ $totalIN}}</td>
                                            <td>{{ $totalOUT}}</td>
                                            <td>{{ $totalIN-$totalOUT}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="nav-pendingVoucher" role="tabpanel" aria-labelledby="nav-pendingVoucher-tab">
                    <div class="row my-2">
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @foreach ($pvouchers as $voucher)

                                        <tr>
                                            <td>{{ $voucher->created_at->format('d/m/Y')}} </td>
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

                
                <div class="tab-pane fade" id="nav-email" role="tabpanel" aria-labelledby="nav-email-tab">
                    <div class="row my-2">

                        <section class="profile purchase-status">
                            <div class="title-section">
                                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                                <div class="mx-2">More email account</div>
                            </div>
                        </section>

                        <section class="card m-3">
                            <div class="row  my-3 mx-0 ">
                                <div class="col-md-12 ">

                                    <div class="errmsg"></div>

                                    <div class="col-md-12" id="emailCreateForm">
                                        <form class="form-inline" method="POST">
                                            @csrf         

                                            <div class="row justify-content-center">

                                                <div class="col-md-4">
                                                    <div class="form-group my-2">
                                                        <label for="newemail"><small>Email</small> </label>
                                                        <input class="form-control mr-sm-2" id="newemail" name="newemail" type="email"  value="">
                                                        <input id="charity_id" name="charity_id" type="hidden"  value="{{$id}}">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 d-flex align-items-center">
                                                    <div class="form-group d-flex mt-4">
                                                        <input type="hidden" id="update_id" value="">
                                                        <button class="text-white btn-theme ml-1" id="addBtn" type="button">Add</button>
                                                        <button class="text-white btn-theme ml-1 d-none" id="updateBtn" type="button">Update</button>

                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                    
                                </div>
                            </div>
                        </section>

                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                
                                <table class="table table-custom shadow-sm bg-white" id="example">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Email</th>
                                            <th>Action </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach (\App\Models\UserDetail::where('charity_id', $id)->get() as $data)
                                            <tr>
                                                <td>{{ $data->date }}</td>
                                                <td>{{ $data->email }}</td>
                                                <td class="text-right">
                                                    <button data-udid="{{$data->id}}" data-email="{{$data->email}}" class="btn btn-sm btn-primary mr-1 editBtn" >Edit</button>

                                                    <form action="{{ route('useremail.destroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this email?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-warning mr-1">Delete</button>
                                                    </form>
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
    </div>
  </section>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        var title = 'Report: ';
        var data = 'Data: ';


        // datatable common
        $('#reportDT').DataTable({
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true,
            columnDefs: [ { type: 'date', 'targets': [0] } ],
            order: [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {extend: 'copy'},
                {extend: 'excel', title: title},
                {extend: 'print',
                exportOptions: {
                stripHtml: false
            },
                title: "<p style='text-align:center;'>"+data+"<br>"+title+"</p>",
                header:true,
                    customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');
                }
                }
            ]
        });



    });
</script>

<script>
$(document).ready(function(){

    // -------------------
    // Add Email (AJAX)
    // -------------------
    $("#addBtn").click(function(e){
        e.preventDefault();

        var email = $("#newemail").val();
        var charity_id = $("#charity_id").val();

        $.ajax({
            url: "{{ route('useremail.store') }}",
            type: "POST",
            data: {
                email: email,
                charity_id: charity_id,
                _token: "{{ csrf_token() }}"
            },
            success: function(res){

                if(res.status == 200){                    
                    $(".errmsg").html(`<div class="alert alert-success">${res.message}</div>`);

                    $("#example tbody").prepend(`
                        <tr id="row_${res.data.id}">
                            <td>${res.data.date}</td>
                            <td class="email_${res.data.id}">${res.data.email}</td>
                            <td class="text-right">
                                <button data-id="${res.data.id}" data-email="${res.data.email}" class="btn btn-sm btn-primary mr-1 editBtn">Edit</button>
                                <form action="/useremail/${res.data.id}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-warning mr-1">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `);

                    $("#newemail").val("");
                }
            }
        });
    });

    // -------------------
    // Get Edit Data
    // -------------------
    $("body").on("click", ".editBtn", function(){

        var id = $(this).data("udid");
        var email = $(this).data("email");

        $("#update_id").val(id);
        $("#newemail").val(email);

        // Switch form buttons
        $("#addBtn").addClass("d-none");
        $("#updateBtn").removeClass("d-none");

    });

    // -------------------
    // Update Email (AJAX)
    // -------------------
    $("#updateBtn").click(function(e){
        e.preventDefault();

        var id = $("#update_id").val();
        var email = $("#newemail").val();

        $.ajax({
            url: "{{ route('charityemail.update') }}",
            type: "POST",
            data: {
                id: id,
                email: email,
                _token: "{{ csrf_token() }}"
            },
            success: function(res){

                if(res.status == 200){
                    $(".errmsg").html(`<div class="alert alert-success">${res.message}</div>`);

                    // Update email in table row
                    $(".email_" + id).text(email);

                    // reset form
                    $("#newemail").val("");
                    $("#update_id").val("");

                    $("#updateBtn").addClass("d-none");
                    $("#addBtn").removeClass("d-none");
                }
            }
        });

    });

});
</script>




@endsection
