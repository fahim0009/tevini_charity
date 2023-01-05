@extends('frontend.layouts.user')
@section('content')
<div class="dashboard-content py-2 px-4">
    {{-- donation calculation start  --}}

        <div style="text-align: center; font-size:25px">
            <p class="text-center" style="text-align: center">Ma'aser Calculator</p>
        </div>
<hr>
        @if(isset($msg))<p class="text-center fw-bold">{{$msg}}</p>@endif
        <fieldset>
            <legend>Add One-Off Income</legend>
            <div class="oneoffermsg"></div>
            <section class="">
                <div class="row  my-3 mx-0 ">
                    <div class=" col-md-12 bg-white px-4">
                        <div class="form-container">
                            <div class="overflow mx-auto">
                                <table class="table shadow-sm">
                                    <thead>
                                        <tr>
                                            <th>Choose Start Date  </th>
                                            <th>Income</th>
                                            <th>Description</th>
                                            <th>Choose Your Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="item-row" style="position:realative;">
                                            <td width="150px">
                                                <input class="form-control" id="ostart_date" name="ostart_date" type="date" value="">
                                            </td>
                                            <td width="150px">
                                                <input class="form-control" id="oincome_amount" name="oincome_amount" value=""  placeholder="Income">
                                            </td>
                                            <td width="150px">
                                                <input style="min-width: 50px;"  type="text" id="oincome_title" name="oincome_title" class="form-control" value="" placeholder="Description">
                                            </td>
                                            <td width="250px">
                                                <select name="odonation_percentage" id="odonation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                                                    <option value>Donation Percentage</option>
                                                    <option value="10">10%</option>
                                                    <option value="20">20%</option>
                                              </select>
                                            </td>
                                            <td width="50px">
                                                <div class="col-md-6 my-2">
                                                    <button class="text-white btn-theme ml-1 mb-4" style="margin-top: -7px;" id="oincome_submit" type="button">Submit</button>

                                                </div>
                                            </td>
                                            <td width="50px">
                                                <div class="col-md-6 my-2">
                                                    <a href="{{ route('user.onOffdonationDetails')}}" style="margin-top: -7px;" class="btn btn-primary">View</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </fieldset>
        <fieldset >
        <legend>Add Regular Income:</legend>
        <div class="ermsg"></div>
        <section class="">
            <div class="row  my-3 mx-0 ">
                <div class=" col-md-12 bg-white px-4">
                    <div class="form-container">
                        <div class="overflow mx-auto">
                            <table class="table shadow-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Choose Start Date  </th>
                                        <th>Income</th>
                                        <th>Description</th>
                                        <th>Choose Option</th>
                                        <th>Choose Your Percentage</th>
                                    </tr>
                                </thead>
                                <tbody id="inner">

                                    @forelse($donor_cals as $donor_cal)

                                    <tr class="item-row" style="position:realative;">
                                        <td width = "50px">
                                            {{-- <div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 8px;" onclick="removeRow(event)" >X</div> --}}
                                        </td>
                                        <td width="150px">
                                            <input class="form-control" name="start_date[]" type="date" value="{{$donor_cal->start_date}}">
                                            <input class="form-control" name="donorcal_id[]" type="hidden" value="{{$donor_cal->id}}">
                                        </td>
                                        <td width="150px">
                                            <input class="form-control donor" name="income_amount[]" value="{{$donor_cal->income_amount}}"  placeholder="Income Amount">
                                        </td>
                                        <td width="150px">
                                            <input style="min-width: 50px;"  type="text" name="income_title[]" class="form-control donorAcc" value="{{$donor_cal->income_title}}" placeholder="Income Title">
                                        </td>
                                        <td width="200px">
                                            <select name="income_slot[]" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                                                <option value="">Select Donation Slot</option>
                                                <option value="7" @if ($donor_cal->income_slot == "7") selected @endif>Weekly</option>
                                                <option value="30" @if ($donor_cal->income_slot == "30") selected @endif>Monthly</option>
                                            </select>
                                        </td>
                                        <td width="150px">
                                            <select name="donation_percentage[]" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                                                <option value="">Select Donation Percentage</option>
                                                <option value="10" @if ($donor_cal->donation_percentage == "10") selected @endif>10%</option>
                                                <option value="20" @if ($donor_cal->donation_percentage == "20") selected @endif>20%</option>
                                          </select>
                                        </td>
                                    </tr>

                                    @empty

                                    <tr class="item-row" style="position:realative;">
                                        <td width = "50px">
                                            <div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 8px;" onclick="removeRow(event)" >X</div>
                                        </td>
                                        <td width="150px">
                                            <input class="form-control" name="start_date[]" type="date" value="">
                                        </td>
                                        <td width="150px">
                                            <input class="form-control" name="income_amount[]" value=""  placeholder="Income">
                                        </td>
                                        <td width="150px">
                                            <input style="min-width: 50px;"  type="text" name="income_title[]" class="form-control" value="" placeholder="Description">
                                        </td>
                                        <td width="200px">
                                            <select name="income_slot[]" max-width="100px" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                                                <option value>Select Income Slot</option>
                                                <option value="7">Weekly</option>
                                                <option value="30">Monthly</option>
                                            </select>
                                        </td>
                                        <td width="150px">
                                            <select name="donation_percentage[]" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                                                <option value>Donation Percentage</option>
                                                <option value="10">10%</option>
                                                <option value="20">20%</option>
                                          </select>
                                        </td>
                                    </tr>

                                    @endforelse
                                </tbody>
                                <tr>
                                    <td colspan="4">
                                        <span type="submit" class="text-white btn-theme add-row"> + Add
                                        </span>
                                    </td>
                                    <td width="80px">
                                    </td>
                                    <td width="250px">
                                        <div class="col-md-6 my-2">
                                            @if ($donor_cals->isEmpty())
                                            <button class="text-white btn-theme ml-1 mb-4" id="income_submit" type="button">Submit</button>
                                            @else
                                            <button class="text-white btn-theme ml-1 mb-4" id="income_update" type="button">Update</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </fieldset>


        {{-- donation calculation start  --}}
        {{-- <div style="text-align: center; font-size:25px">
            <p class="text-center" style="text-align: center">Charity Goal</p>
        </div>
        <hr> --}}
        <fieldset >
            @if(isset($msg))<p class="text-center fw-bold">{{$msg}}</p>@endif
            <legend>Other Given Charity:</legend>
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
                <legend>Tevini Ltd.</legend>
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="text" class="form-control" value="{{$tevini_donation}}" readonly>
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
                <legend>Other Given Charity:</legend>
                <div class="transferFunds shadow-sm">
                    <div class="para pl-2">
                        <input type="text" class="form-control" value="{{$otherdonation}}" readonly>
                    </div>
                </div>

            </div>
        </div>

    </fieldset>

    {{-- end  --}}

    <fieldset >
        <legend>REGULAR INCOME DONATION DETAILS:</legend>
        <div class="row">
            <div class="stsermsg"></div>
            <div class="col-md-12 mt-2 text-center">

                <div class="overflow">
                    <table class="table table-custom shadow-sm bg-white" id="exampleIn">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>Income Title</th>
                                <th>Income by</th>
                                <th>Donation Percentage</th>
                                <th>Income Amount</th>
                                <th>Status</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>


                            @foreach ($donor_cals as $data)
                                <tr>
                                    <td>{{$data->start_date }}</td>
                                    <td>{{$data->income_title }}</td>
                                    <td>
                                        @if ($data->income_slot == 7)
                                            Weekly
                                        @elseif ($data->income_slot == 30)
                                            Monthly
                                        @else
                                            On/Off
                                        @endif
                                    </td>
                                    <td>{{$data->donation_percentage}}</td>
                                    <td>{{$data->income_amount}}</td>
                                    <td>

                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="flexSwitchCheckChecked"  data-id="{{$data->id}}" {{ $data->status ? 'checked' : '' }}>
                                            <span class="flip-indecator" data-toggle-on="Active" data-toggle-off="Inactive"></span>
                                        </div>

                                    </td>
                                    <td>
                                        <a href="{{ route('user.donationdetails', $data->id)}}" class="btn btn-primary">View</a>
                                    </td>
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
<script type="text/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    function removeRow(event) {
            event.target.parentElement.parentElement.remove();
    }
</script>

<script>
    $(function() {
      $('.form-check-input').change(function() {
        var url = "{{URL::to('/user/active-donation-details')}}";
          var status = $(this).prop('checked') == true ? 1 : 0;
          var id = $(this).data('id');
           console.log(status);
          $.ajax({
              type: "GET",
              dataType: "json",
              url: url,
              data: {'status': status, 'id': id},
              success: function(d){
                    if (d.status == 303) {
                        $(".stsermsg").html(d.message);
                    }else if(d.status == 300){
                        $(".stsermsg").html(d.message);
                        window.setTimeout(function(){location.reload()},2000)
                    }
                },
                error: function (d) {
                    console.log(d);
                }
          });
      })
    })
  </script>

<script>
$(document).ready(function () {
    $(".add-row").click(function() {
                var markup =
                    ' <tr class="item-row" style="position:realative;"> <td width="50px"> <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px; left: 4px; top: 8px;" onclick="removeRow(event)" >X</div></td><td width="150px"> <input class="form-control" name="start_date[]" type="date" value=""> </td><td width="150px"> <input class="form-control" name="income_amount[]" value="" placeholder="Income"> </td><td width="150px"> <input style="min-width: 50px;" type="text" name="income_title[]" class="form-control" value="" placeholder="Description"> </td><td width="200px"> <select name="income_slot[]" max-width="100px" id="income_slot" class="form-control" aria-placeholder="Income Slot"> <option value="">Select Income Slot</option> <option value="7">Weekly</option> <option value="30">Monthly</option></select> </td><td width="150px"> <select name="donation_percentage[]" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage"> <option value="">Donation Percentage</option> <option value="10">10%</option> <option value="20">20%</option> </select> </td></tr>';
                $("table #inner").append(markup);
            });



    //header for csrf-token is must in laravel
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //

    // donations calculators start
        var url = "{{URL::to('/user/donation-calculator')}}";
            $("#income_submit").click(function(){

            var start_dates = $("input[name='start_date[]']")
              .map(function(){return $(this).val();}).get();

            var income_amounts = $("input[name='income_amount[]']")
              .map(function(){return $(this).val();}).get();

            var income_titles = $("input[name='income_title[]']")
              .map(function(){return $(this).val();}).get();

            var income_slots = $("select[name='income_slot[]']")
              .map(function(){return $(this).val();}).get();

            var donation_percentages = $("select[name='donation_percentage[]']")
              .map(function(){return $(this).val();}).get();

                    $.ajax({
                      url: url,
                      method: "POST",
                      data: {start_dates,income_amounts,income_titles,income_slots,donation_percentages},
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

//  regular income update
            var upurl = "{{URL::to('/user/donation-calculator-update')}}";
            $("#income_update").click(function(){

                var start_dates = $("input[name='start_date[]']")
                .map(function(){return $(this).val();}).get();

                var donorcal_ids = $("input[name='donorcal_id[]']")
                .map(function(){return $(this).val();}).get();

                var income_amounts = $("input[name='income_amount[]']")
                .map(function(){return $(this).val();}).get();

                var income_titles = $("input[name='income_title[]']")
                .map(function(){return $(this).val();}).get();

                var income_slots = $("select[name='income_slot[]']")
                .map(function(){return $(this).val();}).get();

                var donation_percentages = $("select[name='donation_percentage[]']")
                .map(function(){return $(this).val();}).get();

                    $.ajax({
                      url: upurl,
                      method: "POST",
                      data: {start_dates,donorcal_ids,income_amounts,income_titles,income_slots,donation_percentages},
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
            });


//  on-off income add
            var onoffurl = "{{URL::to('/user/one-off-donation')}}";
            $("#oincome_submit").click(function(){
                    var form_data = new FormData();
                    form_data.append("oincome_title", $("#oincome_title").val());
                    form_data.append("oincome_amount", $("#oincome_amount").val());
                    form_data.append("ostart_date", $("#ostart_date").val());
                    form_data.append("odonation_percentage", $("#odonation_percentage").val());

                    $.ajax({
                      url: onoffurl,
                      method: "POST",
                      contentType: false,
                      processData: false,
                      data:form_data,
                      success: function (d) {
                          if (d.status == 303) {
                              $(".oneoffermsg").html(d.message);
                          }else if(d.status == 300){
                            $(".oneoffermsg").html(d.message);
                            window.setTimeout(function(){location.reload()},2000)
                          }
                      },
                      error: function (d) {
                          console.log(d);
                      }
                  });

            });


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

    });
    // others donation store end




    });
</script>

@endsection
