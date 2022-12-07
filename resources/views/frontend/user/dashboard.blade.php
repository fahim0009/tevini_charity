@extends('frontend.layouts.user')

@section('content')


<div class="dashboard-content py-2 px-4">

    <div class="row my-4">
        <div class="col-md-12 text-center ">
            <h4 class="text-capitalize bg-info text-white p-3 border-left d-inline-block mx-auto rounded">
                welcome to mr ‘{{auth()->user()->name}}’
            </h4>
            <h5> <span> Gift Aid in current year : £{{ $currentyramount }}</span></h5>
            <h5><span>  Gift Aid in previous year : £{{ $totalamount }}</span></h5>
        </div>
    </div>

    {{-- <div>
        <div class="col-md-12 text-center ">
            <p class="bg-info text-white p-3 border-left d-inline-block mx-auto rounded">
                Your overdrawn limit is £{{auth()->user()->overdrawn_amount}}
            </p>
            @php
                $checkadmin = \App\Models\OverdrawnRecord::where('user_id','=', Auth::user()->id)->where('created_by','=','Admin')->count();
            @endphp
            @if ($checkadmin > 0 )

            @else
                <!--<button type="button" class="btn btn-info overdrawn" overdrawn-id="{{Auth::user()->id}}" class="overdrawn" data-bs-toggle="modal" data-bs-target="#exampleModal">-->
                <!--    Edit-->
                <!--</button>-->
            @endif
        </div>
    </div> --}}


     @php
     $dcal = \App\Models\DonationCalculator::where('donor_id','=', Auth::user()->id)->first();
     $dcaldetails = \App\Models\DonationDetail::where('donor_id','=', Auth::user()->id)->get();
        $ptotal = \App\Models\Provoucher::where([
        ['user_id','=', Auth::user()->id],
        ['status', '=', '0']
        ])->sum('amount');
    @endphp
    @if($ptotal)
    <div class="alert alert-danger" role="alert">
        <p>Your pending voucher balance is : £{{$ptotal}}</p>
    </div>
    @endif

    @if(auth()->user()->status == "0")
    <div class="alert alert-danger" role="alert">
        You are not active user. Now you have limited access. Please wait till confirmation. -- Thanks.
    </div>
    @endif

    <fieldset >
        <legend>TO TRANSFER FUNDS, EITHER:</legend>
        <div class="row">
            <div class="col-md-5">
                <div class="transferFunds shadow-sm">
                    <div class="pointer">
                        1
                    </div>
                    <div class="para pl-2">
                        Send a cheque made payable to <br> 'Tevini Ltd,
                        5A Holmdale Terrace, London, N156PP
                    </div>
                </div>

                <div class="transferFunds shadow-sm mt-2">
                    <div class="pointer">
                        3
                    </div>
                    <div class="para pl-2">
                     Top-Up using your card <br>
                     <a href="{{ route('stripeDonation') }}"> Click here</a>
                    </div>
                </div>
            </div>
            <div class="col-md-1 d-flex justify-content-center align-items-center">
                <h4 class="my-3"> OR</h4>
            </div>
            <div class="col-md-5">

                <div class="transferFunds shadow-sm">
                    <div class="pointer">
                        2
                    </div>
                    <div class="para pl-2">
                        Transfer funds to our bank account:
                    </div>
                </div>

                <div class="transferFunds shadow-sm mt-2">
                    <div class="para pl-2">
                        <b>CAF BANK</b> <br>
                        Tevini Ltd, <br>
                        <!--Dummy address goes here <br>-->
                        Sort Code: <b>40-52-40</b><br>
                        Account no: <b>00024463</b>
                    </div>
                </div>

            </div>
        </div>
    </fieldset>
    <p class="text-center fw-bold"> N.B. Please mention your name and client number as the reference.</p>

    {{-- donation calculation start  --}}
    <fieldset >
        <legend>DONATION CALCULATOR:</legend>
        <div class="ermsg"></div>

        @if (isset($dcal))
        <div class="row">
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="number" name="income_amount" id="income_amount" class="form-control" value="{{$dcal->income_amount}}" placeholder="Income Amount">
                        <input type="hidden" name="dcalid" id="dcalid" value="{{$dcal->id}}">
                    </div>
                </div>
                <div class="transferFunds shadow-sm mt-2">
                    <div class="para pl-2">
                        <select name="income_slot" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                            <option value="">Select Donation Slot</option>
                            <option value="7" @if ($dcal->income_slot == "7") selected @endif>Weekly</option>
                            <option value="30" @if ($dcal->income_slot == "30") selected @endif>Monthly</option>
                            <option value="0" @if ($dcal->income_slot == "0") selected @endif>On/Off</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <select name="donation_percentage" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                            <option value="">Select Donation Percentage</option>
                            <option value="5" @if ($dcal->donation_percentage == "5") selected @endif>5%</option>
                            <option value="10" @if ($dcal->donation_percentage == "10") selected @endif>10%</option>
                        </select>
                    </div>
                </div>
                <div class="transferFunds shadow-sm mt-2">
                    <div class="para pl-2">
                        <input type="button" id="dCalUpBtn" value="Update" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="number" name="income_amount" id="income_amount" class="form-control" value="" placeholder="Income Amount">
                    </div>
                </div>
                <div class="transferFunds shadow-sm mt-2">
                    <div class="para pl-2">
                        <select name="income_slot" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                            <option value="">Select Donation Slot</option>
                            <option value="7">Weekly</option>
                            <option value="30">Monthly</option>
                            <option value="0">On/Off</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <select name="donation_percentage" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                            <option value="">Select Donation Percentage</option>
                            <option value="5">5%</option>
                            <option value="10">10%</option>
                        </select>
                    </div>
                </div>
                <div class="transferFunds shadow-sm mt-2">
                    <div class="para pl-2">
                        <input type="button" id="dCalBtn" value="Submit" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </div>
        @endif
        
    </fieldset>


    {{-- new code  --}}
    <fieldset >      
        <div class="row">
            <div class="col-md-6">
                <legend>TOTAL DONATION IN CURRENT YEAR:</legend>
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="text" class="form-control" value="{{$totaltran}}" readonly>
                    </div>
                </div>
                
            </div>
            <div class="col-md-6">
                <legend>AVAILABLE FOR DONATION :</legend>
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="text" class="form-control" value="{{$availabledonation}}" readonly>
                    </div>
                </div>

            </div>
        </div>
        
    </fieldset>

    {{-- end  --}}

    <fieldset >
        <legend>DONATION DETAILS:</legend>
        <div class="row">
            <div class="col-md-12 mt-2 text-center">
                

                <div class="overflow">
                    <table class="table table-custom shadow-sm bg-white" id="exampleIn">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Donation Slot</th>
                                <th>Income Amount</th>
                                <th>Donation Amount</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($dondetails as $data)
                            @php
                                $slot = \App\Models\DonationCalculator::where('donor_id','=',Auth::user()->id)->first()->income_slot;
                            @endphp
                                
                                <tr>
                                    <td>{{ date('d-M, Y', strtotime($data->date)) }}</td>
                                    <td>
                                        @if ($slot == 7)
                                            Weekly
                                        @elseif ($slot == 30)
                                            Monthly
                                        @else
                                            On/Off
                                        @endif
                                    </td>
                                    <td>{{ \App\Models\DonationCalculator::where('donor_id','=',Auth::user()->id)->first()->income_amount}}</td>
                                    <td>{{$data->donation_amount}}</td>
                                </tr>
                            @endforeach



                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </fieldset>



</div>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Update Overdrawn </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsgod"></div>
            <div class="mb-3">
                <label for="overdrawnno" class="form-label">Overdrawn Amount</label>
                <input type="number" class="form-control" id="overdrawnno">
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
  <!-- Modal End -->
@endsection


@section('script')

<script>
$(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //
        //add overdrawn
        $(".overdrawn").click(function(){
            var overdrawnid = $(this).attr("overdrawn-id");
            console.log(overdrawnid );
            $('#overdrawnid').val(overdrawnid);
	    });
        var overdrawnurl = "{{URL::to('/user/update-overdrawn')}}";
        $("#overdrawnBtn").click(function(){
        var overdrawnid= $("#overdrawnid").val();
        var overdrawnno= $("#overdrawnno").val();
        // console.log(stockId);
        $.ajax({
            url: overdrawnurl,
            method: "POST",
            data: {overdrawnid:overdrawnid,overdrawnno:overdrawnno},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsgod").html(d.message);
                }else if(d.status == 300){
                    $(".ermsgod").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });
            });
        // overdrawn END



        var url = "{{URL::to('/user/donation-calculator')}}";
            // console.log(url);
            $("#dCalBtn").click(function(){
                    
                    var form_data = new FormData();
                    form_data.append("income_amount", $("#income_amount").val());
                    form_data.append("income_slot", $("#income_slot").val());
                    form_data.append("donation_percentage", $("#donation_percentage").val());
                    
                    $.ajax({
                      url: url,
                      method: "POST",
                      contentType: false,
                      processData: false,
                      data:form_data,
                      success: function (d) {
                          if (d.status == 303) {
                              $(".ermsg").html(d.message);
                          }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            window.setTimeout(function(){location.reload()},2000)
                          }
                      },
                      error: function (d) {
                          console.log(d);
                      }
                  });
                //create  end
                
            });





            var upurl = "{{URL::to('/user/donation-calculator-update')}}";
            // console.log(url);
            $("#dCalUpBtn").click(function(){
                    
                    var form_data = new FormData();
                    form_data.append("dcalid", $("#dcalid").val());
                    form_data.append("income_amount", $("#income_amount").val());
                    form_data.append("income_slot", $("#income_slot").val());
                    form_data.append("donation_percentage", $("#donation_percentage").val());
                    
                    $.ajax({
                      url: upurl,
                      method: "POST",
                      contentType: false,
                      processData: false,
                      data:form_data,
                      success: function (d) {
                          if (d.status == 303) {
                              $(".ermsg").html(d.message);
                          }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            window.setTimeout(function(){location.reload()},2000)
                          }
                      },
                      error: function (d) {
                          console.log(d);
                      }
                  });
                //create  end
                
            });












    });
</script>

@endsection
