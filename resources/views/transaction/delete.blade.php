@extends('layouts.admin')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Transaction Delete</div>
        </div>
    </section>


<section class="card m-3">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                    <button type="button" class="close text-right" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (isset($success))
                <div class="alert alert-success">
                    {{ $success }}
                    <button type="button" class="close text-right float-right" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            <div class="col-md-12">
                <form class="form-inline" action="{{route('admin.transactionSearch')}}" method="POST">
                    @csrf         

                    <div class="row justify-content-center">

                        <div class="col-md-4">
                            <div class="form-group my-2">
                                <label for="tranId"><small>Tran ID</small> </label>
                                <input class="form-control mr-sm-2" id="tranId" name="tranId" type="text" value="{{ $tranId ?? '' }}">
                            </div>
                        </div>

                        
                        <div class="col-md-4">
                            <div class="form-group my-2">
                                <label for="voucher"><small> Voucher </small> </label>
                                <input class="form-control mr-sm-2" id="voucher" name="voucher" type="text" value="">
                            </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-group d-flex mt-4">
                              <button class="text-white btn-theme m-1" type="submit">check</button>
                              <button class="btn btn-sm btn-danger" id="dltBtn">Delete</button>
                            </div>
                        </div>

                    </div>


                </form>
            </div>
            
        </div>
    </div>
</section>


@if (isset($chktran) && $chktran->count() > 0)
<section class="card m-3">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            <div class="stsermsg"></div>
            
            <div class="col-md-12 mt-2 text-center">
                <div class="overflow">
                    <table class="table table-custom shadow-sm bg-white" id="example">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Donor Name</th>
                                <th>TranID </th>
                                <th>Barcode </th>
                                <th>Amount </th>
                                <th>Status </th>
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($chktran as $transaction)
                            
                           
                            <tr>
                                    <td><span style="display:none;">{{ $transaction->id }}</span>{{ $transaction->created_at }}</td>
                                    
                                    
                                    <td>{{ $transaction->user->name}}</td>
                                    <td>{{ $transaction->t_id}}</td>
                                    <td>{{ $transaction->cheque_no}}</td>
                                    <td>£{{ $transaction->amount}}</td>
                                    <td>{{ $transaction->status}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateModal" data-id="{{ $transaction->id }}" >Update</button>
                                    </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</section>  

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updateForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="transactionId" name="transactionId">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endif



</div>
@endsection

@section('script')
<script>
    document.getElementById('dltBtn').addEventListener('click', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete these data?')) {
            return;
        }

        const tranId = document.getElementById('tranId').value;

        if (!tranId) {
            alert('Please enter  Transaction ID.');
            return;
        }

        fetch("{{ route('admin.transactionChangeStatus') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tranId: tranId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data deleted successfully.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete data.');
            }
        })
        .catch(() => {
            alert('An error occurred while deleting data.');
        });
    });
</script>


<script>
    $(document).ready(function() {
        // 1. Handle Modal Opening and Data Passing
        $('#updateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id'); // Extract info from data-* attributes
            
            // Update the modal's content.
            var modal = $(this);
            modal.find('#transactionId').val(id);
            
            // Optional: Clear the date field or set it to today when opening
            modal.find('#date').val(''); 
        });

        // 2. Handle Form Submission
        $('#updateForm').on('submit', function(e) {
            e.preventDefault();
            
            const transactionId = $('#transactionId').val();
            const date = $('#date').val();

            console.log('Submitting update for Transaction ID:', transactionId, 'with Date:', date);

            if(!date) {
                alert('Please select a date');
                return;
            }

            fetch("{{ route('admin.deleteTransactionUpdate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    transactionId: transactionId,
                    date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Transaction updated successfully.');
                    $('#updateModal').modal('hide'); // Close modal
                    // window.location.reload(); // Reload to see changes
                } else {
                    alert(data.message || 'Failed to update transaction.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating transaction.');
            });
        });
    });
</script>

@endsection
