@extends('layouts.admin')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <div class="dashboard-content" id="focusBcode">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                <div class="mx-2">Process Voucher</div>
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
                                            <select name="charity" id="charity_list" class="form-control">
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
                                            <select name="charity" id="charity_list" style="min-width: 100px;" class="form-control">
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
                                                            <form action="#"  enctype="multipart/form-data" method="POST">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <label for="pdfFile">Choose file</label>
                                                                    <input type="file" class="form-control-file" id="pdfFile" name="pdfFile" accept="application/pdf" required>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
    $('#charity_list').select2({
      width: '250px',
      placeholder: "Select charity",
      allowClear: true
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
                        return;
                    }
                    // check duplicate barcode 

                    $.ajax({
                    url: urlbr,
                    method: "POST",
                    data: {barcode:barcode},

                    success: function (d) {
                        console.log(d);
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

            console.log('test');

            let formData = new FormData();

            var fileInput = document.querySelector('#pdfFile');
            if (fileInput.files.length > 0) {
                formData.append("pdfFile", fileInput.files[0]);
            } else {
                alert("Please select a PDF file.");
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
                    console.log("Success:", response);
                    $('#message').html('<p style="color:green;">' + response.message + '</p>');
                },
                error: function (xhr) {
                    console.log("Error response:", xhr.responseText);
                    let errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                    if (errors) {
                        $('#message').html('<p style="color:red;">' + Object.values(errors).join('<br>') + '</p>');
                    } else {
                        $('#message').html('<p style="color:red;">An error occurred. Check the console.</p>');
                    }
                }
            });
        });
    });
</script>
@endsection
