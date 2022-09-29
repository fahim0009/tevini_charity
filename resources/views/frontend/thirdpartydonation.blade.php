@extends('frontend.layouts.master')
@section('content')
<style>
    /*loader css*/
        #loading {
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        }

        #loading-image {
        z-index: 100;
        }

    .homeBanner {
        padding: 50px 0;
        background: linear-gradient(to bottom, #15569c 0%, #00a6e5 100%);
        position: relative;
    }

    .homeBanner .intro {
        color: #FFF;
        font-size: 34px;
        font-weight: 600;
        line-height: 130%;
    }

    @media (max-width: 768px) {

        .homeBanner .intro {
            text-align: center;
            font-size: 22px;
        }

        .homeBanner .tagline {
            text-align: center;
        }

    }

    .homeBanner .arrow {
        position: absolute;
        bottom: 36%;
        right: 28%;
    }

    @media (min-width: 990px) and (max-width: 1200px) {
        .homeBanner .arrow {
            transform: rotateZ(359deg) rotateX(0deg) rotateY(17deg);
            bottom: 9%;
            right: 5%;
        }
    }

    @media (max-width: 990px) {
        .homeBanner .arrow {
            transform: rotateZ(83deg) rotateX(161deg) rotateY(17deg);
            bottom: 1%;
            right: 5%;
        }
    }

    @media (max-width: 375px) {
        .homeBanner .arrow {
            width: 49px;
        }
    }

    @media (max-width: 320px) {

        .homeBanner .arrow {
            transform: rotateZ(83deg) rotateX(161deg) rotateY(14deg);
            bottom: 7%;
            right: 4%;
            width: 49px;
        }
    }
</style>
        @php
            if(isset($_GET["tevini_campaignid"]) && isset($_GET["acc"]) && isset($_GET["amt"]) && isset($_GET["hash"])) {
               $tevini_campaignid = $_GET["tevini_campaignid"];
                $transid = $_GET["transid"];
                $acc = $_GET["acc"];
                $amt = $_GET["amt"];
                $comment = $_GET["comment"];
                $thrdprty_hash = $_GET["hash"];
                $identify = 1;

                $campaign_dtls =\App\Models\Campaign::where('id',$tevini_campaignid)->first();
                if(!empty($campaign_dtls)){
                $mhash = "?campaign=".$tevini_campaignid."&transid=".$transid."&acc=".$acc."&amt=".$amt;
                $tevini_hash = hash_hmac("sha256", $mhash, $campaign_dtls->hash_code);
                }
            }elseif (isset($_GET["campaign"]) && isset($_GET["transid"]) && isset($_GET["cid"]) && isset($_GET["donation"]) && isset($_GET["hashed"])) {

                $tevini_campaignid = $_GET["campaign"];
                $transid = $_GET["transid"];
                $acc = $_GET["cid"];
                $amt = $_GET["donation"];
                $comment = " ";
                $thrdprty_hash = $_GET["hashed"];
                $identify = 2;

                $campaign_dtls =\App\Models\Campaign::where('id',$tevini_campaignid)->first();
                if(!empty($campaign_dtls)){
                $mhash = "?campaign=".$tevini_campaignid."&transid=".$transid."&cid=".$acc."&donation=".$amt;
                $tevini_hash = hash_hmac("sha256", $mhash, $campaign_dtls->hash_code);
            }
        }
        @endphp
<section class="homeBanner">
    <div class="container d-flex justify-content-center align-items-center">
        <div class="col-md-10 mx-auto">
    <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->
            <div class="row">
                @if(empty($campaign_dtls))

                <div class="col-lg-12 col-md-12 d-flex justify-content-center">
                    <div class="card bg-white rounded text-center d-flex align-items-center justify-content-center  p-5 w-100 shadow-lg "
                        style="min-height: 300px ;">
                        <div>
                            <h1 class="display-4" style="color: red">ERROR</h1>
                            <p>Sorry, Invalid campaign id.</p>
                        </div>
                    </div>
                </div>

                @elseif (hash_equals($tevini_hash, $thrdprty_hash))

                {{-- if link is ok  --}}
                <div class="col-lg-6 col-md-12 position-relative">
                    <div class="px-3 pb-5">
                        <h2 class=" intro mb-0 text-white">
                            You are now completing your
                            donation to  {{\App\Models\Campaign::where('id',$tevini_campaignid)->first()->campaign_title}} using funds in your
                            Tevini account.
                        </h2>
                        <h5 class="mt-3 tagline text-white">Charity: {{\App\Models\Charity::where('id',\App\Models\Campaign::where('id',$tevini_campaignid)->first()->charity_id)->first()->name}}</h4>
                        <img src="{{ asset('assets/image/arrow.png') }}" class="arrow" alt="">
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 d-flex justify-content-center">
                    <form action=" " method="post" class="d-flex justify-content-center">
                        <div class="bg-white rounded  p-5">
                            <div class="title text-muted text-center mb-4 fw-bold border-bottom pb-4">
                                ONE-OFF DONATION
                            </div>
                            <div class="form-group d-flex justify-content-between">
                                <label for="account-number " class="text-secondary fw-bold">Your account
                                    number:</label>
                                <span class="float-right text-secondary fw-bold">{{$acc}}</span>
                                <input type="text" id="acc" hidden value="{{$acc}}">
                            </div>

                            <div class="form-group text-info d-flex justify-content-between fw-bold my-3">
                                <label for="account-number" class="">Total donation amount</label>
                                <span class="float-right fw-bold"> {{$amt}} </span>
                                <input type="text" id="amt" hidden value="{{$amt}}">
                            </div>

                            <div class="form-group mb-4">
                                <label for="account-password" class="fw-bold text-secondary mb-2">Enter your
                                    password:</label>
                                <input type="password" id="password" class="form-control bg-default py-3" placeholder="Account Password">
                                <div class="ermsg"></div>
                            </div>

                            <small class="text-muted">
                                Please allow up to one week for your donation to be processed.
                            </small>

                            <input type="text" hidden id="tevini_campaignid" value="{{$tevini_campaignid}}">
                            <input type="text" hidden id="transid" value="{{$transid}}">
                            <input type="text" hidden id="comment" value="{{$comment}}">
                            <input type="text" hidden id="hash" value="{{$thrdprty_hash}}">
                            <input type="text" hidden id="identify" value="{{$identify}}">

                            <input type="button" id="apidonation" value="CONFIRM DONATION" class="btn btn-info mt-4 d-block w-100 fw-bold py-3 text-white">

                        </div>
                    </form>
                </div>
                {{-- if link is ok end  --}}
                @else
                <div class="col-lg-12 col-md-12 d-flex justify-content-center">
                    <div class="card bg-white rounded text-center d-flex align-items-center justify-content-center  p-5 w-100 shadow-lg "
                        style="min-height: 300px ;">
                        <div>
                            <h1 class="display-4" style="color: red">ERROR</h1>
                            <p>Sorry, Smonething went wrong.</p>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

@endsection
@section('script')
<script>
 $(document).ready(function () {
<<<<<<< Updated upstream


 //header for csrf-token is must in laravel
 $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

=======
>>>>>>> Stashed changes
            //  make mail start
            var url = "{{URL::to('/api')}}";
            var attemp_pass = 0;
            $("#apidonation").click(function(){

                    $("#apidonation").prop('disabled', true);
                    $("#loading").show();

                    var transid= $("#transid").val();
                    var acc= $("#acc").val();
                    var amt= $("#amt").val();
                    var tevini_campaignid= $("#tevini_campaignid").val();
                    var comment= $("#comment").val();
                    var password= $("#password").val();
                    var hash= $("#hash").val();
                    var identify= $("#identify").val();
                    // alert(hash);
                    $.ajax({
                        url: url,
                        method: "POST",
<<<<<<< Updated upstream
                        data: {transid,acc,amt,tevini_campaignid,comment,hash,password,identify},
=======
                        data: {
                            "_token": "{{ csrf_token() }}",
                            transid,acc,amt,tevini_campaignid,comment,hash,password
                        },
>>>>>>> Stashed changes
                        success: function (d) {

                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                                $("#loading").hide();
                                $("#apidonation").prop('disabled', false);
                            }else if(d.status == 301){
                                $("#loading").hide();
                                $(".ermsg").html(d.message);
                                attemp_pass+=1;
                                if(attemp_pass == "3"){
                                $("#loading").show();
                                window.setTimeout(function(){window.location.replace(d.url)},1000)
                                }
                                $("#apidonation").prop('disabled', false);
                            }else if(d.status == 300){
                                $("#loading").hide();
                                $(".ermsg").html(d.message);
                                window.setTimeout(function(){window.location.replace(d.url)},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });

            });
            // send mail end


});
</script>
@endsection
