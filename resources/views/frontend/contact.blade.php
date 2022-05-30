@extends('frontend.layouts.master')

@section('content')

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
</section>
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
