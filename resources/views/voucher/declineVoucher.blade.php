@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> 
            <div class="mx-2">Cancel Voucher</div>
        </div>
    </section>

    <!-- Image loader -->
    <div id='loading' style='display:none;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="ermsg"></div>

    <section class="">
        <div class="row my-3 mx-0">
            <div class="col-md-12">
                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel">
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
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($wvouchers as $voucher)
                                        <tr>
                                            <td>
                                                <span style="display:none;">{{ $voucher->id }}</span>
                                                {{ $voucher->created_at }} 
                                                <br> 
                                                <small class="text-muted">#{{ $voucher->id }}</small>
                                            </td>
                                            <td>{{ $voucher->charity->name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $voucher->user->name ?? '' }} {{ $voucher->user->surname ?? '' }}
                                                <br>
                                                <small class="text-muted">{{ $voucher->user->email ?? '' }}</small>
                                            </td>
                                            <td>{{ $voucher->cheque_no }}</td>
                                            <td>£{{ number_format($voucher->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-danger">Cancelled</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-toggle="modal" 
                                                        data-target="#transactionModal"
                                                        onclick="loadTransaction({{ $voucher->id }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
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

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-receipt mr-2"></i>Transaction Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionModalBody">
                <!-- Dynamic content loaded here -->
            </div>
            <div class="modal-footer" id="transactionModalFooter">
                <!-- Accept button loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
 $(document).ready(function() {
    $.ajaxSetup({ 
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } 
    });
});

// Store transaction data (only fields with values)
const transactionData = {};

@foreach($wvouchers as $voucher)
    @if($voucher->transaction)
    transactionData[{{ $voucher->id }}] = {
        @foreach($voucher->transaction->getAttributes() as $key => $value)
            @if(!is_null($value) && $value !== '')
            '{{ $key }}': @json($value),
            @endif
        @endforeach
    };
    @endif
@endforeach

function loadTransaction(voucherId) {
    const data = transactionData[voucherId];
    const reAcceptUrl = "{{ route('admin.voucher.reaccept', ['voucher' => ':id']) }}".replace(':id', voucherId);
    
    // Field labels mapping
    const labels = {
        'id': 'Transaction ID',
        't_id': 'Transaction Ref',
        'charity_id': 'Charity ID',
        'user_id': 'User ID',
        'donation_id': 'Donation ID',
        'standing_donationdetails_id': 'Standing Donation ID',
        'source': 'Source',
        't_type': 'Transaction Type',
        'commission': 'Commission',
        'amount': 'Amount',
        'note': 'Note',
        't_unq': 'Unique Ref',
        'order_id': 'Order ID',
        'cheque_no': 'Cheque No',
        'title': 'Title',
        'barcode_image': 'Barcode Image',
        'donation_by': 'Donation By',
        'pending': 'Pending',
        'expired': 'Expired',
        'gift': 'Gift',
        'date': 'Date',
        'clear_gift': 'Clear Gift',
        'notification': 'Notification',
        'gateway_id': 'Gateway ID',
        'campaign_id': 'Campaign ID',
        'onegiv_transaction_id': 'OneGiv Transaction ID',
        'voucher_create_date': 'Voucher Create Date',
        'voucher_complete_date': 'Voucher Complete Date',
        'provoucher_batch_id': 'Provoucher Batch ID',
        'batch_no': 'Batch No',
        'status': 'Status',
        'crdAcptID': 'Card Accept ID',
        'crdAcptLoc': 'Card Accept Location',
        'updated_by': 'Updated By',
        'created_by': 'Created By',
        'created_at': 'Created At',
        'updated_at': 'Updated At'
    };
    
    const statusLabels = {
        '0': '<span class="badge badge-warning">Pending</span>',
        '1': '<span class="badge badge-success">Accepted</span>',
        '3': '<span class="badge badge-danger">Cancelled</span>'
    };
    
    const typeLabels = {
        'Out': '<span class="text-danger">Outgoing</span>',
        'In': '<span class="text-success">Incoming</span>'
    };
    
    const boolLabels = {
        '1': '<span class="text-success">Yes</span>',
        '0': '<span class="text-secondary">No</span>'
    };
    
    let html = '';
    
    if (data && Object.keys(data).length > 0) {
        html += '<div class="row"><div class="col-md-6">';
        
        const entries = Object.entries(data);
        const totalFields = entries.length;
        const halfFields = Math.ceil(totalFields / 2);
        let count = 0;
        let firstHalf = true;
        
        for (const [key, value] of entries) {
            let displayValue = value;
            
            if (key === 'status') {
                displayValue = statusLabels[value] || value;
            } else if (key === 't_type') {
                displayValue = typeLabels[value] || value;
            } else if (key === 'amount' || key === 'commission') {
                displayValue = '<strong>£' + parseFloat(value).toFixed(2) + '</strong>';
            } else if (['expired', 'pending', 'clear_gift', 'notification'].includes(key)) {
                displayValue = boolLabels[value] || value;
            } else if (key === 'barcode_image' && value) {
                displayValue = '<img src="' + value + '" class="img-fluid" style="max-height: 80px;" />';
            }
            
            const label = labels[key] || key;
            
            html += '<div class="form-group row mb-2 border-bottom pb-2">' +
                '<label class="col-sm-5 col-form-label font-weight-bold text-muted" style="font-size: 12px;">' + label + ':</label>' +
                '<div class="col-sm-7 d-flex align-items-center" style="font-size: 13px;">' + displayValue + '</div>' +
                '</div>';
            
            count++;
            
            if (count === halfFields && firstHalf) {
                html += '</div><div class="col-md-6">';
                firstHalf = false;
            }
        }
        
        html += '</div></div>';
    } else {
        html = '<div class="alert alert-warning text-center">' +
            '<i class="fas fa-exclamation-triangle mr-2"></i>No transaction data available for this voucher.' +
            '</div>';
    }
    
    $('#transactionModalBody').html(html);
    
    // Add Accept button - NO @csrf here, it's handled by ajaxSetup
    $('#transactionModalFooter').html(
        '<button type="button" class="btn btn-secondary" data-dismiss="modal">' +
        '<i class="fas fa-times"></i> Close</button>' +
        '<button type="button" class="btn btn-success d-none" id="acceptVoucherBtn" data-url="' + reAcceptUrl + '">' +
        '<i class="fas fa-check-circle"></i> Accept Voucher</button>'
    );
}

// Handle accept button click
 $(document).on('click', '#acceptVoucherBtn', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you sure you want to re-accept this voucher?\n\n• Change status to Accepted\n• Add amount to charity balance\n• Deduct from user balance')) {
        return false;
    }
    
    var url = $(this).data('url');
    var btn = $(this);
    
    // Disable button and show loading
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    $('#loading').show();
    
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',  // This is important!
        success: function(response) {
            $('#loading').hide();
            $('#transactionModal').modal('hide');
            
            // Show success message and reload
            showNotification(response.message, 'success');
            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            $('#loading').hide();
            btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Accept Voucher');
            
            let errorMsg = 'Something went wrong.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showNotification(errorMsg, 'danger');
        }
    });
});

// Notification function
function showNotification(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var html = '<div class="alert ' + alertClass + ' alert-dismissible fade show mx-3 mt-3" role="alert">' +
        message +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span></button></div>';
    
    $('.ermsg').html(html);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.ermsg .alert').alert('close');
    }, 5000);
}
</script>
@endsection