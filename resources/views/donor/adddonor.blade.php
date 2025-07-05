@extends('layouts.admin')

@section('content')
<div class="rightSection">
    <div class="dashboard-content">
        <!-- Title Section -->
        <section class="profile purchase-status">
            <div class="title-section d-flex align-items-center">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Donor List</div>
                <button id="newBtn" type="button" class="btn btn-info ms-auto">Add New</button>
            </div>
        </section>

        <!-- Success/Error Messages -->
        @if(session('message'))
            <section class="px-4">
                <div class="row my-3">
                    <div class="alert alert-success" id="successMessage">{{ session('message') }}</div>
                </div>
            </section>
        @endif
        @if(session('error'))
            <section class="px-4">
                <div class="row my-3">
                    <div class="alert alert-danger" id="errMessage">{{ session('error') }}</div>
                </div>
            </section>
        @endif

        <!-- Add Donor Form -->
        <section class="px-4" id="addThisFormContainer" style="display: none;">
            <div class="row justify-content-md-center bg-white">
                <div class="col-md-8">
                    <form action="{{ route('donor.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                        @csrf
                        <div class="row my-3">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <select name="prefix_name" id="prefix_name" class="form-control">
                                        <option value="">Please Select</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" placeholder="Title" class="form-control @error('title') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="fname">Name</label>
                                    <input type="text" name="fname" id="fname" placeholder="Name" class="form-control @error('fname') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="surname">Surname</label>
                                    <input type="text" name="surname" id="surname" placeholder="Surname" class="form-control @error('surname') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="postcode">Postcode</label>
                                    <input type="text" name="postcode" id="postcode" placeholder="Postcode" class="form-control @error('postcode') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" placeholder="Phone" class="form-control @error('phone') is-invalid @enderror">
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="street">Street</label>
                                    <input type="text" name="street" id="street" placeholder="Street" class="form-control @error('street') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="houseno">House No</label>
                                    <input type="text" name="houseno" id="houseno" placeholder="House No" class="form-control @error('houseno') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="town">Town</label>
                                    <input type="text" name="town" id="town" placeholder="Town" class="form-control @error('town') is-invalid @enderror">
                                </div>
                                <input type="hidden" name="donorid" id="donorid">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror">
                                </div>
                                <div class="mb-3">
                                    <label for="balance">Balance</label>
                                    <input type="text" name="balance" id="balance" placeholder="Balance" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="accno">Account No</label>
                                    <input type="text" name="accno" id="accno" placeholder="Account No" class="form-control">
                                </div>
                            </div>
                            <!-- Form Buttons -->
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-theme mt-2 text-white">Create</button>
                                <button type="button" class="btn btn-warning mt-2 text-white" id="FormCloseBtn">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Donor List Table -->
        <section class="px-4" id="contentContainer">
            <div class="row my-3">
                <div class="col-md-12 my-2 d-flex gap-3 align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="checkAll" value="all">
                        <label class="form-check-label" for="checkAll">All Select</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" for="fromdate">Date From</label>
                        <input type="date" id="fromdate" name="fromdate" class="form-control">
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" for="todate">Date To</label>
                        <input type="date" id="todate" name="todate" class="form-control">
                    </div>
                    <button class="btn btn-primary rounded-pill" id="sentRpt" type="button">Send Mail</button>
                    <a href="{{ route('admin.donor.email') }}" class="btn btn-success rounded-pill">Custom Mail</a>
                </div>

                <!-- Loader -->
                <div id="loading" style="display: none;">
                    <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." loading="lazy" />
                </div>

                <!-- Error Message Container -->
                <div class="ermsg"></div>

                <!-- Table -->
                <div class="col-md-12 mt-2">
                    <div class="overflow-auto">
                        <table class="table table-donor shadow-sm bg-white" id="donorexample">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th style="min-width:160px">Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Account</th>
                                    <th>Balance</th>
                                    <th>Overdrawn</th>
                                    <th>Pending Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @php
                                        $pending_transactions = $user->transactions ? $user->transactions->where('t_type', 'Out')->where('pending', 0)->sum('amount') : 0;
                                        $donorUpBalance = $user->transactions ? $user->transactions->reduce(function ($carry, $tran) {
                                            return $carry + ($tran->t_type == 'In' ? $tran->amount : ($tran->t_type == 'Out' ? -$tran->amount : 0));
                                        }, 0) : 0;
                                    @endphp
                                    <tr>
                                        <td><input class="form-check-input getDid" type="checkbox" name="donorIds[]" value="{{ $user->id }}"></td>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            {{ $user->name }} {{ $user->surname }}
                                            <i class="fa fa-{{ $user->email_verified_at ? 'check-circle' : 'times-circle' }}" style="color: {{ $user->email_verified_at ? 'green' : 'red' }};"></i>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->town }}</td>
                                        <td>
                                            @if ($user->accountno)
                                            {{ $user->accountno ?? '' }}
                                                
                                            @else
                                            <button type="button" user-id="{{ $user->id }}" class="btn btn-primary acc" data-bs-toggle="modal" data-bs-target="#exampleModal">add</button>
                                                
                                            @endif
                                        </td>
                                        <td>£{{ number_format($donorUpBalance, 2) }}</td>
                                        <td>
                                            £{{ number_format($user->overdrawn_amount, 2) }}
                                            <a overdrawn-id="{{ $user->id }}" class="overdrawn" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="fa fa-edit" style="color: #2094f3; font-size: 16px;"></i>
                                            </a>
                                        </td>
                                        <td>£{{ number_format($pending_transactions, 2) }}</td>
                                        <td>
                                            <a class="text-decoration-none bg-dark text-white py-1 px-3 rounded mb-1 d-block text-center" href="{{ route('topup', [$user->id, 0]) }}" target="_blank">
                                                <small>Top Up</small>
                                            </a>
                                            <!-- Send Text Message Button -->
                                            <button type="button" class="btn btn-secondary mb-1 d-block w-100" data-bs-toggle="modal" data-bs-target="#sendTextModal">
                                                <small>Message</small>
                                            </button>

                                            <!-- Send Text Message Modal -->
                                            <div class="modal fade" id="sendTextModal" tabindex="-1" aria-labelledby="sendTextModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="sendTextModalLabel">Send Text Message</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.donor.sendtext', $user->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="textMessage" class="form-label">Message</label>
                                                                    <textarea class="form-control" id="textMessage" name="message" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Send</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="py-1 text-center d-flex gap-2 justify-content-center">
                                                <a href="{{ route('sendemail', $user->id) }}"><i class="fa fa-envelope-o" style="color: #4D617E; font-size: 16px;"></i></a>
                                                <a href="{{ route('donor.profile', $user->id) }}"><i class="fa fa-eye" style="color: #09a311; font-size: 16px;"></i></a>
                                                <a href="{{ route('donor.edit', encrypt($user->id)) }}"><i class="fa fa-edit" style="color: #2094f3; font-size: 16px;"></i></a>
                                                <a id="deleteBtn" rid="{{ $user->id }}"><i class="fa fa-trash-o" style="color: red; font-size: 16px;"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links -->
                    <div class="mt-3">
                        {{-- {{ $users->links() }} --}}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Overdrawn Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2">Update Overdrawn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsgod"></div>
                <div class="mb-3">
                    <label for="overdrawnno" class="form-label">Overdrawn Amount</label>
                    <input type="text" class="form-control" id="overdrawnno">
                    <input type="hidden" class="form-control" value="" id="overdrawnid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="overdrawnBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Donor Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <div class="mb-3">
                    <label for="updaccno" class="form-label">Account</label>
                    <input type="text" class="form-control" id="updaccno">
                    <input type="hidden" class="form-control" value="" id="donnerid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="addaccBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    // CSRF Token Setup
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Hide form by default
    $("#addThisFormContainer").hide();

    // Toggle form visibility
    $("#newBtn").click(function() {
        $("#createThisForm")[0].reset();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn").click(function() {
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        $("#createThisForm")[0].reset();
    });

    // Auto-hide messages
    setTimeout(function() {
        $('#successMessage, #errMessage').fadeOut('fast');
    }, 3000);

    // Check all checkboxes
    $("#checkAll").click(function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    // Uncheck "All Select" when individual checkbox is clicked
    $("#contentContainer").on('click', '.getDid', function() {
        $("#checkAll").prop('checked', false);
    });

    // Delete donor
    $("#contentContainer").on('click', '#deleteBtn', function() {
        if (!confirm('Are you sure?')) return;
        const donorId = $(this).attr('rid');
        $.ajax({
            url: "{{ URL::to('/admin/donor/delete') }}",
            method: "POST",
            data: { donorId },
            success: function(d) {
                $(".ermsg").html(d.message);
                if (d.status == 300) {
                    location.reload();
                }
            },
            error: function() {
                $(".ermsg").html("An error occurred. Please try again.");
            }
        });
    });

    // Add account to donor
    $("#contentContainer").on('click', '.acc', function() {
        $('#donnerid').val($(this).attr("user-id"));
    });

    $("#addaccBtn").click(function() {
        const donnerId = $("#donnerid").val();
        const accno = $("#updaccno").val();
        $.ajax({
            url: "{{ URL::to('/admin/add-account') }}",
            method: "POST",
            data: { donnerId, accno },
            success: function(d) {
                $(".ermsg").html(d.message);
                if (d.status == 300) {
                    location.reload();
                }
            },
            error: function() {
                $(".ermsg").html("An error occurred. Please try again.");
            }
        });
    });

    // Update overdrawn amount
    $("#contentContainer").on('click', '.overdrawn', function() {
        $('#overdrawnid').val($(this).attr("overdrawn-id"));
    });

    $("#overdrawnBtn").click(function() {
        const overdrawnid = $("#overdrawnid").val();
        const overdrawnno = $("#overdrawnno").val();
        $.ajax({
            url: "{{ URL::to('/admin/update-overdrawn') }}",
            method: "POST",
            data: { overdrawnid, overdrawnno },
            success: function(d) {
                $(".ermsgod").html(d.message);
                if (d.status == 300) {
                    location.reload();
                }
            },
            error: function() {
                $(".ermsgod").html("An error occurred. Please try again.");
            }
        });
    });

    // Send report to donors
    $("#sentRpt").click(function() {
        $("#loading").show();
        const donorIds = $('.getDid:checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        const fromdate = $("#fromdate").val();
        const todate = $("#todate").val();
        const checkAll = $("#checkAll").prop('checked') ? "all" : "";

        $.ajax({
            url: "{{ URL::to('/admin/reportall') }}",
            method: "POST",
            data: { donorIds, fromdate, todate, checkAll },
            success: function(d) {
                $(".ermsg").html(d.message);
                $('html, body').animate({ scrollTop: 0 }, 'fast');
            },
            complete: function() {
                $("#loading").hide();
            },
            error: function() {
                $(".ermsg").html("An error occurred. Please try again.");
            }
        });
    });

    // DataTables Initialization
    $('#donorexample').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        order: [[1, 'desc']],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            { extend: 'copy' },
            { extend: 'excel', title: 'Report' },
            {
                extend: 'print',
                exportOptions: { stripHtml: false },
                title: "<p style='text-align:center;'>Data:<br>Report</p>",
                customize: function(win) {
                    $(win.document.body).addClass('white-bg').css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }
        ]
    });
});
</script>
@endsection