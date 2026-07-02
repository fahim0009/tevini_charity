@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Cancel Voucher</div>
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
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($wvouchers as $voucher)

                                        <tr>
                                                <td><span style="display:none;">{{ $voucher->id }}</span>{{ $voucher->created_at->format('d/m/Y')}} </td>
                                                <td>{{ $voucher->charity->name}} </td>
                                                <td>{{ $voucher->user->name }} {{ $voucher->user->surname }}
                                                <br>
                                                {{ $voucher->user->email }}
                                                </td>
                                                <td>{{ $voucher->cheque_no}}</td>
                                                <td>{{ $voucher->note}}</td>
                                                <td>£{{ $voucher->amount}}</td>
                                                <td>
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



});
</script>
@endsection
