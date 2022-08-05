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
                                    </tr>
                                </thead>
                                <tbody id="inner">
                                    <tr class="item-row">
                                        <td width="230px">
                                            <select name="charity" id="charity_list" class="form-control">
                                                <option value>Select</option>
                                                @foreach ($charities as $charity)
                                                    <option value="{{ $charity->id }}">{{ $charity->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150px">
                                            <input class="form-control donor" name="donor_acc[]"  placeholder="Type Acc no...">
                                        </td>
                                        <td width="200px">
                                            <input style="min-width: 50px;"  type="text" readonly class="form-control donorAcc" value>
                                            <input type="hidden" name="donor[]"  class="donorid" value>
                                        </td>
                                        <td width="100px">
                                            <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value>
                                        </td>

                                        <td width="40px">
                                            <input style="min-width: 30px;" name="amount[]" type="text" class="amount form-control" value>
                                        </td>
                                        <td width="250px">
                                            <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value>
                                        </td>
                                    </tr>
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
                                    <td  colspan="5"></td>
                                    <td>
                                        <div class="col-md-12 my-2">
                                            <button class="text-white btn-theme ml-1 mb-4" id="addvoucher" type="button">Process Voucher</button>
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
      width: '200px',
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
                    '<tr class="item-row" style="position:realative;"><td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="150px"><input class="form-control donor" name="donor_acc[]" placeholder="Type Acc no..."></td><td width="200px"><input style="min-width:50px" type="text" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" class="donorid" value></td><td width="100px"><input style="min-width:100px" name="check[]" type="text" class="check form-control" value></td> <td width="40px"><input style="min-width:30px" name="amount[]" type="text" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td></tr>';
                $("table #inner ").append(markup);
            });

        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

        var url = "{{URL::to('/admin/pvoucher-store')}}";

        // $("#addvoucher").click(function(){
            
            $("body").delegate("#addvoucher","click",function(event){
                event.preventDefault();

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

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {charityId,donorIds,donorAccs,chqNos,amts,notes},

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
            if (!clicked.is(".donor") && !clicked.is(".check") && !clicked.is(".amount") && !clicked.is(".note") && !clicked.is("span#select2-charity_list-container.select2-selection__rendered")) {
                $("#barcode").focus();
            }
        })

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
                    $.ajax({
                    url: urlbr,
                    method: "POST",
                    data: {barcode:barcode},

                    success: function (d) {
                        console.log(d);
                        if (d.status == 303) {

                        }else if(d.status == 300){

                            var markup =
                            '<tr class="item-row" style="position:realative;"><td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="150px"><input class="form-control donor" name="donor_acc[]" value="'+d.donoracc+'" placeholder="Type Acc no..."></td><td width="200px"><input style="min-width:50px" type="text" value="'+d.donorname+'" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" value="'+d.donorid+'"  class="donorid" value></td><td width="100px"><input style="min-width:100px" name="check[]" type="text" value="'+barcode+'" class="form-control check" ></td> <td width="40px"><input style="min-width:30px" name="amount[]" type="text" value="'+d.amount+'" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td></tr>';
                        $("table #inner ").append(markup);

                        $("#barcode").val("");
                        net_total();
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

                });

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
@endsection
