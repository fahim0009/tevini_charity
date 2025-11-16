@extends('layouts.admin')
@section('content')
@php
use Illuminate\Support\Carbon;
use app\Models\Provoucher;
@endphp
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection" id="section-to-print">

    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="et:wallet"></span>
                <div class="mx-2">
                  Remitance Reports
                </div>
            </div>
        </section>

        <section class="px-4">

            <div class="row no-print">
            <div class="col-12">
                <button onclick="window.print()" class="fa fa-print btn btn-default float-end">Print</button>
            </div>
            </div>

            <div class="row my-3">
                <div class="col-md-12 mt-2 ">
                    <div class="text-start mb-4 px-2">

                        <p class="mb-1" id="charityname"> @if ($charity !=""){{ $charity->name}} @endif</p>
                        <p class="mb-1" id="charityaddress">@if ($charity !=""){{ $charity->address}} @endif</p>

                    </div>

                    <div class="d-flex justify-content-between no-print align-items-center flex-wrap">

                        <div class="text-start mb-1 flex-fill">
                            <form  action="{{route('remittance.search')}}" method ="POST" class="d-flex justify-content-around align-items-center flex-wrap">
                                @csrf
                                <div class="form-group my-2 mx-1 flex-fill">
                                    <label for=""><small>Select Charity </small> </label>
                                    <select name="charity" id="charity" required class="form-control no-print charity select2">
                                        <option value>Select</option>
                                        @foreach (\App\Models\Charity::all() as $user)
                                        <option value="{{$user->id}}|{{$user->name}}|{{$user->address}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group my-2 mx-1 flex-fill">
                                    <label for=""><small>Date From </small> </label>
                                    <input class="form-control no-print mr-sm-2" type="date" id="fromdate" name="fromdate" placeholder="Search" aria-label="Search">
                                </div>

                                <div class="form-group my-2 mx-1 flex-fill">
                                    <label for=""><small>Date To </small> </label>
                                    <input class="form-control mr-sm-2 no-print" type="date" id="todate" name="todate" placeholder="Search" aria-label="Search">
                                </div>
                                <input type="hidden" name="charityid" id="charityid" class="charityid">

                                <div class="form-group my-2 mx-1 flex-fill">
                                    <button type="button" id="searchBtn" class="text-white btn-theme no-print ml-1 mt-4">
                                        Search
                                    </button>
                                </div>

                                


                           </form>
                        </div>


                    </div>



                    <div class="overflow mt-2">
                        <h4 class="text-center my-3">STATEMENT</h4>

                        @if ($fromDate !="")
                            <h5 class="text-center my-3">From {{ $fromDate }} to {{ $toDate }}</h5>
                        @endif
                        <table class="table table-custom statement shadow-sm bg-white" id="remittanceTable">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Voucher number</th>
                                    <th>Amount </th>
                                    <th>Balance </th>
                                    <th>Notes </th>
                                    <th>Status </th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>
                        <h6 class="text-center my-4">
                            THANK YOU. Your account is now in credit and the statement is for your
                            information only.
                            </h6>
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

        // header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //


            $('select').on('change', function() {
                var str =  this.value;
                var ret = str.split("|");
                var id = ret[0];
                var name = ret[1];
                var address = ret[2];
                $('#charityname').html(name);
                $('#charityaddress').html(address);
                $('#charityid').val(id);
            });

            $('#charity').on('change', function(){
                var parts = this.value.split("|");
                $('#charityid').val(parts[0]);
            });


    });
</script>

<script>
$(document).ready(function () {

    $('#remittanceTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: true,
        pageLength: 25,
        ajax: {
            url: "{{ route('remittance') }}",
            type: "GET",
            data: function (d) {
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.charityid = $('#charityid').val();
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'description', name: 'description' },
            { data: 'voucher', name: 'voucher' },
            { data: 'amount', name: 'amount' },
            { data: 'balance', name: 'balance', orderable: false },
            { data: 'notes', name: 'notes' },
            { data: 'status_text', name: 'status_text' },
        ]
    });

    $('#searchBtn').on('click', function () {
        $('#remittanceTable').DataTable().ajax.reload();
    });


});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
    $('#charity').select2({
      width: '100%',
      placeholder: "Select an Option",
      allowClear: true
    });

  </script>
@endsection
