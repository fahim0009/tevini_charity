@extends('frontend.layouts.charity')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Process Voucher
            </div>
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
                                    <td width="200px">
                                        <input style="min-width: 100px;" class="form-control donor" type="number" name="donor_acc[]"  placeholder="Type Acc no...">
                                    </td>
                                    <td width="250px">
                                        <input style="min-width: 100px;"  type="text" name="donor_name[]" readonly class="form-control donorAcc" value>
                                        <input type="hidden" name="donor[]"  class="donorid" value>
                                    </td>
                                    <td width="250px">
                                        <input style="min-width: 100px;" name="check[]" type="text" class="form-control check" value>
                                    </td>

                                    <td width="250px">
                                        <input style="min-width: 100px;" name="amount[]" type="text" class="form-control amount" value>
                                    </td>
                                    <td width="250px">
                                        <input style="min-width: 200px;" name="note[]" type="text" class="form-control note" value>
                                    </td>
                                    <td width="150px">
                                        <select name="waiting[]" class="form-control">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforelse

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
                                '<tr class="item-row" style="position:realative;"><td width = "200px" style="display:inline-flex;"><div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 81px;" onclick="removeRow(event)" >X</div></td><td width="200px"><input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="'+d.donoracc+'" placeholder="Type Acc no..."></td><td width="250px"><input style="min-width:100px" type="text" value="'+d.donorname+'" readonly class="form-control donorAcc" value><input type="hidden" name="donor[]" value="'+d.donorid+'"  class="donorid" value></td><td width="250px"><input style="min-width:100px" name="check[]" type="text" value="'+barcode+'" class="form-control check" ></td> <td width="20px"><input style="min-width:30px" name="amount[]" type="text" value="'+d.amount+'" class="amount form-control" value></td><td width="250px"><input style="min-width:200px" name="note[]" type="text" class="form-control note" value></td><td width="150px"><select name="waiting[]" class="form-control"><option value="No">No</option><option value="Yes">Yes</option></select></td></tr>';
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