@extends('frontend.layouts.master')

@section('content')

<style>

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
            if(isset($_GET["aac_campaignid"]) && isset($_GET["acc"]) && isset($_GET["amt"]) && isset($_GET["hash"])) {

                $aac_campaignid = $_GET["aac_campaignid"];
                $acc = $_GET["acc"];
                $amt = $_GET["amt"];
                $comment = $_GET["comment"];
                $hash = $_GET["hash"];

            $mhash = "?aac_campaignid=".$aac_campaignid."&acc=".$acc."&amt=".$amt."&comment=".$comment;
            $dhash = hash_hmac("sha256", $mhash,"5c72d1");
            echo $dhash."<br>";
            echo "$hash";
            }



        @endphp
<section class="homeBanner">
    <div class="container d-flex justify-content-center align-items-center">
        <div class="col-md-10 mx-auto">
            <div class="row">
                <div class="col-lg-6 col-md-12 position-relative">
                    <div class="px-3 pb-5">
                        <h2 class=" intro mb-0 text-white">
                            You are now completing your <br>
                            donation to Help Taliban <br>
                            Targets using funds in your <br>
                            AAC account.
                        </h2>
                        <h5 class="mt-3 tagline text-white">Charity: Manchester <br> Jewish Philanthropic</h4>
                            <img src="./images/arrow.png" class="arrow" alt="">
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

                            <input type="text" hidden id="aac_campaignid" value="{{$aac_campaignid}}">
                            <input type="text" hidden id="comment" value="{{$comment}}">
                            <input type="text" hidden id="hash" value="{{$hash}}">

                            <button type="button" id="apidonation" class="btn btn-info mt-4 d-block w-100 fw-bold py-3 text-white">
                                CONFIRM DONATION
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
@section('script')
<script>
     $(document).ready(function () {


 //header for csrf-token is must in laravel
 $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            //  make mail start
            var url = "{{URL::to('/api')}}";
            $("#apidonation").click(function(){

                    var acc= $("#acc").val();
                    var amt= $("#amt").val();
                    var aac_campaignid= $("#aac_campaignid").val();
                    var comment= $("#comment").val();
                    var password= $("#password").val();
                    var hash= $("#hash").val();
                    // alert(hash);
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {acc,amt,aac_campaignid,comment,hash,password},
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
            // send mail end


});
</script>
@endsection
