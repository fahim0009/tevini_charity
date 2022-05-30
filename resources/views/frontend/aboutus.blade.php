@extends('frontend.layouts.master')
@section('content')
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
            <!--<div class="row">-->
            <!--    <div class="col-md-6 text-center my-4">-->
            <!--        <h6>Turnover</h6>-->
            <!--        <p class="text-muted">-->
            <!--            {{\App\Models\AboutContent::first()->turnover_title}}-->
            <!--            <img class="my-5" src="https://aponlab.com/project/charity/public/assets/image/gif/graph-01b.gif" alt="">-->
            <!--        </p>-->
            <!--    </div>-->
            <!--    <div class="col-md-6 text-center my-4">-->
            <!--        <h6>Profits</h6>-->
            <!--        <p class="text-muted">-->
            <!--            {{\App\Models\AboutContent::first()->profit_title}}-->
            <!--            <img class="my-5" src="https://aponlab.com/project/charity/public/assets/image/gif/graph-02b.gif" alt="">-->
            <!--        </p>-->
            <!--    </div>-->


            <!--</div>-->
        </div>
    </div>
</section>
@endsection


