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
                     Top Up using yur card <br>
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
    });
</script>

@endsection
