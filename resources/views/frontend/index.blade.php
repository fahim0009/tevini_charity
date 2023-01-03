@extends('frontend.layouts.home')
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



<section class="banner py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row">
                    <div class="col-lg-6 d-flex align-items-center">
                        <img src="{{ asset('assets/front/images/hero image jewish.svg') }}" class="img-fluid mx-auto" alt="">
                    </div>
                    <div class="col-lg-6 d-flex align-items-center justify-content-center">
                        <div class="inner w-75">
                            <div class="intro-title">
                                Easier Giving
                            </div>
                            <p class="txt-theme mb-4">The Charity voucher account that <br> allows you to give more
                                charity <br> with less paperwork,<br> less tax and less stress.</p>
                            <div>
                            <a href="{{ route('register') }}" class="btn-theme bg-secondary">Open an account</a>
                            <a href="{{ route('howitWorks') }}" class="btn-theme bg-primary">How it works</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- massas calcluter  --}}
<section class="bleesed default">
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center">

            <div class="title">Give and feel blessed.</div>
            <div class="para text-center my-5">
                <p> Taxes, claiming and paperwork can suck the joy out of being on the giving end.</p>
                <p>
                    At Tevini, we bring you a charity account using a system that is efficient, accountable and
                    <br> completely stress free.
                </p>
                <p>
                    If you're looking to put the joy back into opening your pocket, writing a voucher and piling up
                    <br>
                    your merits with nothing else to worry about, Tevini is for you.
                </p>
            </div>
            <img src="{{ asset('assets/front/images/down-arrow-01.svg') }}" class="arrow">

        </div>
    </div>
</section>

<section class="help default">
    <div class="container">
        <div class="row">
            <div class="title">
                Tevini helps you <br> to help others.
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/tevini helps you to help others-02 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Simple</div>
                        <p class="theme-para">
                            Tevini is the simplest way to manage your charity. With a bank-like set-up you can
                            easily make payments straight from your account. You can get paid directly into your
                            account too.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Get started</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/tevini helps you to help others-05 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Safe</div>
                        <p class="theme-para">
                            Tevini makes it easy to manage your charity and keep track of your giving. Our system is
                            designed to meet the highest standards of transparency and accountability.


                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/tevini helps you to help others-04 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Supportive</div>
                        <p class="theme-para">
                            We understand that charity accounts and legislation can be confusing and overwhelming.
                            We make it our priority to always be available for you and provide guidance every step
                            of the way.


                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/tevini helps you to help others-03 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Give more</div>
                        <p class="theme-para">
                            Tevini account gives you the opportunity to minimise your tax bill and maximise the
                            amount of money you give. We claim gift aid for your donations, so they increase by 25%.

                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Get started</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<section class="bleesed default">
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center">
            <img src="{{ asset('assets/front/images/tevini helps you to help others-03 1.svg') }}" class="w-25">
            <div class="title"><span class="txt-primary">New!</span> The Tevini Ma’aser <br> Calculator:</div>
            <div class="para text-center my-5 w-75">
                <p> The Tevini Ma’aser Calculator provides an innovative way for all Tevini account holders to
                    calculate their charity payments. All you have to do is input your income and the Tevini
                    calculator will do the maths for you. You can deduct manually for donations that weren’t made
                    through Tevini.
                </p>
                <p>
                    Sign up for an account and get instant access to the calculator!
                </p>
                <p>
                    Note: Admin will have no access to these numbers keeping your charitable donations completely
                    confidential.
                </p>

            </div>
            <div class="title"><span class="txt-primary">Your Options:</span> </div>
            <ul class="list-theme">
                <li> Weekly recurring payments</li>
                <li> Monthly recurring payments</li>
                <li> One off payments</li>
                <li> 10% (ma’aser) </li>
                <li> 20% (chomesh)</li>
                <li> Other amount</li>

            </ul>

        </div>
    </div>
</section>


<section class="ranks default">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="title text-start txt-secondary">
                            Join the <br> ranks of <br> easy giving
                        </div>
                        <img src="{{ asset('assets/front/images/right-arrow-01.svg') }}" alt="" class="w-50 my-5">
                    </div>
                    <div class="col-lg-6 ranksAddjust">
                        <div class="row my-3">
                            <div class="col-lg-4">
                                <img src="{{ asset('assets/front/images/1.svg') }}" alt="">
                            </div>
                            <div class="col-lg-8 pt-5">
                                <div class="paratitle">Sign up</div>
                                <p class="theme-para">
                                    Sign up for our<br> account by filling a<br> short form.
                                </p>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-lg-4">
                                <img src="{{ asset('assets/front/images/2.svg') }}" alt="">
                            </div>
                            <div class="col-lg-8 pt-5">
                                <div class="paratitle">Transfer</div>
                                <p class="theme-para">
                                    Transfer £100 to <br> jumpstart your Tevini <br> account.

                                </p>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-lg-4">
                                <img src="{{ asset('assets/front/images/3.svg') }}" alt="">
                            </div>
                            <div class="col-lg-8 pt-5">
                                <div class="paratitle">Start giving</div>
                                <p class="theme-para">
                                    Start giving the Tevini <br> way; more money, less <br> stress.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="together default">
    <div class="container">
        <div class="row">
            <div class="title">
                Together, we make the <br> world a better place.

            </div>
        </div>
        <br>
        <br>
        <div class="row mt-5">
            <div class="col-lg-3 col-md-6 upperGap">
                <div class="paratitle">0</div>
                <p class="theme-para">
                    Paperwork and administrative hassle
                </p>
            </div>
            <div class="col-lg-3 col-md-6 upperGap">
                <div class="paratitle">25%</div>
                <p class="theme-para">
                    Donation increase with Gift Aid
                </p>
            </div>
            <div class="col-lg-3 col-md-6 upperGap">
                <div class="paratitle">2600+</div>
                <p class="theme-para">
                    Potential reputable charity recipients

                </p>
            </div>
            <div class="col-lg-3 col-md-6 upperGap">
                <div class="paratitle">100%</div>
                <p class="theme-para">
                    Donor and charity satisfaction

                </p>
            </div>


        </div>
    </div>
</section>

<section class="client default ">
    <div class="container">
        <div class="row">
            <div class="title txt-secondary">
                Join over 1,000 satisfied <br> clients.
            </div>
            <a href="{{ route('register') }}" class="mx-auto btn-theme bg-primary d-inline my-5"> Join Tevini </a>
        </div>
    </div>
</section>

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


