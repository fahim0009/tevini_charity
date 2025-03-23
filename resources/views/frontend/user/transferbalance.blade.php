@extends('frontend.layouts.user')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
             <div class="mx-2">
               Balance Transfer
            </div>
        </div>
    </section>
  <section class="">
<!-- Image loader -->
<div id='loading' style='display:none ;'>
    <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." style="height: 225px;" />
</div>
<!-- Image loader -->
    <div class="row  my-3 mx-0 ">
        <div class="col-lg-5" >
            <div class="tdfermsg"></div>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('transfer.balance') }}" method="POST">
                @csrf

                <label for="">Account Number</label>
                <input type="text" id="accountno" name="accountno" class="form-control">
                <label for="">Amount to Transfer</label>
                <input type="text" id="amount" min="0" name="amount" class="form-control">
                
                <div class=" my-3">
                    <button type="submit" class="btn-theme bg-secondary" id="submitBtn">
                        Transfer
                        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-transactionOut-tab" data-toggle="tab" href="#nav-transactionOut" role="tab" aria-controls="nav-transactionOut" aria-selected="true">Send</a>
                        <a class="nav-item nav-link" id="nav-transactionIn-tab" data-toggle="tab" href="#nav-transactionIn" role="tab" aria-controls="nav-transactionIn" aria-selected="false">Received</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut-tab">
                        <div class="row my-2">
                            <div class="col-md-12 mt-2 text-center">
                                <div class="overflow">
                                    <table class="table table-custom shadow-sm bg-white" id="example">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Account Number</th>
                                                <th>Transferred To</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data as $data)
                                            @php
                                                $transferTo = \App\Models\User::where('id', $data->transfer_to)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $data->date ?? ""}} </td>
                                                <td>{{ $data->accountno}}</td>
                                                <td>{{ $transferTo->email ?? ""}}</td>
                                                <td>£{{ $data->amount}}</td>
                                                <td>@if($data->status =="0")
                                                    Pending
                                                    @elseif($data->status =="1")
                                                    Complete
                                                    @elseif($data->status =="3")
                                                    Cancel
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center"> <p>No order found</p> </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-transactionIn" role="tabpanel" aria-labelledby="nav-transactionIn-tab">
                        <div class="row my-2">
                            <div class="col-md-12 mt-2 text-center">
                                <div class="overflow">
                                    <table class="table table-custom shadow-sm bg-white" id="example">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Account Number</th>
                                                <th>Received From</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($receive as $data)

                                            <tr>
                                                <td>{{ $data->date ?? ""}} </td>
                                                <td>{{ $data->accountno}}</td>
                                                <td>{{ $data->received_from}}</td>
                                                <td>£{{ $data->amount}}</td>
                                                <td>@if($data->status =="0")
                                                    Pending
                                                    @elseif($data->status =="1")
                                                    Complete
                                                    @elseif($data->status =="3")
                                                    Cancel
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center"> <p>No order found</p> </td>
                                            </tr>
                                            @endforelse
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
<script>
    document.getElementById('amount').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    document.getElementById('accountno').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        $("#loading").show();
        document.getElementById('spinner').classList.remove('d-none');
    });
</script>
    
@endsection

@section('script')

<script>
    document.getElementById('amount').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    document.getElementById('accountno').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    document.getElementById('submitBtn').addEventListener('click', function () {
        $("#loading").show();
        document.getElementById('spinner').classList.remove('d-none');
    });
</script>
    
@endsection
