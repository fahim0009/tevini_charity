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
</style>
 @php
     $readableBarcode = \App\Models\ProcessedBarcode::where('barcode', '!=', 'Not Found')->get();
     $notReadableBarcode = \App\Models\ProcessedBarcode::where('barcode', '=', 'Not Found')->get();
 @endphp


    <div class="dashboard-content" id="focusBcode">
        <section class="profile purchase-status">
            <div class="title-section d-flex justify-content-between align-items-center">
                <div>
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Process Voucher
                </div>
                <div class="ml-auto">
                        <button class="iconify" data-icon="mdi:book" data-inline="false" data-toggle="modal" data-target="#fullWidthModal" style="cursor: pointer;"></button>

                        <!-- Modal -->
                        <div class="modal fade" id="fullWidthModal" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="fullWidthModalLabel">Voucher</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Add your content here -->
                                        
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($readableBarcode as $readblebarcode)
                                                        <tr>
                                                            <td>{{$readblebarcode->created_at}}</td>
                                                            <td>
                                                                <a href="{{ asset('storage/barcodeimages/'.$readblebarcode->file) }}" target="_blank">
                                                                    <img src="{{ asset('storage/barcodeimages/'.$readblebarcode->file) }}" alt="barcode" style="width: 200px; height: 100px;">
                                                                </a>
                                                            </td>
                                                            <td>{{$readblebarcode->barcode}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>


                                            </div>
                                            <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                                
                                                <div class="d-flex justify-content-end p-2">
                                                    <button class="btn btn-warning" id="deleteProcessImage">Delete Voucher Image</button>
                                                </div>
                                                
                                                <div class="image-gallery">
                                                    <div class="row">
                                                        @foreach ($notReadableBarcode as $notReadable)
                                                        <div class="col-md-3 mb-3">
                                                            <a href="{{ asset('storage/barcodeimages/'.$notReadable->file) }}" target="_blank">
                                                                <img src="{{ asset('storage/barcodeimages/'.$notReadable->file) }}" alt="barcode" class="img-thumbnail">
                                                            </a>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>


                                            </div>
                                        </div>




                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="ermsg"></div>
        </section>
    <!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
   </div>
    <!-- Image loader -->
        <section class="">
            <div class="row  my-3 mx-0 ">
                <div class=" col-md-12 bg-white px-4">
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
                                            <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="{{$voucher->donor_acc}}"  placeholder="Type Acc no...">
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;"  type="text" name="donor_name[]" readonly class="form-control donorAcc" value="{{$voucher->donor_name}}">
                                            <input type="hidden" name="donor[]"  class="donorid" value="{{$voucher->donor_id}}">
                                        </td>
                                        <td width="100px">
                                            <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value="{{$voucher->voucher_number}}">
                                        </td>

                                        <td width="40px">
                                            <input style="min-width: 30px;" name="amount[]" type="text" class="amount form-control" value="{{$voucher->amount}}">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value="{{$voucher->note}}">
                                        </td>                
                                        <td width="150px">
                                            <select name="waiting[]" class="form-control">
                                                <option value="No"  @if(isset($voucher->waiting) && $voucher->waiting == "No") selected @endif>No</option>
                                                <option value="Yes" @if(isset($voucher->waiting) && $voucher->waiting == "Yes") selected @endif>Yes</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @else
                                    <tr class="item-row" style="position:realative;">
                                        <td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td>
                                        <td width="150px">
                                            <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="{{$voucher->donor_acc}}"  placeholder="Type Acc no...">
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;"  type="text" name="donor_name[]" readonly class="form-control donorAcc" value="{{$voucher->donor_name}}">
                                            <input type="hidden" name="donor[]"  class="donorid" value="{{$voucher->donor_id}}">
                                        </td>
                                        <td width="100px">
                                            <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value="{{$voucher->voucher_number}}">
                                        </td>

                                        <td width="40px">
                                            <input style="min-width: 30px;" name="amount[]" type="text" class="amount form-control" value="{{$voucher->amount}}">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value="{{$voucher->note}}">
                                        </td>
                                        <td width="150px">
                                            <select name="waiting[]" class="form-control">
                                                <option value="No"  @if(isset($voucher->waiting) && $voucher->waiting == "No") selected @endif>No</option>
                                                <option value="Yes" @if(isset($voucher->waiting) && $voucher->waiting == "Yes") selected @endif>Yes</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endif
                                    @empty
                                    <tr class="item-row " id="firstRow">
                                        <td width="230px">
                                            <select name="charity" id="charity_list" style="min-width: 100px;" class="form-control charitylist">
                                            <option value>Select</option>
                                                @foreach ($charities as $charity)
                                                    <option value="{{ $charity->id }}">{{ $charity->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 100px;" class="form-control donor" type="number" name="donor_acc[]" id="donor_acc_num"  placeholder="Type Acc no...">
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 100px;"  type="text" name="donor_name[]" readonly class="form-control donorAcc" id="donor_acc_name" value>
                                            <input type="hidden" name="donor[]" id="donorid"  class="donorid" value>
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 100px;" name="check[]" id="donor_check" type="text" class="form-control check" value>
                                        </td>

                                        <td width="250px">
                                            <input style="min-width: 100px;" name="amount[]" type="text" class="form-control amount" id="d_amnt" value>
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" id="d_note" value>
                                        </td>
                                        <td width="150px">
                                            <select name="waiting[]" class="form-control" id="d_waiting">
                                                <option value="No">No</option>
                                                <option value="Yes">Yes</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforelse

                                </tbody>
                                <tr>
                                    <td colspan="3">
                                        <span type="submit" class="text-white btn-theme add-row"> + Add
                                        </span>
                                    </td>
                                    <td width="40px">
                                        <span>Total</span>
                                    </td>
                                    <td width="250px">
                                        <input style="min-width: 200px;" id="total" readonly  type="text" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td  colspan="4"></td>
                                    <td colspan="2">
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
                                                            <div id='pdfloader' style='display:none ;'>
                                                                <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
                                                            </div>

                                                            <div class="errmsg"></div>
                                                            <form action="#"  enctype="multipart/form-data" method="POST">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <label for="pdfFile">Choose file</label>
                                                                    <input type="file" class="form-control-file" id="pdfFile" name="pdfFile[]" accept="application/pdf" multiple required>
                                                                    {{-- <input type="file" name="barcode_image[]" multiple  accept="image/*" required> --}}
                                                                </div>
                                                                
                                                                {{-- <button type="submit" class="btn btn-primary">Upload</button> --}}
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
                                <div class="row" style="opacity:0">
                                    <div class="col-md-1">
                                        <input style="min-width: 200px;" id="barcode"  type="text" class="form-control">
                                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
    $('.charitylist').select2();
  </script>

<script>
    $(document).ready(function() {
        $('#readableBarcodeTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[0, "desc"]], // Order by date descending
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    function removeRow(event) {
            event.target.parentElement.parentElement.remove();
    }
</script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".add-row").click(function() {
                var markup =
                    '<tr class="item-row" style="position:realative;"><td width = "200px" style="display:inline-flex;"><div style="color: white; user-select:none;  padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 81px;" onclick="removeRow(event)" >X</div></td><td width="200px"><input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" placeholder="Type Acc no..."></td><td width="250px"><input style="min-width:100px" type="text" name="donor_name[]" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" class="donorid" value></td><td width="250px"><input style="min-width:100px" name="check[]" type="text" class="check form-control" value></td> <td width="20px"><input style="min-width:30px" name="amount[]" type="text" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td> <td width="150px"><select name="waiting[]" class="form-control"><option value="No">No</option><option value="Yes">Yes</option></select></td></tr>';
                $("table #inner ").append(markup);
            });

        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

        var url = "{{URL::to('/admin/pvoucher-store')}}";


        $("body").delegate("#addvoucher","click",function(event){
            event.preventDefault();

            $("#loading").show();    

            var charityId = $("select[name='charity']").val();

            var donorIds = $("input[name='donor[]']")
              .map(function(){return $(this).val();}).get();

            var donorAccs = $("input[name='donor_acc[]']")
              .map(function(){return $(this).val();}).get();

            var chqNos = $("input[name='check[]']")
              .map(function(){return $(this).val();}).get();

            var amts = $("input[name='amount[]']")
              .map(function(){return $(this).val();}).get();

            var notes = $("input[name='note[]']")
              .map(function(){return $(this).val();}).get();

            var waitings = $("select[name='waiting[]']")
              .map(function(){return $(this).val();}).get();  

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {charityId,donorIds,donorAccs,chqNos,amts,notes,waitings},

                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){window.location.href="https://www.tevini.co.uk/admin/process-voucher/"+d.batch_id},2000)
                        }
                    },
                    complete:function(d){
                        $("#loading").hide();
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

        });

// voucher draft
var urld = "{{URL::to('/admin/pvoucher-draft')}}";

    $("body").delegate("#Draftvoucher","click",function(event){
        event.preventDefault();

    $("#loading").show();    

    var charityId = $("select[name='charity']").val();

    var donorIds = $("input[name='donor[]']")
      .map(function(){return $(this).val();}).get();

    var donorNms = $("input[name='donor_name[]']")
      .map(function(){return $(this).val();}).get();

    var donorAccs = $("input[name='donor_acc[]']")
      .map(function(){return $(this).val();}).get();

    var chqNos = $("input[name='check[]']")
      .map(function(){return $(this).val();}).get();

    var amts = $("input[name='amount[]']")
      .map(function(){return $(this).val();}).get();

    var notes = $("input[name='note[]']")
      .map(function(){return $(this).val();}).get();

    var waitings = $("select[name='waiting[]']")
        .map(function(){return $(this).val();}).get();    

        $.ajax({
            url: urld,
            method: "POST",
            data: {charityId,donorIds,donorNms,donorAccs,chqNos,amts,notes,waitings},

            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    pagetop();
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    pagetop();
                }
            },
            complete:function(d){
                        $("#loading").hide();
                    },
            error: function (d) {
                console.log(d);
            }
        });

});


        var urlf = "{{URL::to('/admin/find-name')}}";
        $("body").delegate(".donor","keyup",function(event){
		            event.preventDefault();
                    var donoracc = $(this).val();
                    var row = $(this).parents('.item-row');

                    $.ajax({
                    url: urlf,
                    method: "POST",
                    data: {accno:donoracc},

                    success: function (d) {
                        if (d.status == 303) {
                        }else if(d.status == 300){
                        row.find('.donorAcc').val(d.donorname);
                        row.find('.donorid').val(d.donorid);
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

                });

                
        // foucs when click anywhere
        $(document).on("mousedown", function(e) {
            clicked = $(e.target);
        })

        $("body").on("blur","input", function()
        {
            if (!clicked.is(".donor") && !clicked.is(".check") && !clicked.is(".amount") && !clicked.is(".note") && !clicked.is(".waiting") && !clicked.is("span#select2-charity_list-container.select2-selection__rendered")) {
                $("#barcode").focus();
            }
        });

        $('#charity_list').on("select2:selecting", function(e) {
            $("#barcode").focus();
        });

        //focus onload
        $("#barcode").focus();
        // get barcode data
        var urlbr = "{{URL::to('/admin/barcode')}}";
            $("#barcode").change(function(){
		            event.preventDefault();
                    var barcode = $(this).val();

                    // check duplicate barcode 
                    var check = $("input[name='check[]']")
                        .map(function(){return $(this).val();}).get();
                        
                    check.push(barcode);
                    seen = check.filter((s => v => s.has(v) || !s.add(v))(new Set));

                    if (Array.isArray(seen) && seen.length) {
                        $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This voucher number has already been scanned.</b></div>");
                        setTimeout(function() {
                            $(".ermsg").html("");
                        }, 3000);
                        $("#barcode").val("");
                        return;
                    }
                    // check duplicate barcode 
                    $.ajax({
                    url: urlbr,
                    method: "POST",
                    data: {barcode:barcode},

                    success: function (d) {
                        // console.log(d);
                        if (d.status == 303) {

                        }else if(d.status == 300){

                            // new code update
                            if ($('#donorid').val() === '') {
                                $('#donorid').val(d.donorid); // Set default value for empty donorid field
                                $('#donor_acc_num').val(d.donoracc);
                                $('#donor_acc_name').val(d.donorname);
                                $('#donor_check').val(barcode);
                                $('#d_amnt').val(d.amount);
                            
                            }else{
                                    var markup =
                                '<tr class="item-row" style="position:realative;"><td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="200px"><input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="'+d.donoracc+'" placeholder="Type Acc no..."></td><td width="250px"><input style="min-width:100px" type="text" value="'+d.donorname+'" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" value="'+d.donorid+'"  class="donorid" value></td><td width="250px"><input style="min-width:100px" name="check[]" type="text" value="'+barcode+'" class="form-control check" ></td> <td width="20px"><input style="min-width:30px" name="amount[]" type="text" value="'+d.amount+'" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td><td width="150px"><select name="waiting[]" class="form-control"><option value="No">No</option><option value="Yes">Yes</option></select></td></tr>';
                            $("table #inner ").append(markup);
                            }
                            // new code update





                            

                        $("#barcode").val("");
                        net_total();
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

                });

                net_total();

                $("body").delegate(".amount","keyup",function(event){

                    net_total();
                });

                function net_total(){
                    var total = 0;
                    $('.amount').each(function(){
                        total += ($(this).val()-0);
                    })
                    $('#total').val(total.toFixed(2));
                }


        });
    </script>



<script>
    $(document).ready(function () {
        
        var pdfurl = "{{URL::to('/admin/pdf-to-text')}}";

        $('#uploadPdfSubmit').on('click', function (e) {
            e.preventDefault();
            // $("#pdfloader").show();

            $('#uploadPdfSubmit').prop('disabled', true); // Disable the button
            $('#uploadPdfSubmit').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...'); // Add spinner

            let formData = new FormData();
            let fileInput = document.querySelector('#pdfFile');

            if (fileInput.files.length > 0) {
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append("pdfFiles[]", fileInput.files[i]); // Append multiple files
                }
            } else {
                alert("Please select at least one PDF file.");
                $("#pdfloader").hide();
                $('#uploadPdfSubmit').prop('disabled', false); // Enable the button
                return;
            }

            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: pdfurl,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {

                    $("#pdfloader").hide();
                $('#uploadPdfSubmit').prop('disabled', false); // Enable the button
                    console.log("Success:", response);
                    $('.errmsg').html('<p style="color:green;">' + response.message + '</p>');
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                },
                error: function (xhr) {
                    $("#pdfloader").hide();
                    $('#uploadPdfSubmit').prop('disabled', false); // Enable the button
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




        $('#addToProcess').on('click', function (e) {
            e.preventDefault();

            let selectedBarcodes = [];
            $('#readableBarcodeTable tbody tr').each(function () {
            let barcode = $(this).find('td:nth-child(3)').text().trim();
            if (barcode) {
                selectedBarcodes.push(barcode);
            }
            });

            if (selectedBarcodes.length === 0) {
            alert("No barcodes selected to process.");
            return;
            }

            $.ajax({
            url: "{{URL::to('/admin/add-to-process')}}",
            type: "POST",
            data: {
                barcodes: selectedBarcodes,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log("Success:", response);

                response.orderDetails.forEach(function (orderDetail) {

                    if ($('#donorid').val() === '') {

                            $('#donorid').val(orderDetail.user_id); // Set default value for empty donorid field
                            $('#donor_acc_num').val(orderDetail.user.accountno);
                            $('#donor_acc_name').val(orderDetail.user.name);
                            $('#donor_check').val(orderDetail.barcode);
                            $('#d_amnt').val(orderDetail.amount);
                        
                        }else{
                    
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
                                    <input style="min-width:30px" name="amount[]" type="text" value="${orderDetail.amount}" class="amount form-control">
                                </td>
                                <td width="250px">
                                    <input style="min-width:200px" name="note[]" type="text" value="" class="form-control note">
                                </td>
                                <td width="150px"><select name="waiting[]" class="form-control"><option value="No">No</option><option value="Yes">Yes</option></select></td>
                            </tr>`;
                        $("table #inner").append(markup);
                    }
                });
                
                $("table #inner ").append(response.data2);
                
                setTimeout(function () {
                    $('#fullWidthModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }, 500);

            },
            error: function (xhr) {
                console.log("Error response:", xhr.responseText);
                alert("An error occurred while processing the barcodes.");
            }
            });
        });

        $('#deleteProcess').on('click', function (e) {
            e.preventDefault();

            if (!confirm("Are you sure you want to delete the selected processes?")) {
            return;
            }

            $.ajax({
            url: "{{URL::to('/admin/delete-process-voucher-list')}}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log("Success:", response);
                alert("Selected processes have been deleted successfully.");
                location.reload();
            },
            error: function (xhr) {
                console.log("Error response:", xhr.responseText);
                alert("An error occurred while deleting the processes.");
            }
            });
        });

        $('#deleteProcessImage').on('click', function (e) {
            e.preventDefault();

            if (!confirm("Are you sure you want to delete images?")) {
            return;
            }

            $.ajax({
            url: "{{URL::to('/admin/delete-process-voucher-image-list')}}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log("Success:", response);
                alert("Selected processes have been deleted successfully.");
                location.reload();
            },
            error: function (xhr) {
                console.log("Error response:", xhr.responseText);
                alert("An error occurred while deleting the processes.");
            }
            });
        });
    });
</script>
@endsection
