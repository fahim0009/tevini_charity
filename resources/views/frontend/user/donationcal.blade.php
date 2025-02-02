@extends('frontend.layouts.user')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Maaser calculator
            </div>
            <p>To activate your calculator please insert a one-off income of any amount.</p>
        </div>
    </div>

    <div class="row ">
        <div class="col-lg-3">
            <div class="calculatior mt-3" style="min-height: 0px">
                <div>
                    <div class="fs-37 fw-bold txt-secondary">{{$availabledonation}} GBP</div>
                    <div class="fs-16 fw-bold txt-secondary">Total maaser Goal</div>
                    <div class="progress mt-5 mb-3">
                        <div class="progress-bar   progress-bar-animated" role="progressbar"
                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                        </div>
                    </div>
                    <div class="fs-23 fw-bold txt-primary mb-2 lh-1">You already paid £{{$tevini_donation}}
                        through Tevini</div>
                    <a href="#" class="fs-16 txt-secondary  ">Other Given Charity: £{{$otherdonation}} </a>
                    <a href="{{ route('user.makedonation')}}" class="btn-theme mt-3 bg-secondary text-white">Make a donation</a>
                </div>
            </div>


            <div class="calculatior mt-3" style="min-height: 0px">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Add external donation: </div> <br>

                    <div class="otherermsg"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Donation Amount</label>
                                <input type="number" name="d_amount" id="d_amount" class="form-control" value="" placeholder="Donation Amount">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Donation receiver, Charity, Campaign</label>
                                <input type="text" name="d_title" id="d_title" class="form-control" value="" placeholder="Donation receiver, Charity, Campaign">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group ">
                                <label for=""> Date</label>
                                <input type="date" name="donation_date" id="donation_date" class="form-control" value="" placeholder="Date">
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-12">
                            <div class="form-group ">
                                <input type="button" id="othrBtn" value="Submit" class="btn-theme mt-3 bg-secondary text-white w-100">
                                <a href="{{ route('user.otherdonationDetails')}}" class="btn-theme bg-primary text-white w-100">View</a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>




        </div>
        <div class="col-lg-9 py-3">
            <div class="fw-bold fs-36 txt-secondary">Add your income </div> <br>
            <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">One-off income</div> <br>
            <div class="oneoffermsg"></div>
            <div class="data-container">
                <table class="table table-theme mt-0">
                    <thead>
                        <tr>
                            <th scope="col">Choose Start Date  </th>
                            <th scope="col">Income</th>
                            <th scope="col">Description</th>
                            <th scope="col">Choose Your Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-1">
                                <input class="form-control" id="ostart_date" name="ostart_date" type="date" value="">
                            </td>
                            <td class="px-1">
                                <input class="form-control" id="oincome_amount" name="oincome_amount" value=""  placeholder="Income">
                            </td>
                            <td class="fs-16 txt-secondary px-1">
                                <input style="min-width: 50px;"  type="text" id="oincome_title" name="oincome_title" class="form-control" value="" placeholder="Description">
                            </td>
                            <td class="fs-16 txt-secondary px-1 text-center">
                                <select name="odonation_percentage" id="odonation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                                    <option value>Donation Percentage</option>
                                    <option value="10">10%</option>
                                    <option value="20">20%</option>
                              </select>
                            </td>
                            <td class="text-center">
                                <button class="btn-theme bg-secondary text-white float-end" id="oincome_submit" type="button">Submit</button>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user.onOffdonationDetails')}}" class="btn-theme bg-secondary text-white float-end">View</a>
                            </td>
                        </tr>
                            
                    </tbody>
                </table>


            </div>
            <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Regular income</div> <br>
            <div class="ermsg"></div>
            <div class="data-container">
                <table class="table table-theme mt-0">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Choose Start Date  </th>
                            <th scope="col">Income</th>
                            <th scope="col">Description</th>
                            <th scope="col">Choose Option</th>
                            <th scope="col">Choose Your Percentage</th>

                        </tr>
                    </thead>
                    <tbody id="inner">


                        @forelse($donor_cals as $donor_cal)
                        <tr class="item-row" style="position:realative;">
                            <td class="px-1">
                                
                            </td>
                            <td class="px-1">
                                
                                <input class="form-control" name="start_date[]" type="date" value="{{$donor_cal->start_date}}">
                                <input class="form-control" name="donorcal_id[]" type="hidden" value="{{$donor_cal->id}}">

                            </td>
                            <td class="fs-16 txt-secondary px-1">
                                <input class="form-control donor" name="income_amount[]" value="{{$donor_cal->income_amount}}"  placeholder="Income Amount">
                                
                            </td>
                            <td class="fs-16 txt-secondary px-1">
                                <input style="min-width: 50px;"  type="text" name="income_title[]" class="form-control donorAcc" value="{{$donor_cal->income_title}}" placeholder="Income Title">
                                
                            </td>
                            <td class="fs-16 txt-secondary px-1 text-center">
                                <select name="income_slot[]" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                                    <option value="">Select Donation Slot</option>
                                    <option value="7" @if ($donor_cal->income_slot == "7") selected @endif>Weekly</option>
                                    <option value="30" @if ($donor_cal->income_slot == "30") selected @endif>Monthly</option>
                                </select>
                                
                            </td>
                            <td class="text-center">
                                <select name="donation_percentage[]" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                                    <option value="">Select Donation Percentage</option>
                                    <option value="10" @if ($donor_cal->donation_percentage == "10") selected @endif>10%</option>
                                    <option value="20" @if ($donor_cal->donation_percentage == "20") selected @endif>20%</option>
                              </select>


                            </td>
                        </tr>
                        @empty

                        <tr class="item-row" style="position:realative;">
                            <td class="px-1">
                                <div style="color: white;  user-select:none;  padding: 5px;    background: red;    width: 45px;    display: flex;    align-items: center; margin-right:5px;   justify-content: center;    border-radius: 4px;   left: 4px;    top: 8px;" onclick="removeRow(event)" >X</div>
                                
                            </td>
                            <td class="px-1">
                                
                                <input class="form-control" name="start_date[]" type="date" value="">

                            </td>
                            <td class="fs-16 txt-secondary px-1">
                                <input class="form-control" name="income_amount[]" value=""  placeholder="Income">
                                
                            </td>
                            <td class="fs-16 txt-secondary px-1">
                                <input style="min-width: 50px;"  type="text" name="income_title[]" class="form-control" value="" placeholder="Description">
                                
                            </td>
                            <td class="fs-16 txt-secondary px-1 text-center">
                                <select name="income_slot[]" max-width="100px" id="income_slot" class="form-control" aria-placeholder="Income Slot">
                                    <option value>Select Income Slot</option>
                                    <option value="7">Weekly</option>
                                    <option value="30">Monthly</option>
                                </select>
                                
                            </td>
                            <td class="text-center">
                                <select name="donation_percentage[]" id="donation_percentage" class="form-control" aria-placeholder="Donation Percentage">
                                    <option value>Donation Percentage</option>
                                    <option value="10">10%</option>
                                    <option value="20">20%</option>
                              </select>


                            </td>
                        </tr>

                        @endforelse
                    </tbody>
                    

                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <span class="fs16 txt-primary add-row" type="submit" >Add +</span>
                            </td>
                            <td>

                            </td>
                            <td class="text-center">
                                <div class="col-md-6 my-2">
                                    @if ($donor_cals->isEmpty())
                                    <button class="btn-theme me-5 bg-secondary text-white float-end" id="income_submit" type="button">Submit</button>
                                    @else
                                    <button class="btn-theme me-5 bg-secondary text-white float-end" id="income_update" type="button">Update</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>

    <div class="row mb-5" style="display:none">
        <div class="col-lg-12">
            <div class="row ">
                <div class="col-lg-6  px-3"> 
                    
                    <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Other Given Charity: </div> <br>

                    <div class="otherermsg"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Donation Amount</label>
                                <input type="number" name="d_amount" id="d_amount" class="form-control" value="" placeholder="Donation Amount">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Donation receiver, Charity, Campaign</label>
                                <input type="text" name="d_title" id="d_title" class="form-control" value="" placeholder="Donation receiver, Charity, Campaign">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group ">
                                <label for=""> Date</label>
                                <input type="date" name="donation_date" id="donation_date" class="form-control" value="" placeholder="Date">
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-12 mt-4">
                            <div class="form-group ">
                                <input type="button" id="othrCharityBtn" value="Submit" class="btn btn-primary">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-6  px-3"> 
                    
                    <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Tevini Ltd. </div> <br>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{$tevini_donation}}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">AVAILABLE FOR DONATION :</div> <br>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{$availabledonation}}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Other Given Charity:</div> <br>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{$otherdonation}}" readonly>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <div class="row mb-5 mt-3">
        
        <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">REGULAR INCOME DONATION DETAILS: </div> <br>
        <div class="col-lg-12 mt-2">
            <div class="stsermsg"></div>
            <div class="data-container">
                <table class="table table-theme mt-4" id="exampleIn">
                    <thead>
                        <tr> 
                            <th scope="col">Start Date</th>
                            <th scope="col">Income Title</th>
                            <th scope="col">Income by</th>
                            <th scope="col">Donation Percentage</th>
                            <th scope="col">Income Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">View</th>
    
    
                        </tr>
                    </thead>
                    <tbody>
    
                        @foreach ($donor_cals as $data)
                            <tr>
                                <td class="fs-16 txt-secondary">{{$data->start_date }}</td>
                                <td class="fs-16 txt-secondary">{{$data->income_title }}</td>
                                <td class="fs-16 txt-secondary">
                                    @if ($data->income_slot == 7)
                                        Weekly
                                    @elseif ($data->income_slot == 30)
                                        Monthly
                                    @else
                                        On/Off
                                    @endif
                                </td>
                                <td class="fs-16 txt-secondary">{{$data->donation_percentage}}</td>
                                <td class="fs-16 txt-secondary">{{$data->income_amount}}</td>
                                <td class="fs-16 txt-secondary">
    
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="flexSwitchCheckChecked"  data-id="{{$data->id}}" {{ $data->status ? 'checked' : '' }}>
                                        <span class="flip-indecator" data-toggle-on="Active" data-toggle-off="Inactive"></span>
                                    </div>
    
                                </td>
                                <td class="fs-16 txt-secondary">
                                    <a href="{{ route('user.donationdetails', $data->id)}}" class="btn-theme bg-secondary text-white">View</a>
                                </td>
                            </tr>
                        @endforeach
    
                    </tbody>
                </table>
            </div>
            
            



        </div>
    </div>



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
