@extends('frontend.layouts.user')

@section('content')


<div class="dashboard-content py-2 px-4">
 



    <a href="{{ route('user.donationcal')}}" class="btn btn-primary">Back</a>
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


                            @foreach ($donation as $data)
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
                                    <td> {{$data->donation_amount}}</td>
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
