@extends('frontend.layouts.master')

@section('content')



<section class="contact py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="title mb-5">
                            Contact us today:
                        </div>
                        <div class="theme-para ">
                            Fill out the form below and we’ll get back to you as   soon as we can.
                        </div>
                        <form action="" class="form-custom"> 
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Name"> 
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Email"> 
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Subject"> 
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" rows="3" placeholder="Message"></textarea> 
                            </div>
                            <div class="form-group">
                                <a href="#" class="btn-theme bg-primary">Send</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-8 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/images/contact page top 1.svg') }}" alt="" class="w-100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="default contactInfo">
    <div class="container">
        <div class="row ">
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Phone</div>
                <p class="theme-para text-center">  07490 956 227  </p>
                <a href="#" class="btn-theme bg-primary btn-line">Call</a>
            </div>
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Whatsapp</div>
                <p class="theme-para text-center">  07490 956 227  </p>
                <a href="#" class="btn-theme bg-primary btn-line">Message</a>
            </div>
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Email</div>
                <p class="theme-para text-center"> info@tevini.co.uk  </p>
                <a href="#" class="btn-theme bg-primary btn-line">Email</a>
            </div>
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Address</div>
                <p class="theme-para text-center"> 5a Holmdale Terrace <br>
                    London N15 6PP</p>
                <a href="#" class="btn-theme bg-primary btn-line">Visit</a>
            </div>
            
        </div>
    </div>
</section>
<section class="platform default">
    <div class="container-fluid">
        <div class="row">
            <div class="title">
                A platform that givers deserve.
            </div>
        </div>
        <br>
        <br>
        <div class="row my-5">
            <div class="col-lg-1 col-md-6 position-relative">  </div> 
                
           
            <div class="col-lg-2 col-md-6 position-relative"> 
                <img src="{{ asset('assets/front/images/1-1.svg') }}" alt="" class="numbering">
                <p class="paratitle">
                    Full <br> support
                </p>
            </div> 
            <div class="col-lg-2 col-md-6 position-relative"> 
                <img src="{{ asset('assets/front/images/2.svg') }}" alt="" class="numbering">
                <p class="paratitle">
                    Easy to  <br>use  
                </p>
            </div> 
            <div class="col-lg-2 col-md-6 position-relative"> 
                <img src="{{ asset('assets/front/images/3.svg') }}" alt="" class="numbering">
                <p class="paratitle">
                    Less tax  <br>deduction  
                </p>
            </div> 
            <div class="col-lg-2 col-md-6 position-relative"> 
                <img src="{{ asset('assets/front/images/4.svg') }}" alt="" class="numbering">
                <p class="paratitle">
                    Maximised  <br>donations  
                </p>
            </div> 
            <div class="col-lg-2 col-md-6 position-relative"> 
                <img src="{{ asset('assets/front/images/5.svg') }}" alt="" class="numbering">
                <p class="paratitle">
                    Financial  <br>control  
                </p>
            </div> 
            <div class="col-lg-1 col-md-6 position-relative">  </div> 
        </div>
        <div class="row">
            <a href="#" class="btn-theme   mx-auto bg-secondary">Open your account</a>
        </div>
    </div>
</section>


{{-- 
<section class="introBanner py-5">
    <div class="col-md-12 ">
        <div class="col-md-6 px-3 mx-auto text-center f-flex align-items-center justify-content-center flex-column">
            <h1>Contact us</h1>
            <p>What is Lorem Ipsum What is Lorem Ipsum What is Lorem Ipsum</p>
        </div>
    </div>
</section>
<section>
    <div class="container">
       <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="wrapper">
                <div class="row no-gutters mb-5">
                    <div class="col-md-12"><br>
                        <div class="contact-wrap w-100 p-md-5 p-4 my-5 shadow-lg">
                            <h3 class="mb-4 theme-color">Contact Us</h3>
                            <div id="form-message-warning" class="mb-4"></div>

                      <div class="ermsg"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label" for="name">Full Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label" for="email">Email Address</label>
                                            <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="label" for="subject">Subject</label>
                                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="label" for="#">Message</label>
                                            <textarea name="message" class="form-control" id="message" cols="30" rows="4" placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div class="form-group">
                                            <input type="button" id="submit" value="Submit" class="btn btn-primary">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    </div>
</section> --}}
@endsection

@section('script')
<script>
     $(document).ready(function () {


 //header for csrf-token is must in laravel
 $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            //  make mail start
            var url = "{{URL::to('/contact-submit')}}";
            $("#submit").click(function(){

                    var name= $("#name").val();
                    var email= $("#email").val();
                    var subject= $("#subject").val();
                    var message= $("#message").val();
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {name,email,subject,message},
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
