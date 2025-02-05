@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Complete Voucher</div>
        </div>
    </section>
    
    @if (isset($donor_id))
        @include('inc.user_menue')
    @endif
  <section class="">
    <input type="hidden" id="donorid" value="{{$donorid}}">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example3">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Completed Date</th>
                                            <th>Charity</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {{-- @foreach ($cvouchers as $voucher)
                                        <tr>
                                                <td>{{ $voucher->created_at->format('m/d/Y')}} </td>
                                                <td>{{ $voucher->charity->name}} </td>
                                                <td>{{ $voucher->user->name }}</td>
                                                <td>{{ $voucher->cheque_no}}</td>
                                                <td>{{ $voucher->voucher_type}}</td>
                                                <td>{{ $voucher->note}}</td>
                                                <td>£{{ $voucher->amount}}</td>
                                        </tr>
                                        @endforeach --}}


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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            let id = $('#donorid').val();
            console.log(id)
            $('#example3').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('completevoucher') }}",
                    type: "GET",
                    data: function (d) {
                        d.id = id; // Add the ID as a parameter to the request
                    },
                    error: function(xhr, error, code) {
                        console.log(xhr.responseText); // Debugging response
                    }
                },
                pageLength: 100,
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'completed_date', name: 'completed_date' },
                    { data: 'charity', name: 'charity' },
                    { data: 'user', name: 'user' },
                    { data: 'cheque_no', name: 'cheque_no' },
                    { data: 'note', name: 'note' },
                    { data: 'amount', name: 'amount' }
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
