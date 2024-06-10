@extends('frontend.layouts.charity')
@section('content')
<style>
    .dashboard-wraper .rightbar .content .amount{
        font-size: 20px !important; 
    }
</style>
<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Process Voucher
            </div>
            <div class="ermsg"></div>
        </div>
    </div>
    <!-- Image loader -->
    <div id='loading' style='display:none ;'>
        <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
    </div>

    <section class="">
        <div class="row  my-3 mx-0 ">
            <div class=" col-md-12 px-4">
                <div class="form-container">
                    <div class="overflow mx-auto">
                        <table class="table shadow-sm">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Donor</th>
                                    <th>Donor Name</th>
                                    <th>Check No</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody id="inner">
                                
                                
                            </tbody>
                            <tr>
                                <td colspan="2">
                                    {{-- <span type="submit" class="text-white btn-theme add-row"> + Add
                                    </span> --}}
                                </td>
                                <td width="40px">
                                    <span>Total</span>
                                </td>
                                <td width="250px">
                                    <input style="min-width: 200px;" id="total" readonly  type="text" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td  colspan="3"></td>
                                <td colspan="1">
                                    <div class="row">
                                        <div class="col-md-6 my-2">
                                            <button class="text-white btn-theme ml-1 mb-4" id="addvoucher" type="button">Process Voucher</button>
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

<script type="text/javascript">
    $(document).ready(function() {
        $("#usercontact").addClass('active');
    });
</script>
<script type="text/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
    function net_total(){
            var total = 0;
            $('.amount').each(function(){
                total += ($(this).val()-0);
            })
            $('#total').val(total.toFixed(2));
        }

    function removeRow(event) {
            event.target.parentElement.parentElement.remove();
                            net_total();
    }
</script>
<script>
    $(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        var url = "{{URL::to('/charity/pvoucher-store')}}";


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
                data: {donorIds,donorAccs,chqNos,amts,notes,waitings},

                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                        pagetop();
                    }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                        pagetop();
                        window.setTimeout(function(){window.location.href="https://www.tevini.co.uk/charity/process-voucher/"+d.batch_id},2000)
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
        var urlbr = "{{URL::to('/barcode')}}";

                $("#barcode").change(function(){
		            event.preventDefault();
                    var barcode = $(this).val();

                    
                    // check duplicate barcode 
                    var check = $("input[name='check[]']")
                        .map(function(){return $(this).val();}).get();
                        
                    check.push(barcode);
                    seen = check.filter((s => v => s.has(v) || !s.add(v))(new Set));

                    if (Array.isArray(seen) && seen.length) {
                        $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This check number has already added.</b></div>");
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
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                var markup =
                                '<tr class="item-row" style="position:realative;"><td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="200px"><input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="'+d.donoracc+'" placeholder="Type Acc no..." readonly></td><td width="250px"><input style="min-width:100px" type="text" value="'+d.donorname+'" readonly class="form-control donorAcc" ><input type="hidden" readonly name="donor[]" value="'+d.donorid+'"  class="donorid" ></td><td width="250px"><input style="min-width:100px" name="check[]" type="text" value="'+barcode+'" class="form-control check"  readonly></td> <td width="20px"><input style="min-width:30px; front-size:13px" name="amount[]" type="text" value="'+d.amount+'" class="amount form-control" readonly ></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" readonly value><input name="waiting[]" type="hidden" class="form-control" readonly value="No"></td></tr>';
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

@endsection