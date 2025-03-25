@extends('frontend.layouts.user')
@section('content')

@php
    $alltransactions = \App\Models\Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->limit(5)->get();


    $tamount = \App\Models\Usertransaction::where([
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
                ])->orderBy('id','DESC')->get();

    $pending_transactions = \App\Models\Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
        ])->sum('amount');

    $donation_req = \App\Models\CharityLink::where('email',auth()->user()->email)->where('donor_notification','0')->get();

use Illuminate\Support\Carbon;
@endphp
<!-- Image loader -->
<div id='loading' style='display:none ;'>
    <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." style="height: 225px;" />
</div>
<!-- Image loader -->

@php
    $userCreatedDate = auth()->user()->created_at;
@endphp

@if ($userCreatedDate->isToday())
@if (!auth()->user()->hasVerifiedEmail())
<div class="alert alert-warning">
    We have sent you an email verification link. Please check your inbox and click the link to verify your email address. <br>

    If you don’t see the email, please check your spam folder or request a new verification link.
    <br>
    <a href="{{ route('verification.resend') }}" class="btn-theme bg-secondary" onclick="event.preventDefault(); document.getElementById('verifyBtn').submit();">Resend</a>

    <form method="POST" action="{{ route('verification.resend') }}" id="verifyBtn" style="display: none;">
    @csrf
    <button type="submit" class="btn btn-primary">
        Resend Verification Email
    </button>
    </form>

    @if (session('message'))
    <div class="alert alert-success" role="alert">
        {{ session('message') }}
    </div>
    @endif
    
</div> 
@endif
    
@else
    

    @if (!auth()->user()->hasVerifiedEmail())

        <div class="alert alert-warning">
            <strong>  Is Your Email Up to Date? </strong>
            <br>  <a href="{{ route('verification.resend') }}" class="btn-theme bg-secondary" onclick="event.preventDefault(); document.getElementById('verifyBtn').submit();" id="verifythis">Yes</a>
            <a href="{{ route('verification.resend') }}" class="btn-theme bg-danger" onclick="event.preventDefault(); document.getElementById('verifyBtn').submit();" id="verifythis">No</a> <br> Please update your current active email address
            </div>

            <form method="POST" action="{{ route('verification.resend') }}" id="verifyBtn" style="display: none;">
            @csrf
            <button type="submit" class="btn btn-primary">
                Resend Verification Email
            </button>
            </form>

            @if (session('message'))
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
            @endif

    @endif


@endif



  
<div class="row ">
    <div class="col-lg-6">
        <div class="user">
            Welcome, {{auth()->user()->name}}
        </div>
        <div class="ermsg"></div>
        @if (isset($donation_req))
        @foreach ($donation_req as $item)
            <p>You have donation request. Please click <a href="{{route('user.makedonation')}}?cid={{$item->charity_id}}&amount={{$item->amount}}" class="btn-theme bg-primary">here</a>
            <input type="button" value="X" linkid="{{$item->id}}"  class="btn-theme bg-warning close">
            </p>
        @endforeach
        @endif
        <br>

        <div class="row ">
            <div class="col-lg-6">
                <h4 class="txt-dash">Account Balance</h4>
                <h2 class="amount">{{$donorUpBalance}} GBP</h2>
                <p>Pending Balance: {{number_format($pending_transactions, 2)}} GBP</p>
            </div>
            <div class="col-lg-6">
                <h4 class="txt-dash">Tevini Ltd</h4>
                <h5>Sort Code: 30-99-50</h5>
                <h5>Account no: 80075460</h5>
            </div>
        </div>



        <div class="row my-2">
            <div class="col-lg-12" id="tdfDiv">
                <div class="tdfermsg"></div>
                <div class="">
                    <small>Calculator is for your information only actual transfer is subject to change in the exchange rate market.</small>
                    <p>
                        <iframe title="fx" src="https://wise.com/gb/currency-converter/fx-widget/converter?sourceCurrency=GBP&targetCurrency=USD&amount=1" height=210 width=500 frameBorder="0" allowtransparency="true" ></iframe>
                    </p>


                    <label for="">TDF Account Number</label>
                    <input type="text" id="tdfaccount" name="tdfaccount" class="form-control">
                    <label for="">Amount to Transfer</label>
                    <input type="text" id="tdfamount" min="0" name="tdfamount" class="form-control">
                    {{-- <button type="button" id="tdfsubmit" class="btn-theme bg-secondary">Transfer to TDF</button> --}}
                    <!-- Button trigger modal -->
                    <button type="button" id="tdfmodal" class="btn-theme bg-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Transfer to TDF
                    </button>
                </div>
            </div>
            <div class="col-lg-6 pt-3 d-flex flex-column px-4">
                
                <img src="{{ asset('assets/user/images/card.png') }}" class="img-fluid mt-3 mb-2" alt="">
                <a href="{{ route('balanceTransfer') }}" class="btn-theme bg-secondary">Balance Transfer</a>
            </div>
            @if (Auth::user()->accountno)
            <div class="col-lg-6  pt-3 d-flex flex-column px-4">
                
                <button  class="btn-theme bg-secondary" id="tdfButton">Transfer to TDF</button>
                <a href="{{ route('user.makedonation') }}" class="btn-theme bg-primary">Make a
                    donation</a>
                <a href="{{ route('user.orderbook') }}" class="btn-theme bg-secondary">Order voucher books</a>
                <a href="{{ route('stripeDonation')}}" class="btn-theme bg-ternary">Top up account</a>
                
            </div>
            <div class="col-lg-12">
                <a href="{{ route('userCardService')}}" class="btn-theme bg-primary d-block fs-14 txt-theme fw-bold">Order a card</a>
            </div>
            @endif
        </div>
        <div class="p-4 py-5 mt-2" style="background-color: #D9D9D9;">
            <div>
                <div class="txt-secondary fs-32 fw-bold  text-center">GIFT AID DONATIONS</div>  <br>
                <div class="txt-secondary fs-20"> Expected gift aid : £{{ Auth::user()->expected_gift_aid }}</div>
                <div class="txt-secondary fs-20"> Gift Aid donations for this Tax Year : £{{ $currentyramount }}</div>
                <div class="txt-secondary fs-20"> Gift Aid donations for last Tax Year : £{{ $totalamount }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row mb-5">
            <div class="col-lg-6">
                <div class="user">
                    Latest transactions
                </div>

            </div>
            <div class="col-lg-6 text-center">
                <a href="{{ route('user.transaction')}}" class="btn-theme bg-ternary">View all transactions</a>
            </div>
        </div>


        <div class="data-container">
            <table class="table table-theme mt-4">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Balance</th>
                    </tr>
                </thead>

                <?php
                $tbalance = 0;
                ?>

                @foreach ($tamount as $data)
                    @if($data->commission != 0)
                        @php
                        $tbalance = $tbalance - $data->commission;
                        @endphp
                    @endif

                    @php
                    if($data->t_type == "In"){
                        if($data->commission != 0){

                        $tbalance = $tbalance + $data->amount + $data->commission;
                        }else {

                        $tbalance = $tbalance + $data->amount;
                        }

                    }
                    @endphp

                    @php
                    if($data->t_type == "Out"){
                    $tbalance = $tbalance - $data->amount;
                    }
                    @endphp
                @endforeach


                <tbody>
                    @foreach ($alltransactions as $data)
                        @if($data->commission != 0)

                            <tr>
                                <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fs-20 txt-secondary fw-bold"></span>
                                        <span class="fs-16 txt-secondary">Commission</span>
                                    </div>
                                </td>
                                <td>-£{{$data->commission}}</td>
                                <td>£{{ number_format($tbalance, 2) }}</td>
                                @php
                                $tbalance = $tbalance + $data->commission;
                                @endphp
                            </tr>
                        @endif

                        <tr>
                            <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fs-20 txt-secondary fw-bold">@if($data->charity_id){{ $data->charity->name}}@endif</span>
                                    <span class="fs-16 txt-secondary">{{$data->title}}</span>
                                </div>
                            </td>
                            @if($data->t_type == "In")

                                @if($data->commission != 0)
                                    <td class="fs-16 info txt-primary">
                                        £{{ number_format($data->amount + $data->commission, 2) }}
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        £{{ number_format($tbalance, 2) }}
                                    </td>
                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                @else

                                    <td class="fs-16 info txt-primary">
                                        £{{number_format($data->amount, 2)}}
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        £{{ number_format($tbalance, 2) }}
                                    </td>
                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                @endif

                            @elseif($data->t_type == "Out")
                                <td class="fs-16 info" class="info">
                                    -£{{number_format($data->amount, 2) }}
                                </td>
                                <td class="fs-16 txt-secondary">
                                    £{{ number_format($tbalance, 2) }}
                                </td>
                                @if($data->pending != "0")
                                @php  $tbalance = $tbalance + $data->amount;  @endphp
                                @endif

                            @endif
                        </tr>

                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td>Previous Balance</td>
                            <td>£{{ number_format($tbalance, 2) }}</td>
                        </tr>

                </tbody>
            </table>
        </div>




    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Transfer to TDF</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
            <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">TDF Account Number: <span id="tdfaccmodal"></span> </div>
            <div class="fw-bold fs-23 txt-secondary border-bottom pb-2">Amount to Transfer: <span id="tdfamtmodal"></span></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="tdfsubmit" class="btn-theme bg-secondary">Transfer to TDF</button>
        </div>
      </div>
    </div>
  </div>


@endsection

@section('script')


<script>

    // document.getElementById('verifythis').addEventListener('click', function() {
    //     document.getElementById('loading').style.display = 'block';
    //     setTimeout(function() {
    //         document.getElementById('loading').style.display = 'none';
    //     }, 2000); // Adjust the timeout duration as needed
    // });

    // JavaScript to allow only numeric input
    document.getElementById('tdfamount').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Allow only digits
        });


    $(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        //  make mail start
        var url = "{{URL::to('/user/close-a-link')}}";
        $(".close").click(function(){
            linkid = $(this).attr('linkid');
            $.ajax({
                url: url,
                method: "POST",
                data: {linkid},
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
        // send mail end =

        $("#tdfDiv").hide();
        $("#tdfButton").click(function() {
            $("#tdfButton").hide();
            $("#tdfDiv").show();
        });


        // tdf transfer modal show
        $("#tdfmodal").click(function(){

            var tdfamount = $("#tdfamount").val();
            var tdfaccount = $("#tdfaccount").val();
            
            $("#tdfamtmodal").html(tdfamount);
            $("#tdfaccmodal").html(tdfaccount);
                        

        });

        var tdfurl = "{{URL::to('/user/transfer-to-tdf')}}";
        $("#tdfsubmit").click(function(){

            // if(!confirm('Are you sure?')) return;
            $('#exampleModal').modal('hide');
                $("#loading").show();
            var tdfamount = $("#tdfamount").val();
            var tdfaccount = $("#tdfaccount").val();
            
            $.ajax({
                url: tdfurl,
                method: "POST",
                data: {tdfamount,tdfaccount},
                success: function (d) {
                    
                    $("#loading").hide();
                    if (d.status == 303) {
                        $(".tdfermsg").html(d.message);
                    }else if(d.status == 300){
                        $(".tdfermsg").html(d.message);
                        window.setTimeout(function(){location.reload()},2000)
                    }
                },
                error: function (d) {
                    console.log(d);
                }
            });                

        });

        //currencyurl start 
        var currencyurl = "{{URL::to('/user/check-currency-amount')}}";
        $("#tdfamount").keyup(function(){
            var length =  $(this).val().length;

            var tdfamount = $("#tdfamount").val();
            var currency_from = "GBP";
            var currency_to = "USD";
            
            if (length > 0) {
                $.ajax({
                    url: currencyurl,
                    method: "POST",
                    data: {tdfamount,currency_from,currency_to},

                    success: function (d) {
                        console.log(d);
                        if (d.status == 303) {
                            $(".perrmsg").html(d.message);
                        }else if(d.status == 300){
                            $(".perrmsg").html(d.message);
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                }); 
            }else{
                $(".perrmsg").html("");
            }

            
        });


        // $("#tdfamount").keyup(function(){
            
        //     var tdfamount = $("#tdfamount").val();
        //     // set endpoint and your access key
        //     endpoint = 'live'
        //     access_key = 'YOUR_ACCESS_KEY';

        //     // get the most recent exchange rates via the "live" endpoint:
        //     $.ajax({
        //         url: 'https://api.exchangerate.host/' + endpoint + '?access_key=' + access_key,   
        //         dataType: 'jsonp',
        //         success: function(json) {

        //             // exchange rata data is stored in json.quotes
        //             alert(json.quotes.USDGBP);

        //             // source currency is stored in json.source
        //             alert(json.source);

        //             // timestamp can be accessed in json.timestamp
        //             alert(json.timestamp);

        //         }
        //     });
            

            
        // });



        //currencyurl end 

    });
</script>

@endsection
