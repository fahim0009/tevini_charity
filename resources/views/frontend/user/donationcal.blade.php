@extends('frontend.layouts.user')

@section('content')


<div class="dashboard-content py-2 px-4">
    {{-- donation calculation start  --}}
    <fieldset >

        @if(isset($msg))<p class="text-center fw-bold">{{$msg}}</p>@endif
        <legend>DONATION CALCULATOR:</legend>
        <div class="ermsg"></div>

        @if (isset($donor_cal))
        <div class="row">
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="number" name="income_amount" id="income_amount" class="form-control" value="{{$donor_cal->income_amount}}" placeholder="Income Amount">
                        <input type="hidden" name="dcalid" id="dcalid" value="{{$donor_cal->id}}">
                    </div>
                </div>
                <div class="transferFunds shadow-sm mt-2">
                    <div class="para pl-2">
                        <select name="income_slot" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                            <option value="">Select Donation Slot</option>
                            <option value="7" @if ($donor_cal->income_slot == "7") selected @endif>Weekly</option>
                            <option value="30" @if ($donor_cal->income_slot == "30") selected @endif>Monthly</option>
                            <option value="0" @if ($donor_cal->income_slot == "0") selected @endif>One-Off</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <select name="donation_percentage" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                            <option value="">Select Donation Percentage</option>
                            <option value="10" @if ($donor_cal->donation_percentage == "10") selected @endif>10%</option>
                            <option value="20" @if ($donor_cal->donation_percentage == "20") selected @endif>20%</option>
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
                            <option value="0">One-Off</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <select name="donation_percentage" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                            <option value="">Select Donation Percentage</option>
                            <option value="10">10%</option>
                            <option value="20">20%</option>
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


        {{-- donation calculation start  --}}
        <fieldset >

            @if(isset($msg))<p class="text-center fw-bold">{{$msg}}</p>@endif
            <legend>OTHERS DONATION:</legend>
            <div class="otherermsg"></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="transferFunds shadow-sm">
                        <div class="para pl-2">
                            <input type="number" name="d_amount" id="d_amount" class="form-control" value="" placeholder="Donation Amount">
                        </div>
                    </div>
                    <div class="transferFunds shadow-sm mt-2">
                        <div class="para pl-2">
                            <input type="text" name="d_title" id="d_title" class="form-control" value="" placeholder="Donation receiver, Charity, Campaign">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="transferFunds shadow-sm">
                        <div class="para pl-2">
                            <input type="date" name="donation_date" id="donation_date" class="form-control" value="" placeholder="Date">
                        </div>
                    </div>
                    <div class="transferFunds shadow-sm mt-2">
                        <div class="para pl-2">
                            <input type="button" id="othrBtn" value="Submit" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>

        </fieldset>


    {{-- new code  --}}
    <fieldset >
        <div class="row">
            <div class="col-md-6">
                <legend>Tevini  platform:</legend>
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

            <div class="col-md-6">
                <br>
                <legend>Others Donation:</legend>
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="text" class="form-control" value="{{$totalotherdonation}}" readonly>
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
                                <th>Income by</th>
                                <th>Income Amount</th>
                                <th>Donation Amount</th>
                            </tr>
                        </thead>
                        <tbody>


                            @foreach ($dondetails as $data)
                                <tr>
                                    <td>{{ date('d-M, Y', strtotime($data->date)) }}</td>
                                    <td>
                                        @if ($data->income_slot == 7)
                                            Weekly
                                        @elseif ($data->income_slot == 30)
                                            Monthly
                                        @else
                                            On/Off
                                        @endif
                                    </td>
                                    <td>{{$data->income_amount}}</td>
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
@endsection


@section('script')
<script>
$(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

    // donations calculators start
        var url = "{{URL::to('/user/donation-calculator')}}";
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
            // donation calclutors end

            // others donation store start
            var otherdurl = "{{URL::to('/user/other-donation-store')}}";
            $("#othrBtn").click(function(){
                    var form_data = new FormData();
                    form_data.append("d_title", $("#d_title").val());
                    form_data.append("donation_date", $("#donation_date").val());
                    form_data.append("d_amount", $("#d_amount").val());

                    $.ajax({
                      url: otherdurl,
                      method: "POST",
                      contentType: false,
                      processData: false,
                      data:form_data,
                      success: function (d) {
                          if (d.status == 303) {
                              $(".otherermsg").html(d.message);
                          }else if(d.status == 300){
                            $(".otherermsg").html(d.message);
                            window.setTimeout(function(){location.reload()},2000)
                          }
                      },
                      error: function (d) {
                          console.log(d);
                      }
                  });
                //create  end

            });
            // others donation store end




    });
</script>

@endsection
