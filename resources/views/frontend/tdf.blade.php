@extends('frontend.layouts.master')
@section('content')





    

<section class="bleesed default">
    <div class="container">
      <div class="row d-flex align-items-center justify-content-center">
        <div class="col-md-6 text-center">
          <img src="{{ asset('assets/front/images/overseas giving-29 1.svg') }}" class="custom-img" alt="Overseas Giving Image" />
        </div>
        <div class="col-md-6">
          <div class="container">
            <div class="title text-start txt-secondary">
              Give with ease overseas.
            </div>
            <br />
            <div class="title text-start txt-secondary fs-1 fw-bold">
              Streamline your UK and US donations,
            </div>

            <div class="para my-5">
              <p class="theme-para text-start">
                Maximize impact with charity aids, and avoid transaction
                charges with Tevini's user-friendly platform in partnership
                with The Donors' Fund.
              </p>
              <p class="theme-para text-start">The Donors' Fund.</p>
            </div>

            <div>
              <a href="{{route('register')}}" class="btn-theme bg-primary mx-2">Open your account</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bleesed default partnership">
    <div class="container">
      <div class="row d-flex align-items-center justify-content-center">
        <div class="title">A powerful partnership</div>
        <br />
        <div class="title">Tevini and The Donors Fund.</div>
        <div class="para text-center my-5 partnership-text">
          <p class="text-left">
            Tevini's innovative collaboration with The Donors' Fund opens new
            doors for seamless and cost-free donations in the US without
            compromising your tax rebates and charity aids.
          </p>
          <p class="text-left">
            Simply Create your Donors' Fund account and enjoy easy access
            through Tevini's all-inclusive platform. Transfer your money with
            a click of a button from your Tevini account to your Donors' fund
            account at no cost at all and start giving.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="about py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <div class="row py-5">
            <div class="col-lg-12 px-3">
              <div class="title mb-4">Effortless giving, globally</div>
              <p class="theme-para text-center fs-20">
                Receive gift aid for every eligible donation you make,
                including those <br />
                made through your Donors' Fund account- ensuring a 25%
                increase in<br />impact.
              </p>
              <br />
              <p class="theme-para text-center fs-20">
                Sign up with a click of a button to access your own Donors'
                Fund portal<br />
                through Tevini's all-inclusive platform. Simplify your giving,
                manage all<br />transactions in one place, and enjoy member's
                benefits.
              </p>
            </div>
          </div>
        </div>
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
                From set <br />
                up to all <br />
                set
              </div>
              <img src="{{ asset('assets/front/images/right-arrow-01.svg') }}" alt="" class="w-50 my-5" />
            </div>
            <div class="col-lg-6 ranksAddjust">
              <div class="row my-3">
                <div class="col-lg-4">
                  <img src="{{ asset('assets/front/images/1.svg') }}" alt="" />
                </div>
                <div class="col-lg-8 pt-5">
                  <div class="paratitle">
                    Create your Donors' <br />
                    Fund account
                  </div>
                  <p class="theme-para">
                    This is a separate account linked<br />
                    to your Tevini account with access to your funds.
                  </p>
                </div>
              </div>
              <div class="row my-3">
                <div class="col-lg-4">
                  <img src="{{ asset('assets/front/images/2.svg') }}" alt="" />
                </div>
                <div class="col-lg-8 pt-5">
                  <div class="paratitle">
                    Transfer desired <br />
                    donation
                  </div>
                  <p class="theme-para">
                    Transfer desired funds from your <br />
                    Tevnini account to your Donors' <br />
                    Fund account.
                  </p>
                </div>
              </div>
              <div class="row my-3">
                <div class="col-lg-4">
                  <img src="{{ asset('assets/front/images/3.svg') }}" alt="" />
                </div>
                <div class="col-lg-8 pt-5">
                  <div class="paratitle">
                    Log in to your Donors' <br />Fund account
                  </div>
                  <p class="theme-para">
                    Each time you want to donate in the<br />
                    US, Login to your Donors' Fund account <br />
                    and access your transferred funds.<br />
                  </p>
                </div>
              </div>
              <div class="row my-3">
                <div class="col-lg-4">
                  <img src="{{ asset('assets/front/images/4.svg') }}" alt="" />
                </div>
                <div class="col-lg-8 pt-5">
                  <div class="paratitle">Give big</div>
                  <p class="theme-para">
                    Choose where you want your <br />
                    money to go and maximise every <br />
                    donation you make. <br />
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bleesed default easy-giving">
    <div class="container">
      <div class="row d-flex align-items-start justify-content-center mt-4">
        <div class="col-md-8">
          <div class="title">
            Easy giving has just gotten <br />
            easier, smarter, better
          </div>
          <div class="para text-left my-5"></div>
          <div class="inner w-90 text-center">
            <div>
              <a href="{{route('register')}}" class="btn-theme bg-secondary mx-2"
                >Open an account</a
              >
              <a href="{{route('register')}}" class="btn-theme bg-primary mx-2">Get in touch</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="help default">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6 upperGap">
          <div class="row">
            <div class="col-lg-4">
              <img src="{{ asset('assets/front/images/donors fund benefits-30 1.svg') }}" class="arrow" />
            </div>
            <div class="col-lg-8">
              <div class="paratitle">
                Simple money <br />
                transfers
              </div>
              <p class="theme-para">
                It takes seconds to transfer money from <br />
                your Tevini account to your Donors' fund <br />
                account.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 upperGap">
          <div class="row">
            <div class="col-lg-4">
              <img src="{{ asset('assets/front/images/donors fund benefits-31 1.svg') }}" class="arrow" />
            </div>
            <div class="col-lg-8">
              <div class="paratitle">
                Stretch every <br />
                donation
              </div>
              <p class="theme-para">
                Minimise your tax bill and maximise the <br />
                for every eligible donation you make even in <br />
                the US, so that your donations increase by <br />
                25%.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 upperGap">
          <div class="row">
            <div class="col-lg-4">
              <img src="{{ asset('assets/front/images/donors fund benefits-32 1.svg') }}" class="arrow" />
            </div>
            <div class="col-lg-8">
              <div class="paratitle">
                All-inclusive <br />
                platform
              </div>
              <p class="theme-para">
                Manage your donations in one simplified <br />
                portal. You'll see your Donors' Fund <br />
                transfer on your Tevini statement and all <br />
                American transactions on your Donors' <br />
                Fund statement.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 upperGap">
          <div class="row">
            <div class="col-lg-4">
              <img src="{{ asset('assets/front/images/donors fund benefits-33 1.svg') }}" class="arrow" />
            </div>
            <div class="col-lg-8">
              <div class="paratitle">
                Pay in <br />
                pounds
              </div>
              <p class="theme-para">
                Tevini automatically applies the current <br />
                exchange rate so you can pay in pounds <br />
                and donate in dollars.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div style="display: flex; justify-content: center; align-items: center; height: 100px;" >
        <a href="#" class="btn-theme bg-primary btn-line" style="margin: auto">I want to make my life easier</a>
      </div>
    </div>
  </section>

  <section class="client faq default">
    <div class="container">
      <div class="row">
        <div class="title txt-secondary">Frequently asked questions:</div>
        <br />
        <div class="mt-5">
          <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <div class="accordion-button d-flex align-items-center">
                  What is Tevini's partnership with the Donors' Fund?
                </div>
              </h2>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <div class="accordion-button d-flex align-items-center">
                  How do I transfer funds to my Donors' Fund account?
                </div>
              </h2>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <div class="accordion-button d-flex align-items-center">
                  How does Tevini help in donating overseas?
                </div>
              </h2>
            </div>

            <!-- issue -->

            <div class="accordion-item">
              <div class="accordion-header" >
                <div class="accordion-button d-flex align-items-center">
                  What currencies can I use for donations?
                </div>
                <div class="accordion-body">
                  Tevini allows you to pay in pounds while automatically
                  applying the current exchange rate, making it convenient to
                  donate in dollars. <br />
                  This feature simplifies the donation process and ensures
                  that your contributions reach their intended destinations
                  without hassle.
                </div>
              </div>
            </div>

            <!-- issue -->

            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <div class="accordion-button d-flex align-items-center">
                  How does Tevini handle currency exchange for donations?
                </div>
              </h2>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <div class="accordion-button d-flex align-items-center">
                  How does Tevini handle currency exchange for donations?
                </div>
              </h2>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <div class="accordion-button d-flex align-items-center">
                  What benefits does Tevini offer?
                </div>
              </h2>
            </div>
          </div>
        </div>
        <div class="w-100 d-flex align-items-center justify-content-center">
          <a href="#" class="mx-auto mt-5 btn-theme bg-primary btn-line"
            >Ask another question</a
          >
        </div>
      </div>
    </div>
  </section>

  <section class="client default">
    <div class="container">
      <div class="row">
        <div class="title txt-secondary">
          Join over 1,000 satisfied <br />
          clients.
        </div>
        <a href="#" class="mx-auto btn-theme bg-primary d-inline my-5">
          Join Tevini
        </a>
      </div>
    </div>
  </section>






@endsection

