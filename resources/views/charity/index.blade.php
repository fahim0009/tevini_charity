@extends('layouts.admin')

@section('content')

<style>
    #addThisFormContainer .card {
        transition: all 0.3s ease;
    }
    
    #addThisFormContainer .form-control, 
    #addThisFormContainer .form-select,
    #addThisFormContainer .input-group-text {
        border-color: #e9ecef;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
    }

    #addThisFormContainer .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }

    #addThisFormContainer .btn-primary {
        background-color: #0d6efd;
        border: none;
        padding: 0.7rem 2rem;
        border-radius: 8px;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        display: block;
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: 0.25rem;
    }

    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #f0f0f0;
        }
    }
</style>

<div class="rightSection">
    <div class="dashboard-content">
        <!-- Title Section -->
        <section class="profile purchase-status">
            <div class="title-section d-flex align-items-center">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Charity List</div>
                <button id="newBtn" type="button" class="btn btn-info ms-auto">Add New</button>
            </div>
        </section>

        <!-- Success/Error Messages (Flash Messages) -->
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

        <!-- AJAX Response Message Container -->
        <section class="px-4">
            <div class="row my-3">
                <div class="ermsg" id="ajaxMessage"></div>
            </div>
        </section>

        <!-- Add/Edit Charity Form -->
        <section class="px-4 py-3" id="addThisFormContainer" style="display: none;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card border-0 shadow-sm rounded-4 mt-2">
                            <div class="card-header bg-transparent border-0 pt-4 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0" id="formTitle">Register New Charity</h5>
                                        <p class="text-muted small mb-0">Fill in the information below to create a new charity account.</p>
                                    </div>
                                    <button type="button" class="btn-close" id="FormCloseBtnX"></button>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                <form id="charityForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="charity_id" id="charity_id" value="">
                                    <input type="hidden" name="acc_no" id="acc_no" value="">

                                    <div class="row g-4">
                                        <div class="col-md-6 border-end-md">
                                            <h6 class="text-primary fw-bold mb-3">General Information</h6>
                                            
                                            <div class="mb-3">
                                                <label for="name" class="form-label small fw-bold">Charity Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" placeholder="Enter charity name" class="form-control" required>
                                                <div class="invalid-feedback" id="name-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" name="email" id="email" placeholder="email@example.com" class="form-control" required>
                                                <div class="invalid-feedback" id="email-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="number" class="form-label small fw-bold">Phone Number <span class="text-danger" id="number-required" style="display:none;">*</span></label>
                                                <input type="text" name="number" id="number" placeholder="e.g. +44 123 4567" class="form-control">
                                                <div class="invalid-feedback" id="number-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="address" class="form-label small fw-bold">Address <span class="text-danger" id="address-required" style="display:none;">*</span></label>
                                                <input type="text" name="address" id="address" placeholder="Street address" class="form-control">
                                                <div class="invalid-feedback" id="address-error"></div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-7">
                                                    <label for="town" class="form-label small fw-bold">Town/City</label>
                                                    <input type="text" name="town" id="town" class="form-control">
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="post_code" class="form-label small fw-bold">Post Code</label>
                                                    <input type="text" name="post_code" id="post_code" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <h6 class="text-primary fw-bold mb-3">Financial Details</h6>

                                            <div class="mb-3">
                                                <label for="acc" class="form-label small fw-bold">Charity Registration Number <span class="text-danger">*</span></label>
                                                <input type="text" name="acc" id="acc" placeholder="Reg No." class="form-control" required>
                                                <div class="invalid-feedback" id="acc-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="bank_statement" class="form-label small fw-bold">Bank Statement</label>
                                                <input type="file" name="bank_statement" id="bank_statement" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                <small class="text-muted">Allowed: PDF, JPG, PNG (Max 5MB)</small>
                                                <div class="invalid-feedback" id="bank_statement-error"></div>
                                                <div class="current-file text-muted small mt-1" id="current-file-info"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="account_name" class="form-label small fw-bold">Bank Account Name</label>
                                                <input type="text" name="account_name" id="account_name" class="form-control">
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-7">
                                                    <label for="account_number" class="form-label small fw-bold">Account Number</label>
                                                    <input type="text" name="account_number" id="account_number" class="form-control">
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="account_sortcode" class="form-label small fw-bold">Sort Code</label>
                                                    <input type="text" name="account_sortcode" id="account_sortcode" placeholder="00-00-00" class="form-control">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="balance" class="form-label small fw-bold">Opening Balance</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-muted">£</span>
                                                    <input type="text" name="balance" id="balance" class="form-control" placeholder="0.00">
                                                </div>
                                                <div class="invalid-feedback" id="balance-error"></div>
                                            </div>

                                            <!-- Password Fields (Only for Edit) -->
                                            <div id="passwordSection" style="display:none;">
                                                <hr class="my-3 opacity-50">
                                                <h6 class="text-primary fw-bold mb-3">Change Password</h6>
                                                <div class="mb-3">
                                                    <label for="password" class="form-label small fw-bold">New Password</label>
                                                    <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
                                                    <div class="invalid-feedback" id="password-error"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label small fw-bold">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm new password">
                                                    <div class="invalid-feedback" id="password_confirmation-error"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4 pt-3 border-top">
                                        <div class="col-12 d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light px-4 fw-semibold" id="FormCloseBtn">Cancel</button>
                                            <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm" id="submitBtn">
                                                <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                                                <span class="iconify me-1" data-icon="fluent:add-circle-24-regular" id="submitIcon"></span> 
                                                <span id="submitBtnText">Create Charity</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charity List Table -->
        <section class="px-4" id="contentContainer">
            <div class="row my-3">
                <div class="stsermsg"></div>

                <div class="col-md-12 mt-2">
                    <div class="overflow-auto">
                        <table class="table table-custom shadow-sm bg-white" id="charityTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Town</th>
                                    <th>Post Code</th>
                                    <th>Charity Number</th>
                                    <th>Balance</th>
                                    <th>Pending</th>
                                    <th>Status</th>
                                    <th>Auto Payment</th>
                                    <th>Bank</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Bank Statement Modal -->
<div class="modal fade" id="bankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bankModalBody" style="text-align:center;"></div>
            <div class="modal-footer">
                <a href="#" id="openInTab" target="_blank" class="btn btn-primary">Open in new tab</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this charity? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
 $(document).ready(function () {

    // ==================== CSRF TOKEN SETUP ====================
    $.ajaxSetup({ 
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } 
    });

    // ==================== VARIABLES ====================
    var isEdit = false;
    var deleteId = null;
    var dataTable = null;

    // ==================== AUTO-HIDE FLASH MESSAGES ====================
    setTimeout(function() {
        $('#successMessage, #errMessage').fadeOut('fast');
    }, 3000);

    // ==================== SHOW AJAX MESSAGE (Like Donor Page) ====================
    function showAjaxMessage(message, type) {
        var className = type === 'success' ? 'alert alert-success' : 'alert alert-danger';
        var icon = type === 'success' ? 
            '<i class="fas fa-check-circle me-2"></i>' : 
            '<i class="fas fa-exclamation-circle me-2"></i>';
        
        $("#ajaxMessage").html(
            '<div class="' + className + '" id="ajaxAlert">' + 
            icon + message + 
            '<button type="button" class="btn-close float-end" onclick="$(\'#ajaxAlert\').alert(\'close\')"></button>' +
            '</div>'
        );
        
        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 'fast');
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            $('#ajaxAlert').alert('close');
        }, 5000);
    }

    // ==================== FORM TOGGLE ====================
    $("#addThisFormContainer").hide();
    
    $("#newBtn").click(function(){
        resetForm();
        isEdit = false;
        $("#formTitle").text('Register New Charity');
        $(".card-header p").text('Fill in the information below to create a new charity account.');
        $("#submitBtnText").text('Create Charity');
        $("#submitIcon").show();
        $("#passwordSection").hide();
        $("#number-required").hide();
        $("#address-required").hide();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn, #FormCloseBtnX").click(function(){
        closeForm();
    });

    function closeForm() {
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        resetForm();
    }

    function resetForm() {
        $('#charityForm')[0].reset();
        $('#charity_id').val('');
        $('#acc_no').val('');
        clearErrors();
        $('#current-file-info').html('');
    }

    // ==================== ERROR HANDLING ====================
    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showErrors(errors) {
        clearErrors();

        // Key mapping: server key -> form field id
        var keyMap = {
            'acc_no': 'acc'
        };

        // Build error summary at top
        var errorItems = '';
        $.each(errors, function(key, messages) {
            $.each(messages, function(i, msg) {
                errorItems += '<li>' + msg + '</li>';
            });
        });

        $("#ajaxMessage").html(
            '<div class="alert alert-danger" id="ajaxAlert">' +
            '<strong><i class="fas fa-exclamation-circle me-2"></i>Please fix the following errors:</strong>' +
            '<ul class="mb-0 mt-2">' + errorItems + '</ul>' +
            '<button type="button" class="btn-close float-end" style="margin-top:-20px;" onclick="$(\'#ajaxAlert\').alert(\'close\')"></button>' +
            '</div>'
        );

        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 'fast');

        // Highlight individual fields
        $.each(errors, function(key, messages) {
            var fieldId = keyMap[key] || key; // use mapped id if exists, else use key directly
            var field = $('#' + fieldId);
            if (field.length) {
                field.addClass('is-invalid');
                var errorDiv = $('#' + fieldId + '-error');
                if (errorDiv.length) {
                    errorDiv.text(messages[0]);
                }
            }
        });
    }

    // ==================== FORM SUBMISSION (AJAX) ====================
    $('#charityForm').on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        $("#ajaxMessage").html(''); // Clear previous messages

        var formData = new FormData(this);
        var btn = $('#submitBtn');
        var btnText = $('#submitBtnText');
        var spinner = $('#submitSpinner');
        var icon = $('#submitIcon');

        // Loading state
        btn.prop('disabled', true).addClass('btn-loading');
        spinner.removeClass('d-none');
        icon.addClass('d-none');
        btnText.text(isEdit ? 'Updating...' : 'Creating...');

        var url = isEdit ? '/admin/add-charity/' + $('#charity_id').val() : '/admin/add-charity';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.success) {
                    showAjaxMessage(response.message, 'success');
                    closeForm();
                    if (dataTable) {
                        dataTable.ajax.reload();
                    }
                } else {
                    showAjaxMessage(response.message, 'error');
                    if (response.errors) {
                        showErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    console.log(xhr.responseJSON.errors);
                    var errors = xhr.responseJSON.errors;
                    showErrors(errors);
                    showAjaxMessage('Please fix the errors in the form.', 'error');
                } else {
                    var message = xhr.responseJSON.message || 'Server Error!!';
                    showAjaxMessage(message, 'error');
                }
            },
            complete: function() {
                btn.prop('disabled', false).removeClass('btn-loading');
                spinner.addClass('d-none');
                icon.removeClass('d-none');
                btnText.text(isEdit ? 'Update Charity' : 'Create Charity');
            }
        });
    });

    // ==================== EDIT CHARITY (AJAX) ====================
    window.editCharity = function(id) {
        $.ajax({
            url: '/admin/add-charity/' + id + '/edit',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    isEdit = true;
                    $("#formTitle").text('Edit Charity');
                    $(".card-header p").text('Update the charity information below.');
                    $("#submitBtnText").text('Update Charity');
                    $("#submitIcon").attr('data-icon', 'fluent:save-24-regular');
                    $("#passwordSection").show();
                    $("#number-required").show();
                    $("#address-required").show();
                    
                    // Fill form
                    $('#charity_id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#number').val(data.number);
                    $('#address').val(data.address);
                    $('#town').val(data.town);
                    $('#post_code').val(data.post_code);
                    $('#acc').val(data.acc_no);
                    $('#acc_no').val(data.acc_no);
                    $('#account_name').val(data.account_name);
                    $('#account_number').val(data.account_number);
                    $('#account_sortcode').val(data.account_sortcode);
                    $('#balance').val(data.balance);
                    $('#password').val('');
                    $('#password_confirmation').val('');
                    
                    // Show current file info
                    if (data.bank_statement) {
                        $('#current-file-info').html('Current file: <a href="/images/' + data.bank_statement + '" target="_blank">' + data.bank_statement + '</a>');
                    } else {
                        $('#current-file-info').html('');
                    }
                    
                    clearErrors();
                    $("#ajaxMessage").html(''); // Clear any previous messages
                    $("#newBtn").hide(100);
                    $("#addThisFormContainer").show(300);
                    
                    // Scroll to top of form
                    $('html, body').animate({
                        scrollTop: $("#addThisFormContainer").offset().top - 50
                    }, 300);
                }
            },
            error: function() {
                showAjaxMessage('Error loading charity data.', 'error');
            }
        });
    };

    // ==================== DELETE CHARITY (AJAX) ====================
    window.deleteCharity = function(id) {
        deleteId = id;
        $('#deleteModal').modal('show');
    };

    $('#confirmDeleteBtn').click(function() {
        
        if (deleteId) {
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

            $.ajax({
                url: '/admin/add-charity/delete/' + deleteId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAjaxMessage(response.message, 'success');
                        if (dataTable) {
                            dataTable.ajax.reload();
                        }
                    } else {
                        showAjaxMessage(response.message, 'error');
                    }
                },
                error: function() {
                    showAjaxMessage('Error deleting charity.', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Delete');
                    $('#deleteModal').modal('hide');
                    deleteId = null;
                }
            });
        }
    });

    // ==================== STATUS TOGGLES (AJAX) ====================
    $(document).on('change', '.campaignstatus', function() {
        var url = "/admin/active-charity";
        var status = $(this).prop('checked') ? 1 : 0;
        var id = $(this).data('id');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: url,
            data: {'status': status, 'id': id},
            success: function(d) {
                if (d.message) {
                    $(".stsermsg").html('<div class="alert alert-success py-2">' + d.message + '</div>');
                    setTimeout(function() { $(".stsermsg").html(''); }, 3000);
                }
            },
            error: function() {
                $(".stsermsg").html('<div class="alert alert-danger py-2">Error updating status.</div>');
            }
        });
    });

    $(document).on('change', '.auto_payment_status', function() {
        var url = "/admin/auto-payment-charity";
        var status = $(this).prop('checked') ? 1 : 0;
        var id = $(this).data('id');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: url,
            data: {'status': status, 'id': id},
            success: function(d) {
                if (d.message) {
                    $(".stsermsg").html('<div class="alert alert-success py-2">' + d.message + '</div>');
                    setTimeout(function() { $(".stsermsg").html(''); }, 3000);
                }
            },
            error: function() {
                $(".stsermsg").html('<div class="alert alert-danger py-2">Error updating auto payment.</div>');
            }
        });
    });

    // ==================== BANK STATEMENT MODAL ====================
    $(document).on('click', '.openBankModal', function(e) {
        e.preventDefault();

        let file = $(this).data('file');
        let fileUrl = "/images/" + file;
        let ext = file.split('.').pop().toLowerCase();
        let html = "";

        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) {
            html = '<img src="' + fileUrl + '" style="width:100%;border-radius:8px;">';
        } else if (ext === 'pdf') {
            html = '<iframe src="' + fileUrl + '" width="100%" height="600px" style="border:none;"></iframe>';
        } else {
            html = '<p>Unsupported file format. <a href="' + fileUrl + '" target="_blank">Click here to download</a></p>';
        }

        $("#bankModalBody").html(html);
        $("#openInTab").attr("href", fileUrl);
        $("#bankModal").modal("show");
    });

    var data = 'Tevini';
    var title = 'Charity report';

    // Destroy existing instance if any
    if ($.fn.DataTable.isDataTable('#charityTable')) {
        $('#charityTable').DataTable().clear().destroy();
    }

    dataTable = $('#charityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('charity.data') }}",
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        order: [[0, 'desc']],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
            {
                extend: 'excel',
                title: title,
                messageTop: data,
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdfHtml5',
                title: 'Report',
                orientation: 'portrait',
                pageSize: 'A4',
                header: true,
                exportOptions: { columns: ':not(:nth-last-child(-n+3))' },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: [
                            { text: data + '\n', bold: true, fontSize: 12 },
                            { text: title + '\n', bold: true, fontSize: 15 }
                        ],
                        margin: [0, 0, 0, 12],
                        alignment: 'center'
                    });

                    doc.defaultStyle.alignment = 'center';

                    var tableNode = null;
                    for (var i = 0; i < doc.content.length; i++) {
                        if (doc.content[i] && doc.content[i].table) {
                            tableNode = doc.content[i];
                            break;
                        }
                    }

                    if (tableNode) {
                        var widths = ['15%', '16%', '10%', '17%', '10%', '8%', '8%', '8%', '8%'];
                        tableNode.table.widths = widths;

                        tableNode.layout = {
                            hLineWidth: function(i, node) { return 0.5; },
                            vLineWidth: function(i, node) { return 0; },
                            hLineColor: function(i, node) { return '#aaa'; },
                            vLineColor: function(i, node) { return '#aaa'; },
                            paddingLeft: function(i, node) { return 2; },
                            paddingRight: function(i, node) { return 2; },
                            paddingTop: function(i, node) { return 4; },
                            paddingBottom: function(i, node) { return 4; }
                        };

                        if (!doc.styles) doc.styles = {};
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 8,
                            color: 'white',
                            fillColor: '#4d617e',
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        };
                        doc.styles.tableBodyEven = {
                            fontSize: 7,
                            color: '#4d617e',
                            fillColor: '#f8f9fa',
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        };
                        doc.styles.tableBodyOdd = {
                            fontSize: 7,
                            color: '#4d617e',
                            fillColor: 'white',
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        };

                        doc.pageMargins = [20, 60, 20, 40];
                    }
                }
            },
            {
                extend: 'print',
                title: "<p style='text-align:center;'>" + data + "<br>" + title + "</p>",
                header: true,
                exportOptions: { columns: ':not(:last-child)' },
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'number', name: 'number' },
            { data: 'address', name: 'address' },
            { data: 'town', name: 'town' },
            { data: 'post_code', name: 'post_code' },
            { data: 'acc_no', name: 'acc_no' },
            { data: 'balance', name: 'balance', orderable: false, searchable: false },
            { data: 'pending', name: 'pending', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'auto_payment', name: 'auto_payment', orderable: false, searchable: false },
            { data: 'bank', name: 'bank', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

});
</script>
@endsection