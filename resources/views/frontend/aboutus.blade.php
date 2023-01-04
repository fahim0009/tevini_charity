@extends('frontend.layouts.home')
@section('content')




<section class="about py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row">
                    <div class="col-lg-6  px-3 flex-wrap d-flex align-items-center flex-column">
                        <div class="title text-start txt-secondary">
                            We care
                            about Charity.
                        </div>
                        <img src="{{ asset('assets/front/images/about top image 1.svg') }}" class="mt-5 img-fluid mx-auto" alt="">
                    </div>
                    <div class="col-lg-6  px-3">
                        <div class="mb-4">
                            <div class="paratitle">You care too </div>
                            <p class="theme-para">
                                As a responsible donor, you want to make sure your hard-earned money goes as far as
                                possible to support the causes you care about. In fact, we care so much that all
                                Tevini
                                profits go to charity. But with the administrative hassle and paperwork involved in
                                making donations, it's easy to let the process overwhelm you.
                            </p>
                        </div>
                        <div class="mb-4">
                            <div class="paratitle">Making it easy </div>
                            <p class="theme-para">
                                At Tevini, we excel at customer service and take care of all the tedious legalities
                                for you, so you can focus on what matters most: supporting the charities that are
                                important to you. We work with a large range of reputable organizations, both big
                                and small, so you have unlimited options for giving.
                            </p>
                            <p class="theme-para">

                                We're a registered nonprofit ourselves, so you can be confident that your donation
                                is tax-deductible. Plus, our streamlined donation process makes it easy for you to
                                keep track of your giving and get the most tax deductions for your charitable
                                donations.


                            </p>
                            <p class="theme-para">
                                The best part is that we do it for you. All you need to do is sign up and start
                                giving. And because we’ve been around for so long, we're experts at Gift Aid too, so
                                you can be confident that your money is doing the most good possible.
                            </p>
                        </div>
                        <div class="mb-4">
                            <div class="paratitle">The best of all worlds </div>
                            <p class="theme-para">
                                With Tevini, you benefit from the flexibility of a local provider and the
                                professionalism of experts who have been helping people with their charity accounts
                                for many years. You can be sure your donations are handled efficiently, effectively,
                                and with the utmost care.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="client default ">
    <div class="container">
        <div class="row">
            <div class="title txt-secondary">
                A charity donation system <br> built for your needs.
            </div>
            <div class="d-flex justify-content-center">
                <a href="#" class="btn-theme bg-secondary my-5">Learn more </a>
                <a href="{{ route('register') }}" class="btn-theme bg-primary my-5">Get started </a>
            </div>
        </div>
    </div>
</section>




<section class="stage default">
    <div class="container">
        <div class="row">
            <div class="title">
                Tevini does good <br> at every stage:

            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="{{ asset('assets/front/images/tevini DOES GOOD AT EVERY STAGE-08 1.svg') }}" alt="" class="my-4">
                        <div class="paratitle">Service for Donors</div>
                        <p class="theme-para">
                            Tevini donors enjoy personalised service <br> with quick response times.
                        </p>
                        <p class="theme-para fw-bold"
                            style=" font-style: italic; color: #003057; font-family: 'Roboto-Bold';">
                            We have a helpful and informative <br> customer service team.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="{{ asset('assets/front/images/tevini DOES GOOD AT EVERY STAGE-10 1.svg') }}" alt="" class="my-4">
                        <div class="paratitle">Benefits for Charities</div>
                        <p class="theme-para">
                            Charities receive their donations in a <br> timely and efficient manner.
                        </p>
                        <p class="theme-para fw-bold"
                            style=" font-style: italic; color: #003057; font-family: 'Roboto-Bold';">
                            We offer rapid BACS payment for all <br> donations received.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <img src="{{ asset('assets/front/images/tevini DOES GOOD AT EVERY STAGE-09 1.svg') }}" alt="" class="my-4">
                        <div class="paratitle">Profiting our Community</div>
                        <p class="theme-para">
                            Tevini donates all its profits back to the <br> local community.
                        </p>
                        <p class="theme-para fw-bold"
                            style=" font-style: italic; color: #003057; font-family: 'Roboto-Bold';">
                            Tevini donates all its profits back to the <br> local community.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<section class="client faq default ">
    <div class="container">
        <div class="row">
            <div class="title txt-secondary">
                Frequently asked questions:
            </div>
            <br>
            <div class="mt-5">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How do you charge and how much?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora quos fugiat nostrum voluptas quas laboriosam explicabo harum illo deleniti cupiditate optio hic iure, quae officia.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                How can I check how much money I have in my charity account?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora quos fugiat nostrum voluptas quas laboriosam explicabo harum illo deleniti cupiditate optio hic iure, quae officia.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Can I donate to charities abroad with my Tevini account?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora quos fugiat nostrum voluptas quas laboriosam explicabo harum illo deleniti cupiditate optio hic iure, quae officia.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                When will my donation reach the recipient?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora quos fugiat nostrum voluptas quas laboriosam explicabo harum illo deleniti cupiditate optio hic iure, quae officia.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                What is GiftAid and how does it work?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Gift aid is an arrangement between the government and charities in the United
                                Kingdom where charities can claim back basic rate tax on donations from qualifying
                                donors. This means that for every pound donated by a qualifying donor, the charity
                                can reclaim 25p from HMRC.

                                For example, if somebody donates £100 to a charity, the charity can reclaim an
                                additional £25 from HM Revenue and Customs (HMRC), making the total value of the
                                donation £125.

                                The system works by the taxpayer completing a self-assessment form (known as a Gift
                                Aid declaration) which authorises the charity to reclaim tax on their behalf. The
                                money is then paid back to the charity by HMRC.

                                At Tevini, we’ll do that for you. You just need to open an account and we’ll make
                                sure that your donation is increased by 25%.

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100 d-flex align-items-center justify-content-center">
                <a href="#" class="mx-auto mt-5 btn-theme bg-primary btn-line">Ask another question</a>
            </div>
        </div>
    </div>
</section>

<section class="  why-donors default">
    <div class="container">
        <div class="row">
            <div class="title">
                Why donors choose <br> Tevini
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/flying coins-15 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Low admin <br> costs</div>
                        <p class="theme-para">
                            Our admin costs are less than 1%. Your donations go directly to Jewish educational
                            charities.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/flying coins-14 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Easy to use <br>
                            online account
                        </div>
                        <p class="theme-para">
                            You can check your balance, view transactions since your last statement and make
                            requests via our website.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/flying coins-13 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Efficient & <br> personal service
                        </div>
                        <p class="theme-para">
                            One on one support along with fast processing of all requests makes donating easy and
                            hassle-free.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 upperGap">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="{{ asset('assets/front/images/flying coins-12 1.svg') }}" class="arrow">
                    </div>
                    <div class="col-lg-8">
                        <div class="paratitle">Low balance br notifications

                        </div>
                        <p class="theme-para">
                            We offer Smartphone alerts to remind you of low balances or other important information.
                        </p>
                        <a href="#" class="btn-theme bg-primary btn-line">Learn more</a>
                    </div>
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




{{--
<section class="introBanner">
    <div class="col-md-12 ">
        <div class="col-md-6 px-3 mx-auto text-center f-flex align-items-center justify-content-center flex-column">
            <h1>About us</h1>
            <p>{{\App\Models\AboutContent::first()->title1}}</p>
        </div>
    </div>
</section>
<section class="translate">
    <div class="col-md-12 ">
        <div class="col-md-6  text-muted mx-auto text-center shadow-lg rounded bg-white p-5">
            {{\App\Models\AboutContent::first()->title2}}
        </div>
    </div>
</section>
<section class=" ">
    <div class="container">
        <div class="col-md-12">
            <h4 class="text-center theme-color text-uppercase">How We works</h4>
        </div>
        <div class="col-md-12 mt-4">
            <div class="row">



                @foreach ( \App\Models\AboutHelp::get() as $data)
                    <div class="col-md-4 text-center my-4">
                        <div class="  shadow-sm p-3">
                            <span class="iconify display-1 text-info mb-1" data-icon="bx:bxs-donate-blood"></span>

                            <h5 class="theme-color mb-2">{{ $data->title }}</h5>
                            <p class="text-muted">{{ $data->description }}</p>
                        </div>
                    </div>
                @endforeach





            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="col-md-12 py-4">
                <div class="col-md-6 px-3 mx-auto text-center f-flex align-items-center justify-content-center flex-column">
                    <h4 class="theme-color"> Reports & activities </h4>
                    <p class="text-muted">{{\App\Models\AboutContent::first()->title3}}</p>
                </div>
            </div>
        </div>
    </div>
</section> --}}


@endsection


