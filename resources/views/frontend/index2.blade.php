@extends('frontend.layouts.master')
@section('content')
<style>
    #riskfloater {
      background-color: green;
      left: 10px;
      position: fixed;
      padding: 8px 16px;
      background: green;
      color: #ffffff;
      cursor: pointer;
      bottom: 10px;
      z-index: 2;
      // -moz-border-radius: 3px;
      // -webkit-border-radius: 3px;
      // border-radius: 3px;
  }

  #cookiebar {
      position: fixed;
      bottom: 0;
      left: 5px;
      right: 5px;
      display: none;
      z-index: 200;
  }

      a{
          color: white;
          text-decoration: none;
      }



  #cookiebarBox {
      position: fixed;
      bottom: 0;
      left: 5px;
      right: 5px;
      // display: none;
      z-index: 200;
  }
  .containerrr {
      border-radius: 3px;
      background-color: white;
      color: #626262;
      margin-bottom: 10px;
      padding: 10px;
      overflow: hidden;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      position: fixed;
      padding: 20px;
      background-color: #fff;
      bottom: -10px;
      width: 100%;
      -webkit-box-shadow: 2px 2px 19px 6px #00000029;
      box-shadow: 2px 2px 19px 6px #00000029;
      border-top: 1px solid #356ffd1c;
  }



  .cookieok {
      -moz-border-radius: 3px;
      -webkit-border-radius: 3px;
      border-radius: 3px;
      background-color: #e8f0f3;
      color: #186782 !important;
      font-weight: 600;
      // float: right;
      line-height: 2.5em;
      height: 2.5em;
      display: block;
      padding-left: 30px;
      padding-right: 30px;
      border-bottom-width: 0 !important;
      cursor: pointer;
      max-width: 200px;
      margin: 0 auto;

  }
  </style>

<section class="introBanner">
    <div class="row" style="margin-right:0px;">

        <div class="col-md-5 px-3 mx-auto text-center f-flex align-items-center justify-content-center flex-column">

           <img src="{{ asset('assets/front/images/home-icon.png') }}" alt="Home Icon" class="img-fluid">

        </div>
        <div class="col-md-5 align-middle bannerRight">
            <h1 style="font-size:5rem">The gift</h1>
            <h1 style="font-size:5rem">of giving</h1>
            <br>
            <p>Give smart</p>
            <br>
           <a href="{{ route('login') }}" class="btn loginBtn">Log In</a>
           <a href="{{ route('register') }}" class="btn regBtn">Register</a>
        </div>
    </div>

{{-- for cokkies  --}}

<div id="cookiebarBox" class="os-animation" data-os-animation="fadeIn" >
    <div class="containerrr risk-dismiss " style="display: flex;" >
          <div class="container">
            <div class="row">
                <div class="col-md-9">
                <p class="text-left">
               <h1 class="d-inline text-primary"><span class="iconify" data-icon="iconoir:half-cookie"></span> </h1>
               {{-- {{ App\Models\Cookie::where('id','=', 1)->first()->description }} --}}
               Like most websites, this site uses cookies to assist with navigation and your ability to provide feedback, analyse your use of products and services so that we can improve them, assist with our personal promotional and marketing efforts and provide consent from third parties.
            </p>

                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-center">
                    <a id="cookieBoxok" class="btn btn-sm btn-primary my-3 px-4 text-center" data-cookie="risk">Accept</a>
                </div>
            </div>
          </div>
    </div>
</div>




</section>
@endsection
@section('script')

<script>
// if you want to see a cookie, delete 'seen-cookiePopup' from cookies first.

jQuery(document).ready(function($) {
   // Get CookieBox
  var cookieBox = document.getElementById('cookiebarBox');
	// Get the <span> element that closes the cookiebox
  var closeCookieBox = document.getElementById("cookieBoxok");
    closeCookieBox.onclick = function() {
        cookieBox.style.display = "none";
    };
});

(function () {

    /**
     * Set cookie
     *
     * @param string name
     * @param string value
     * @param int days
     * @param string path
     * @see http://www.quirksmode.org/js/cookies.html
     */
    function createCookie(name, value, days, path) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        else expires = "";
        document.cookie = name + "=" + value + expires + "; path=" + path;
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Set/update cookie
    var cookieExpiry = 30;
    var cookiePath = "/";

    document.getElementById("cookieBoxok").addEventListener('click', function () {
        createCookie('seen-cookiePopup', 'yes', cookieExpiry, cookiePath);
    });

    var cookiePopup = readCookie('seen-cookiePopup');
    if (cookiePopup != null && cookiePopup == 'yes') {
        cookiebarBox.style.display = 'none';
    } else {
        cookiebarBox.style.display = 'block';
    }
})();

</script>
@endsection


