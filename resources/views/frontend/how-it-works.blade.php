@extends('frontend.layouts.master')
@section('content')



<section class="about py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row py-5">
                    <div class="col-lg-12  px-3  ">
                        <div class="title mb-4">
                            Easy Giving
                        </div>
                        <p class="theme-para text-center fs-20">
                            Tevini is a platform that allows donors to simplify their <br> charitable giving by streamlining the process and offering tax <br> deductions, Gift Aid, and a bank-like online account to use for<br>  charitable endeavours.
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="  account default">
    <div class="container">
        <div class="row">
            <div class="title txt-secondary">
                Account Features
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="./images/account features-18 1.svg" alt="" class="my-4">
                        <div class="paratitle">Personal online <br> account</div>
                        <p class="theme-para">
                            You will receive an online account which <br> you can use to receive payments and <br> manage your donations.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="./images/account features-19 1.svg" alt="" class="my-4">
                        <div class="paratitle">Charity voucher <br> booklet</div>
                        <p class="theme-para">
                            You will be given a booklet of vouchers <br> which you can use to donate in person <br> to your chosen charities.

                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="./images/account features-20 1.svg" alt="" class="my-4">
                        <div class="paratitle">Monthly <br> statements</div>
                        <p class="theme-para">
                            You will be able to view your account  <br> balance and transactions online, and will <br> receive a monthly statement by email.


                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="./images/account features-23 1.svg" alt="" class="my-4">
                        <div class="paratitle">Automatic <br> recording</div>
                        <p class="theme-para">
                            Tevini records all donations for you, so <br> your charitable activities are all tracked, <br> documented and accounted for.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="./images/account features-22 1.svg" alt="" class="my-4">
                        <div class="paratitle">2,600+ <br> charities</div>
                        <p class="theme-para">
                            You can support over 2,600 charities <br> and Jewish organisations across the  <br>globe with your vouchers or online.

                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="./images/account features-21 1.svg" alt="" class="my-4">
                        <div class="paratitle">Optional <br> notifications</div>
                        <p class="theme-para">
                            You can choose to receive email or SMS <br> notification when your account is  <br>running low on funds.

                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<section class="client default" style="background: #DACFC2;">
    <div class="container">
        <div class="row">
            <div class="title ">
                The donation platform <br> that works for you
            </div>
            <div class="d-flex justify-content-center">
                <a href="{{ route('register') }}" class="btn-theme bg-secondary my-5">Open an account</a>
                <a href="{{ route('contact') }}" class="btn-theme bg-primary my-5">Contact us</a>
            </div>
        </div>
    </div>
</section>

<section class="why-donors default" style="background: #E9E1DA;">
    <div class="container">
        <div class="row">
            <div class="title">
                Account Perks
            </div>
        </div>
        <div class="row my-5">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Company</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Individual</button>
                </li>

              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">

                    <!-- tab content -->

                    <div class="row ">
                        <div class="col-lg-6 col-md-6 upperGap">
                            <div class="row">
                                <div class="col-lg-3">
                                    <img src="{{ asset('assets/front/images/1-number.svg') }}" class="arrow">
                                </div>
                                <div class="col-lg-9">
                                    <div class="paratitle txt-primary">Simple taxing</div>
                                    <p class="theme-para">
                                        You will have only one recipient, Tevini, on <br> your tax return, no matter how many <br> charities you support.
                                    </p>
                                    <a href="./register.html" class="btn-theme bg-primary btn-line border-primary txt-primary">Open your account</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 upperGap">
                            <div class="row">
                                <div class="col-lg-3">
                                    <img src="{{ asset('assets/front/images/2-number.svg') }}" class="arrow">
                                </div>
                                <div class="col-lg-9">
                                    <div class="paratitle txt-primary">
                                        Easy processing
                                    </div>
                                    <p class="theme-para">
                                        Increase your donation options with <br> charity vouchers which are easier for small <br> charities to process.


                                    </p>
                                    <a href="{{ route('register') }}" class="btn-theme bg-primary btn-line border-primary txt-primary">Join Tevini</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 upperGap">
                            <div class="row">
                                <div class="col-lg-3">
                                    <img src="{{ asset('assets/front/images/3-number.svg') }}" class="arrow">
                                </div>
                                <div class="col-lg-9">
                                    <div class="paratitle txt-primary">GiftAid
                                    </div>
                                    <p class="theme-para">
                                        For every £1 you donate, we can claim  <br> an additional 25p from the government,<br> increasing your donation to £1.25.


                                    </p>
                                    <a href="#" class="btn-theme bg-primary btn-line border-primary txt-primary">Join Tevini</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 upperGap">
                            <div class="row">
                                <div class="col-lg-3">
                                    <img src="{{ asset('assets/front/images/4-number.svg') }}" class="arrow">
                                </div>
                                <div class="col-lg-9">
                                    <div class="paratitle txt-primary">Flexible Giving

                                    </div>
                                    <p class="theme-para">
                                        You can choose to make donations <br> straight from your online account or by  <br> distributing your charitable vouchers.

                                    </p>
                                    <a href="#" class="btn-theme bg-primary btn-line border-primary txt-primary">Open your account</a>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    Other content goes here
                </div>

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
            <a href="#" class="mx-auto btn-theme bg-primary d-inline my-5"> Join Tevini </a>
        </div>
    </div>
</section>

@endsection

