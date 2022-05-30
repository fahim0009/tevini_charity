@extends('layouts.admin')
@section('content')
@php
use Illuminate\Support\Carbon;
use app\Models\Provoucher;
@endphp
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
                                    <select name="charity" id="charity" required class="form-control no-print charity">
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
                                    <button class="text-white btn-theme no-print ml-1 mt-4"  class="btn" name="search" title="Search" type="submit">Search</button>
                                </div>

                           </form>
                        </div>


                    </div>



                    <div class="overflow mt-2">
                        <h4 class="text-center my-3">STATEMENT</h4>

                        @if ($fromDate !="")
                            <h5 class="text-center my-3">From {{ $fromDate }} to {{ $toDate }}</h5>
                        @endif
                        <table class="table table-custom statement shadow-sm bg-white">
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

                            <tbody>
                                @php
                                    $total = $total;
                                    $tbalance = 0;
                                @endphp
                                @foreach ($remittance as $data)

                                    <tr>
                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                        <td>Vouchers </td>
                                        <td>{{$data->cheque_no}}</td>
                                        <td> £{{ number_format($data->amount, 2) }}</td>

                                        @if($data->status == 1)
                                        <td> £{{ number_format($total+$tbalance, 2) }} </td>
                                        <?php $tbalance = $tbalance - $data->amount;?>
                                        @elseif($data->status == 0)
                                        <td> £{{ number_format($total+$tbalance, 2) }} </td>
                                        @endif

                                        <td>
                                        <!--Acc: No: {{$data->donor_acc}}; <br>-->
                                            <!--Voucher No:*****-->
                                           {{$data->note}}
                                        </td>
                                        <td>
                                            @if($data->status == 1)
                                            Complete
                                            @elseif($data->status == 0)
                                            Pending
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach



                            </tbody>
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

    });
</script>
@endsection
