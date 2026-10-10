@extends('layouts.admin')

@section('content')

<style>
    .donation-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>

<div class="rightSection">
    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    <div class="mx-2">New Donation List</div>
                </div>
                <!-- Complete Button Moved to Top -->
                <button class="text-decoration-none bg-success text-white py-1 px-3 rounded mb-1 completeBtn">
                    <i class="fas fa-check"></i> Complete Selected
                </button>
            </div>
            <div class="px-4 ermsg mt-2"></div>
        </section>
   
        <!-- Image loader -->
        <div id="loading" style="display: none;">
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
        </div>

        <section class="profile purchase-status px-4">
            <div class="title-section">
                <div class="col-md-6">
                    <label for="charityFilter">Filter by Charity:</label>
                    <select id="charityFilter" class="form-control">
                        <option value="">Select Charity</option>
                        @foreach ($charities as $charity)
                        <option value="{{ $charity->name }}">{{ $charity->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="px-4" id="contentContainer">
            <div class="row my-3">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white" id="example1">
                            <thead>
                                <tr>
                                    <!-- Select All Checkbox Added Here -->
                                    <th class="text-center" style="width: 50px;">
                                        <input type="checkbox" id="selectAll" class="donation-checkbox">
                                    </th>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Beneficiary</th> 
                                    <th>Amount</th>
                                    <th>Anonymous Donation</th>
                                    <th>Charity Note</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donation as $data)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="donation_id[]" value="{{ $data->id }}" class="donation-checkbox" data-charity="{{ $data->charity_id }}">
                                        </td>
                                        <td data-order="{{ $data->created_at->timestamp }}">{{ $data->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $data->user->name ?? '' }} {{ $data->user->surname ?? '' }}</td>

                                        <td data-search="{{ trim($data->charity->name) }}">
                                            <a href="{{ route('charity.pay', [$data->charity_id, $data->amount]) }}" class="my-2 btn btn-sm btn-success text-white" target="blank"> 
                                                {{ trim($data->charity->name) }} 
                                            </a>
                                        </td>

                                        <td>£{{ $data->amount }}</td>
                                        <td>{{ $data->ano_donation == 'true' ? 'Yes' : 'No' }}</td>
                                        <td>{{ $data->charitynote }}</td>
                                        <td>{{ $data->mynote }}</td>
                                        <td>Pending</td>
                                        <td> 
                                            <select class="status form-control">
                                                <option value="0|{{ $data->id }}" @if($data->status == "0") selected @endif>Pending</option> 
                                                <option value="1|{{ $data->id }}" @if($data->status == "1") selected @endif>Complete</option> 
                                                <option value="3|{{ $data->id }}" @if($data->status == "3") selected @endif>Cancel</option> 
                                            </select> 
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No donations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    var title = 'Report: ';
    var data = 'Data: ';

    var table = $('#example1').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [{ type: 'date', targets: [0] }],
        order: [[1, 'desc']],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            { extend: 'copy' },
            { extend: 'excel', title: title },
            { 
                extend: 'print',
                exportOptions: { stripHtml: false },
                title: "<p style='text-align:center;'>" + data + "<br>" + title + "</p>",
                header: true,
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }
        ]
    });

    // Select All Checkbox Logic
    $('#selectAll').on('click', function() {
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('.donation-checkbox', rows).prop('checked', this.checked);
    });

    // Charity Filter Logic
    $('#charityFilter').on('change', function() {
        var charity = $(this).val();
        if (charity) {
            table.column(3).search(charity).draw();
        } else {
            table.column(3).search('').draw();
        }
    });

    // CSRF setup
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Helper function to display messages
    function showMessage(message, type = 'success') {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var html = "<div class='alert " + alertClass + "'>" + message + "</div>";
        $(".ermsg").html(html);
    }

    // Status update handler
    var statusUrl = "{{ URL::to('/admin/donation-status') }}";
    
    $('.status').on('change', function() {
        let [status, did] = this.value.split("|");
        let userChoice = confirm("Do you want to send email in charity?");
        let sendEmail = userChoice ? 1 : 2;

        $("#loading").show();
        $(".ermsg").html('');

        $.ajax({
            url: statusUrl,
            type: 'POST',
            data: { status: status, did: did, send_email: sendEmail },
            success: function(d) {
                if (d.status == 300) {
                    showMessage(d.message, 'success');
                    // Delay increased to 2000ms (2 seconds) so user can read it
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showMessage(d.message || 'Something went wrong.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showMessage(errorMessage, 'error');
            },
            complete: function() {
                $("#loading").hide();
            }
        });
    });

    // Complete Button Handler (Triggered from Top Button)
    $('.completeBtn').on('click', function() {
        var selected = [];
        var charity = [];
        
        $('.donation-checkbox:checked').not('#selectAll').each(function() {
            selected.push($(this).val());
            charity.push($(this).data('charity'));
        });

        if (selected.length === 0) {
            showMessage('Please select at least one donation.', 'error');
            return;
        }

        let uniqueCharities = [...new Set(charity)];
        let userChoice = confirm("Do you want to send email in charity?");
        let sendEmail = userChoice ? 1 : 2;

        $("#loading").show();
        $(".ermsg").html('');

        $.ajax({
            url: "{{ URL::to('/admin/donation-complete') }}",
            type: 'POST',
            data: { 
                donation_ids: selected,
                charity_ids: uniqueCharities,
                send_email: sendEmail 
            },
            success: function(d) {
                if (d.status == 300) {
                    showMessage(d.message, 'success');
                    // Delay increased to 2000ms (2 seconds) so user can read it
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showMessage(d.message || 'Something went wrong.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while completing the donation.';
                // Catch Laravel validation errors or server errors
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } 
                // If it's a 422 Validation Error and has fields
                else if (xhr.status === 422 && xhr.responseJSON.errors) {
                    errorMessage = 'Validation failed: ' + Object.values(xhr.responseJSON.errors).join(', ');
                }
                showMessage(errorMessage, 'error');
            },
            complete: function() {
                $("#loading").hide();
            }
        });
    });
});
</script>
@endsection