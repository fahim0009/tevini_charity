@extends('frontend.layouts.master')
@section('content')

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

</section>

<!--<section class="feature">-->
<!--    <div class="container">-->
<!--        <div class="row">-->
<!--           <div class="col-md-12 mx-auto text-center">-->
<!--              <img src="{{ asset('assets/front/images/top-mockups.png') }}" class="img-fluid">-->
<!--               <h2 class="theme-color text-uppercase">Lorem Ipsum is simply dummy text will ?</h2>-->
<!--               <p class="text-muted">Lorem Ipsum is simply dummy text of the</p>-->
<!--           </div>-->
<!--           <div class="col-md-12 mt-5">-->
<!--            <ul class="nav nav-tabs custom" id="myTab" role="tablist">-->
<!--                <li class="nav-item" role="presentation">-->
<!--                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">What is Lorem Ipsum</button>-->
<!--                </li>-->
<!--                <li class="nav-item" role="presentation">-->
<!--                  <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">What is Lorem Ipsum</button>-->
<!--                </li>-->
<!--                <li class="nav-item" role="presentation">-->
<!--                  <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">What is Lorem Ipsum</button>-->
<!--                </li>-->
<!--              </ul>-->
<!--              <div class="tab-content" id="myTabContent">-->
<!--                <div class="tab-pane fade show active py-4 px-3" id="home" role="tabpanel" aria-labelledby="home-tab">-->
                    
<!--                    <div class="row">-->
<!--                        <div class="col-md-3 text-center my-4">-->
<!--                            <span class="iconify display-1 text-info mb-1" data-icon="clarity:block-line"></span>-->
<!--                            <h5 class="theme-color">Lorem Ipsum</h5>-->
<!--                            <p>is simply dummy text of the printing and typesetting industry.</p>-->
<!--                        </div> -->
<!--                        <div class="col-md-3 text-center my-4">-->
<!--                            <span class="iconify display-1  text-info mb-1" data-icon="emojione-v1:ballot-box-with-ballot"></span>-->
                            
<!--                            <h5 class="theme-color">Lorem Ipsum</h5>-->
<!--                            <p>is simply dummy text of the printing and typesetting industry.</p>-->
<!--                        </div> -->
<!--                        <div class="col-md-3 text-center my-4">-->
                            
<!--                            <span class="iconify display-1  text-info mb-1" data-icon="flat-ui:box"></span>-->
<!--                            <h5 class="theme-color">Lorem Ipsum</h5>-->
<!--                            <p>is simply dummy text of the printing and typesetting industry.</p>-->
<!--                        </div> -->
<!--                        <div class="col-md-3 text-center my-4">-->
<!--                            <span class="iconify display-1  text-info mb-1" data-icon="emojione-v1:card-file-box"></span> -->
<!--                            <h5 class="theme-color">Lorem Ipsum</h5>-->
<!--                            <p>is simply dummy text of the printing and typesetting industry.</p>-->
<!--                        </div> -->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="tab-pane fade  py-3 px-3" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>-->
<!--                <div class="tab-pane fade  py-3 px-3" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>-->
<!--              </div>-->
<!--           </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
@endsection


