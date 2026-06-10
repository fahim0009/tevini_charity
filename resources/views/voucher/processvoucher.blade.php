@extends('layouts.admin')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet"/>
<style>
    table {
        overflow: visible;
    }
    select.form-control {
        position: static !important;
    }
    #rowCountBadge {
        vertical-align: middle;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
        min-width: 24px;
        text-align: center;
    }
    .badge-primary {
        background-color: #007bff;
    }
    .badge-success {
        background-color: #28a745;
    }
    .badge-warning {
        background-color: #ffc107;
    }
    .badge-secondary {
        background-color: #6c757d;
    }



</style>

</style>


<div class="dashboard-content" id="focusBcode">


    <section class="profile purchase-status sticky-voucher-header">
        <div class="title-section d-flex justify-content-between align-items-center">
            <div>
                <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                Process Voucher
                <span id="rowCountBadge" class="badge badge-primary ml-2" style="font-size: 12px;">0</span>
            </div>

            <div class="ml-auto" >
                <input type="text" id="batch_number" class="form-control" placeholder="Batch Number">
            </div>

            <div class="ml-auto">
                <button class="iconify" data-icon="mdi:book" data-inline="false" id="openVoucherModal" style="cursor: pointer;"></button>

                <!-- Modal remains exactly the same, but replace the table body and image gallery inside it -->
                <div class="modal fade" id="fullWidthModal" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="fullWidthModalLabel">Voucher</h5>
                                
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Readable Barcode</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Not Readable</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                                        <div class="d-flex justify-content-end p-2">
                                            <button class="btn btn-secondary" id="addToProcess">Add to process</button>
                                            <button class="btn btn-warning" id="deleteProcess">Delete process</button>
                                        </div>

                                        <table class="table table-bordered table-striped mt-2" id="readableBarcodeTable">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Image</th>
                                                    <th>Barcode</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <!-- TBODY IS NOW EMPTY - Yajra will fill this -->
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                        <div class="d-flex justify-content-end p-2">
                                            <button class="btn btn-warning" id="deleteProcessImage">Delete Voucher Image</button>
                                        </div>
                                        <div class="image-gallery">
                                            <!-- ADDED ID HERE -->
                                            <div class="row" id="imageGalleryRow">
                                                <!-- AJAX will fill this -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="closeVoucherModal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ermsg"></div>
        </div> <!-- THIS WAS THE MISSING CLOSING DIV -->
    </section>



        <!-- Image loader -->
        <div id='loading' style='display:none;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
        </div>
        <!-- Image loader -->
        <section class="">
            <div class="row my-3 mx-0">
                <div class="col-md-12 bg-white px-4">
                    <div class="form-container">
                        <div class="overflow mx-auto">
                            <table class="table shadow-sm">
                                <thead>
                                    <tr>
                                        <th>Charity</th>
                                        <th>Donor</th>
                                        <th>Donor Name</th>
                                        <th>Check No</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                        <th>Waiting</th>
                                        <th>Expired</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="inner">
                                    @forelse (App\Models\Draft::all() as $index => $voucher)
                                    @if($index == 0)
                                    <tr class="item-row">
                                        <td width="230px">
                                            <select name="charity" id="charity_list" class="form-control charitylist">
                                                <option value>Select</option>
                                                @foreach ($charities as $charity)
                                                    <option value="{{ $charity->id }}" @if($voucher->charity_id == $charity->id) selected @endif>{{ $charity->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="{{ $voucher->donor_acc }}" placeholder="Type Acc no...">
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;" type="text" name="donor_name[]" readonly class="form-control donorAcc" value="{{ $voucher->donor_name }}">
                                            <input type="hidden" name="donor[]" class="donorid" value="{{ $voucher->donor_id }}">
                                        </td>
                                        <td width="100px">
                                            <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value="{{ $voucher->voucher_number }}">
                                        </td>
                                        <td width="40px">
                                            <input style="min-width: 30px;" name="amount[]" type="text" class="amount form-control" value="{{ $voucher->amount }}">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value="{{ $voucher->note }}">
                                        </td>
                                        <td width="150px">
                                            <select name="waiting[]" class="form-control">
                                                <option value="No" @if(isset($voucher->waiting) && $voucher->waiting == "No") selected @endif>No</option>
                                                <option value="Yes" @if(isset($voucher->waiting) && $voucher->waiting == "Yes") selected @endif>Yes</option>
                                            </select>
                                        </td>
                                        <td width="150px">
                                            <select name="expired[]" class="form-control">
                                                <option value="No" @if(isset($voucher->expired) && $voucher->expired == "No") selected @endif>No</option>
                                                <option value="Yes" @if(isset($voucher->expired) && $voucher->expired == "Yes") selected @endif>Yes</option>
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @else
                                    <tr class="item-row" style="position:relative;">
                                        <td width="200px" style="display:inline-flex;">
                                            <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 81px;" onclick="removeRow(event)">X</div>
                                        </td>
                                        <td width="150px">
                                            <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="{{ $voucher->donor_acc }}" placeholder="Type Acc no...">
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;" type="text" name="donor_name[]" readonly class="form-control donorAcc" value="{{ $voucher->donor_name }}">
                                            <input type="hidden" name="donor[]" class="donorid" value="{{ $voucher->donor_id }}">
                                        </td>
                                        <td width="100px">
                                            <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value="{{ $voucher->voucher_number }}">
                                        </td>
                                        <td width="40px">
                                            <input style="min-width: 30px;" name="amount[]" type="text" class="amount form-control" value="{{ $voucher->amount }}">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value="{{ $voucher->note }}">
                                        </td>
                                        <td width="150px">
                                            <select name="waiting[]" class="form-control">
                                                <option value="No" @if(isset($voucher->waiting) && $voucher->waiting == "No") selected @endif>No</option>
                                                <option value="Yes" @if(isset($voucher->waiting) && $voucher->waiting == "Yes") selected @endif>Yes</option>
                                            </select>
                                        </td>
                                        <td width="150px">
                                            <select name="expired[]" class="form-control">
                                                <option value="No" @if(isset($voucher->expired) && $voucher->expired == "No") selected @endif>No</option>
                                                <option value="Yes" @if(isset($voucher->expired) && $voucher->expired == "Yes") selected @endif>Yes</option>
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    @empty
                                    <tr class="item-row" id="firstRow">
                                        <td width="230px">
                                            <select name="charity" id="charity_list" style="min-width: 100px;" class="form-control charitylist">
                                                <option value>Select</option>
                                                @foreach ($charities as $charity)
                                                    <option value="{{ $charity->id }}">{{ $charity->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;" class="form-control donor" type="number" name="donor_acc[]" id="donor_acc_num" placeholder="Type Acc no...">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 100px;" type="text" name="donor_name[]" readonly class="form-control donorAcc" id="donor_acc_name">
                                            <input type="hidden" name="donor[]" id="donorid" class="donorid">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 100px;" name="check[]" id="donor_check" type="text" class="form-control check">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 100px;" name="amount[]" type="text" class="form-control amount" id="d_amnt">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" id="d_note">
                                        </td>
                                        <td width="150px">
                                            <select name="waiting[]" class="form-control" id="d_waiting">
                                                <option value="No">No</option>
                                                <option value="Yes">Yes</option>
                                            </select>
                                        </td>
                                        <td width="150px">
                                            <select name="expired[]" class="form-control" id="d_expired">
                                                <option value="No">No</option>
                                                <option value="Yes">Yes</option>
                                            </select>
                                        </td>
                                        <td id="barcode_status"></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tr>
                                    <td colspan="4">
                                        <span type="submit" class="text-white btn-theme add-row"> + Add</span>
                                    </td>
                                    <td width="40px">
                                        <span>Total</span>
                                    </td>
                                    <td width="250px">
                                        <input style="min-width: 200px;" id="total" readonly type="text" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-md-3 my-2">
                                                <button class="text-white btn-theme ml-1 mb-4" id="Draftvoucher" type="button">Save Draft</button>
                                            </div>
                                            <div class="col-md-4 my-2">
                                                <button class="text-white btn-theme ml-1 mb-4" id="addvoucher" type="button">Process Voucher</button>
                                            </div>
                                            <div class="col-md-3 m-2">
                                                <button class="text-white btn-theme ml-1 mb-4" id="uploadPdfButton" type="button" data-toggle="modal" data-target="#uploadPdfModal">Upload PDF</button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="uploadPdfModal" tabindex="-1" role="dialog" aria-labelledby="uploadPdfModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="uploadPdfModalLabel">Upload PDF</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Image loader -->
                                                                <div id='pdfloader' style='display:none;'>
                                                                    <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
                                                                </div>
                                                                <div class="errmsg"></div>
                                                                <form action="#" enctype="multipart/form-data" method="POST">
                                                                    @csrf
                                                                    <div class="form-group">
                                                                        <label for="pdfFile">Choose file</label>
                                                                        <input type="file" class="form-control-file" id="pdfFile" name="pdfFile[]" accept="application/pdf" multiple required>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="uploadPdfSubmit">Upload</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="barcode">*** Please click this field if scanner not working.</label>
                                        <input style="min-width: 200px;" id="barcode" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">No Donor Found this voucher</div>
            </div>
        </section>

        <section class="d-none" id="notReadableBook">
            <div class="row my-3 mx-0">
                <div class="col-md-12 bg-white px-4">
                    <div class="form-container">
                        <div class="overflow mx-auto">
                            <h4>*** These vouchers have no donor information. Please check the images and update the information manually.</h4>

                            <div class="row"  id="inner2">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>







    </div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>



<script type="text/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('.charitylist').select2();

    // function removeRow(event) {
    //     event.target.parentElement.parentElement.remove();
    // }

    function removeRow(event) {
        event.target.parentElement.parentElement.remove();
        updateRowCount(); // Add this line
        net_total(); // Also recalculate total
    }

    var readableTable; 

    $(document).ready(function() {

    // Function to count and update row count badge
    function updateRowCount() {
        var rowCount = $('#inner tr.item-row').length;
        $('#rowCountBadge').text(rowCount);
        
        if (rowCount === 0) {
            $('#rowCountBadge').removeClass('badge-primary badge-warning badge-success').addClass('badge-secondary');
        } else if (rowCount < 5) {
            $('#rowCountBadge').removeClass('badge-secondary badge-warning badge-success').addClass('badge-primary');
        } else if (rowCount < 10) {
            $('#rowCountBadge').removeClass('badge-secondary badge-primary badge-success').addClass('badge-warning');
        } else {
            $('#rowCountBadge').removeClass('badge-secondary badge-primary badge-warning').addClass('badge-success');
        }
    }


    // Initial count
    updateRowCount();

    // Remove row function
    window.removeRow = function(event) {
        event.target.parentElement.parentElement.remove();
        updateRowCount();
        net_total();
    };



        $(".add-row").click(function() {
            var markup = `
                <tr class="item-row" style="position:relative;">
                    <td width="200px" style="display:inline-flex;">
                        <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 81px;" onclick="removeRow(event)">X</div>
                    </td>
                    <td width="200px">
                        <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" placeholder="Type Acc no...">
                    </td>
                    <td width="250px">
                        <input style="min-width:100px" type="text" name="donor_name[]" readonly class="form-control donorAcc">
                        <input type="hidden" name="donor[]" class="donorid">
                    </td>
                    <td width="250px">
                        <input style="min-width:100px" name="check[]" type="text" class="check form-control">
                    </td>
                    <td width="20px">
                        <input style="min-width:30px" name="amount[]" type="text" class="amount form-control">
                    </td>
                    <td width="250px">
                        <input style="min-width:200px" name="note[]" type="text" class="form-control note">
                    </td>
                    <td width="150px">
                        <select name="waiting[]" class="form-control">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </td>
                    <td width="150px">
                        <select name="expired[]" class="form-control">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </td>
                </tr>`;
            $("table #inner").append(markup);
            updateRowCount();
        });

        // CSRF token setup for AJAX
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        var url = "{{ URL::to('/admin/pvoucher-store') }}";
        $("body").delegate("#addvoucher", "click", function(event) {
            event.preventDefault();
            $("#loading").show();
            var batch_no = $("#batch_number").val();

            if (!batch_no) {
                $(".ermsg").html("<div class='alert alert-danger'>...<b>Batch number is required.</b></div>");
                $("#loading").hide();
                return;
            }

            
            var rows = [];
            $(".item-row").each(function() {
                rows.push({
                    donorId:  $(this).find('.donorid').val(),
                    donorAcc: $(this).find('.donor').val(),
                    chqNo:    $(this).find('.check').val(),
                    amount:   $(this).find('.amount').val(),
                    note:     $(this).find('.note').val(),
                    waiting:  $(this).find('select[name="waiting[]"]').val(),
                    expired:  $(this).find('select[name="expired[]"]').val(),
                });
            });

            $.ajax({
                url: url,
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token:    $('meta[name="csrf-token"]').attr('content'),
                    charityId: $("select[name='charity']").val(),
                    batch_no:  batch_no,
                    rows:      rows,
                }),
                success: function(d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message); pagetop();
                    } else if (d.status == 300) {
                        $(".ermsg").html(d.message); pagetop();
                        window.setTimeout(function() {
                            window.location.href = "{{ route('instreport', ':id') }}".replace(':id', d.batch_id);
                        }, 2000);
                    }
                },
                complete: function() { $("#loading").hide(); },
                error: function(d) { console.log(d); }
            });
        });

        // Voucher draft
        var urld = "{{ URL::to('/admin/pvoucher-draft') }}";
        $("body").delegate("#Draftvoucher", "click", function(event) {
            event.preventDefault();
            $("#loading").show();

            var charityId = $("select[name='charity']").val();
            var donorIds = $("input[name='donor[]']").map(function() { return $(this).val(); }).get();
            var donorNms = $("input[name='donor_name[]']").map(function() { return $(this).val(); }).get();
            var donorAccs = $("input[name='donor_acc[]']").map(function() { return $(this).val(); }).get();
            var chqNos = $("input[name='check[]']").map(function() { return $(this).val(); }).get();
            var amts = $("input[name='amount[]']").map(function() { return $(this).val(); }).get();
            var notes = $("input[name='note[]']").map(function() { return $(this).val(); }).get();
            var waitings = $("select[name='waiting[]']").map(function() { return $(this).val(); }).get();
            var expireds = $("select[name='expired[]']").map(function() { return $(this).val(); }).get();

            $.ajax({
                url: urld,
                method: "POST",
                data: { charityId, donorIds, donorNms, donorAccs, chqNos, amts, notes, waitings, expireds },
                success: function(d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                        pagetop();
                    } else if (d.status == 300) {
                        $(".ermsg").html(d.message);
                        pagetop();
                    }
                },
                complete: function() {
                    $("#loading").hide();
                },
                error: function(d) {
                    console.log(d);
                }
            });
        });

        var urlf = "{{ URL::to('/admin/find-name') }}";
        $("body").delegate(".donor", "keyup", function(event) {
            event.preventDefault();
            var donoracc = $(this).val();
            var row = $(this).parents('.item-row');

            $.ajax({
                url: urlf,
                method: "POST",
                data: { accno: donoracc },
                success: function(d) {
                    if (d.status == 300) {
                        row.find('.donorAcc').val(d.donorname);
                        row.find('.donorid').val(d.donorid);
                    }
                },
                error: function(d) {
                    console.log(d);
                }
            });
        });

        // Track clicked element
        var clicked;
        $(document).on("mousedown", function(e) {
            clicked = $(e.target);
        });

        // Prevent scrolling to bottom when focusing #barcode
        $("body").on("blur", "input", function() {
            if (!clicked.is(".donor") && !clicked.is(".check") && !clicked.is(".amount") && !clicked.is(".note") && !clicked.is(".waiting") && !clicked.is("span#select2-charity_list-container.select2-selection__rendered")) {
                $("#barcode").focus({ preventScroll: true });
            }
        });

        $('#charity_list').on("select2:selecting", function(e) {
            $("#barcode").focus({ preventScroll: true });
        });

        // Focus on barcode input on load
        $("#barcode").focus();

        // Barcode handling
        var urlbr = "{{ URL::to('/admin/barcode') }}";
        $("#barcode").change(function() {
            event.preventDefault();
            var barcode = $(this).val();

            // Check for duplicate barcode
            var check = $("input[name='check[]']").map(function() { return $(this).val(); }).get();
            check.push(barcode);
            var seen = check.filter((s => v => s.has(v) || !s.add(v))(new Set));

            if (Array.isArray(seen) && seen.length) {
                $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This voucher number has already been scanned.</b></div>");
                setTimeout(function() {
                    $(".ermsg").html("");
                }, 3000);
                $("#barcode").val("");
                return;
            }

            $.ajax({
                url: urlbr,
                method: "POST",
                data: { barcode: barcode },
                success: function(d) {
                    $("#barcode").val("");
                    if (d.status == 300) {
                        console.log(d);
                        if ($('#donorid').val() === '') {
                            $('#donorid').val(d.donorid);
                            $('#donor_acc_num').val(d.donoracc);
                            $('#donor_acc_name').val(d.donorname);
                            $('#donor_check').val(barcode);
                            $('#d_amnt').val(d.amount);
                            $('#barcode_status').html(d.barcodeStatus);
                        } else {
                            var markup = `
                                <tr class="item-row" style="position:relative;">
                                    <td width="200px" style="display:inline-flex;">
                                        <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 81px;" onclick="removeRow(event)">X</div>
                                    </td>
                                    <td width="200px">
                                        <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="${d.donoracc}" placeholder="Type Acc no...">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width:100px" type="text" value="${d.donorname}" name="donor_name[]" readonly class="form-control donorAcc">
                                        <input type="hidden" name="donor[]" value="${d.donorid}" class="donorid">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width:100px" name="check[]" type="text" value="${barcode}" class="form-control check">
                                    </td>
                                    <td width="20px">
                                        <input style="min-width:30px" name="amount[]" type="text" value="${d.amount}" class="amount form-control">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width:200px" name="note[]" type="text" class="form-control note">
                                    </td>
                                    <td width="150px">
                                        <select name="waiting[]" class="form-control">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </td>
                                    <td width="150px">
                                        <select name="expired[]" class="form-control">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </td>
                                    <td width="250px">${d.barcodeStatus}</td>
                                </tr>`;
                            $("table #inner").append(markup);
                        }
                        $("#barcode").val("");
                        net_total();
                    }
                },
                error: function(d) {
                    console.log(d);
                }
            });
        });

        net_total();

        $("body").delegate(".amount", "keyup", function(event) {
            net_total();
        });

        function net_total() {
            var total = 0;
            $('.amount').each(function() {
                total += ($(this).val() - 0);
            });
            $('#total').val(total.toFixed(2));
        }
        
        var pdfurl = "{{ URL::to('/admin/pdf-to-text') }}";
        $('#uploadPdfSubmit').on('click', function(e) {
            e.preventDefault();
            $('#uploadPdfSubmit').prop('disabled', true);
            $('#uploadPdfSubmit').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');

            let formData = new FormData();
            let fileInput = document.querySelector('#pdfFile');

            if (fileInput.files.length > 0) {
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append("pdfFiles[]", fileInput.files[i]);
                }
            } else {
                alert("Please select at least one PDF file.");
                $('#uploadPdfSubmit').prop('disabled', false);
                return;
            }

            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: pdfurl,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#uploadPdfSubmit').prop('disabled', false);
                    console.log("Success:", response);
                    $('.errmsg').html('<p style="color:green;">' + response.message + '</p>');
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    $('#uploadPdfSubmit').prop('disabled', false);
                    console.log("Error response:", xhr.responseText);
                    let errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                    if (errors) {
                        $('.errmsg').html('<p style="color:red;">' + Object.values(errors).join('<br>') + '</p>');
                    } else {
                        $('.errmsg').html('<p style="color:red;">An error occurred. Check the console.</p>');
                    }
                }
            });
        });

         $('#addToProcess').on('click', function(e) {
            e.preventDefault();
            
            // Close modal first
            forceCloseModal('fullWidthModal');
            
            // 1. Show the main page loader
            $("#loading").show();
            
            // Disable button to prevent multiple clicks
            var $btn = $(this);
            $btn.prop('disabled', true).text('Processing...');

            let selectedBarcodes = [];
            
            // NEW: Correct way to get all data from a Server-Side (Yajra) DataTable
            if (typeof readableTable !== 'undefined') {
                readableTable.rows().every(function() {
                    let rowData = this.data();
                    if (rowData.barcode) {
                        selectedBarcodes.push(rowData.barcode);
                    }
                });
            } else {
                // Fallback just in case table wasn't initialized
                $('#readableBarcodeTable tbody tr').each(function() {
                    let barcode = $(this).find('td:nth-child(3)').text().trim();
                    if (barcode) {
                        selectedBarcodes.push(barcode);
                    }
                });
            }

            if (selectedBarcodes.length === 0) {
                alert("No barcodes selected to process.");
                $("#loading").hide();
                $btn.prop('disabled', false).text('Add to process');
                return;
            }

            $.ajax({
                url: "{{ URL::to('/admin/add-to-process') }}",
                type: "POST",
                data: {
                    barcodes: selectedBarcodes,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log("Success:", response);
                    
                    let isFirstRowEmpty = ($('#donorid').val() === '' && $('#donor_acc_num').val() === '');
                    
                    response.orderDetails.forEach(function(orderDetail, index) {
                        
                        let amountStyle = orderDetail.amount < 1 
                            ? 'min-width:30px; border: 2px solid red;' 
                            : 'min-width:30px;';
                        
                        let statusStyle = orderDetail.barcodeStatus === 'Will be pending' 
                            ? 'color: red; font-weight: bold;' 
                            : 'color: green; font-weight: bold;';

                        if (index === 0 && isFirstRowEmpty) {
                            $('#donorid').val(orderDetail.user_id);
                            $('#donor_acc_num').val(orderDetail.user.accountno);
                            $('#donor_acc_name').val(orderDetail.user.name);
                            $('#donor_check').val(orderDetail.barcode);
                            $('#d_amnt').val(orderDetail.amount);
                            $('#d_amnt').attr('style', amountStyle);
                            $('#barcode_status').html('<span style="' + statusStyle + '">' + orderDetail.barcodeStatus + '</span>');
                        } else {
                            var markup = `
                                <tr class="item-row" style="position:relative;">
                                    <td width="200px" style="display:inline-flex;">
                                        <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 81px;" onclick="removeRow(event)">X</div>
                                    </td>
                                    <td width="200px">
                                        <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="${orderDetail.user.accountno}" placeholder="Type Acc no...">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width:100px" type="text" name="donor_name[]" readonly class="form-control donorAcc" value="${orderDetail.user.name}">
                                        <input type="hidden" name="donor[]" value="${orderDetail.user_id}" class="donorid">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width:100px" name="check[]" type="text" value="${orderDetail.barcode}" class="form-control check">
                                    </td>
                                    <td width="20px">
                                        <input name="amount[]" type="text" value="${orderDetail.amount}" class="amount form-control" style="${amountStyle}">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width:200px" name="note[]" type="text" class="form-control note">
                                    </td>
                                    <td width="150px">
                                        <select name="waiting[]" class="form-control">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </td>
                                    <td width="150px">
                                        <select name="expired[]" class="form-control">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </td>
                                    <td width="150px">
                                        <span style="${statusStyle}">${orderDetail.barcodeStatus}</span>
                                    </td>
                                </tr>`;
                            $("table #inner").append(markup);
                        }
                    });

                    updateRowCount();
                    net_total();

                    if (response.data2) {
                        $("#notReadableBook").removeClass("d-none");
                        $("#inner2").html(response.data2);
                    }
                    
                    // Reload the Yajra table to remove processed items
                    if (typeof readableTable !== 'undefined') {
                        readableTable.ajax.reload(null, false);
                    }
                    
                },
                complete: function() {
                    $("#loading").hide();
                    $btn.prop('disabled', false).text('Add to process');
                },
                error: function(xhr) {
                    console.log("Error response:", xhr.responseText);
                    alert("An error occurred while processing the barcodes.");
                    $("#loading").hide();
                    $btn.prop('disabled', false).text('Add to process');
                    forceCloseModal('fullWidthModal');
                }
            });
        });

        // Brute-force modal close to bypass Bootstrap 4/5 conflicts
        function forceCloseModal(modalId) {
            var $modal = $('#' + modalId);
            
            // 1. Hide the modal box immediately
            $modal.removeClass('show').css('display', 'none');
            $modal.attr('aria-hidden', 'true');
            
            // 2. Remove the gray background overlay
            $('.modal-backdrop').remove();
            
            // 3. Unlock the page scrolling
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('body').css('overflow', '');
        }

        $('#deleteProcess').on('click', function(e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to delete the selected processes?")) {
                return;
            }

            $.ajax({
                url: "{{ URL::to('/admin/delete-process-voucher-list') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log("Success:", response);
                    alert("Selected processes have been deleted successfully.");
                    location.reload();
                },
                error: function(xhr) {
                    console.log("Error response:", xhr.responseText);
                    alert("An error occurred while deleting the processes.");
                }
            });
        });

        $('#deleteProcessImage').on('click', function(e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to delete images?")) {
                return;
            }

            $.ajax({
                url: "{{ URL::to('/admin/delete-process-voucher-image-list') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log("Success:", response);
                    alert("Selected processes have been deleted successfully.");
                    location.reload();
                },
                error: function(xhr) {
                    console.log("Error response:", xhr.responseText);
                    alert("An error occurred while deleting the processes.");
                }
            });
        });



        // 1. Initialize Yajra Datatable ONLY when the modal opens
    $('#fullWidthModal').on('shown.bs.modal', function () {
        // Check if table is already initialized to prevent errors
        if ( ! $.fn.DataTable.isDataTable( '#readableBarcodeTable' ) ) {
            readableTable = $('#readableBarcodeTable').DataTable({
                processing: true,
                serverSide: true,
                // Removed responsive: true because the extension JS is missing
                autoWidth: false, 
                order: [[0, 'desc']],
                ajax: {
                    url: "{{ route('get.readable.barcodes') }}",
                    type: "GET"
                },
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'image', name: 'file', orderable: false, searchable: false },
                    { data: 'barcode', name: 'barcode' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                // Fixed: Use setTimeout + columns.adjust() to fix modal width without responsive plugin
                "fnDrawCallback": function() {
                    setTimeout(function() {
                        readableTable.columns.adjust();
                    }, 100);
                }
            });
        } else {
            // If already initialized, just recalculate widths
            setTimeout(function() {
                readableTable.columns.adjust();
            }, 100);
        }
    });

    // 2. Fetch "Not Readable" Images ONLY when Tab 2 is clicked
    var imagesLoaded = false;
    $('#tab2-tab').on('shown.bs.tab', function () {
        if (!imagesLoaded) {
            $.get("{{ route('get.not.readable.barcodes') }}", function(response) {
                $('#imageGalleryRow').html(response.html);
                imagesLoaded = true;
            });
        }
    });

    // 3. Updated Delete Button JS
    $('#readableBarcodeTable').on('click', '.delete-single-barcode', function(e) {
        e.preventDefault();
        var rid = $(this).data('id');
        
        if (!confirm("Are you sure you want to delete this barcode?")) {
            return;
        }
        
        $.ajax({
            url: "{{ URL::to('/admin/delete-processed-single-barcode') }}",
            type: "POST",
            data: {
                id: rid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert("Barcode deleted successfully.");
                readableTable.ajax.reload(null, false); // Reload table data
            },
            error: function(xhr) {
                alert("An error occurred while deleting the barcode.");
            }
        });
    });


            // Brute-force modal OPEN to bypass Bootstrap 4/5 conflicts
        $('#openVoucherModal').on('click', function(e) {
            e.preventDefault();
            var $modal = $('#fullWidthModal');
            
            // 1. Destroy any leftover backdrops from previous glitchy opens
            $('.modal-backdrop').remove();
            
            // 2. Setup body for modal (lock scroll, add padding for scrollbar)
            $('body').addClass('modal-open');
            var scrollWidth = window.innerWidth - $(window).width();
            $('body').css('padding-right', scrollWidth + 'px');
            
            // 3. Force show the modal with absolute highest z-index
            $modal.css('display', 'block');
            $modal.addClass('show');
            $modal.attr('aria-hidden', 'false');
            $modal.css('z-index', '99999');
            
            // 4. Manually create exactly ONE backdrop with a lower z-index
            $('body').append('<div class="modal-backdrop fade show" style="z-index: 99998;"></div>');
            
            // 5. Trigger the event so our Yajra Datatable knows it's open and initializes
            $modal.trigger('shown.bs.modal');
        });

        // Brute-force modal CLOSE
        $('#closeVoucherModal').on('click', function(e) {
            e.preventDefault();
            forceCloseModal('fullWidthModal');
        });

        function forceCloseModal(modalId) {
            var $modal = $('#' + modalId);
            
            // 1. Hide the modal box immediately
            $modal.removeClass('show').css('display', 'none');
            $modal.attr('aria-hidden', 'true');
            
            // 2. Remove the gray background overlay
            $('.modal-backdrop').remove();
            
            // 3. Unlock the page scrolling
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('body').css('overflow', '');
        }


});
</script>
@endsection