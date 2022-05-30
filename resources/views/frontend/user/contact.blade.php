@extends('frontend.layouts.user')
@section('content')


<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
            <div class="mx-2">Contact Us </div>
        </div>
    </section>
    <section class="px-4">
        <div class="row my-3">
             <div class="col-md-6  my-4 bg-white p-4 shadow-sm rounded">
                 

                <div class="contact-wrap w-100 p-md-5 p-4 my-5 shadow-lg">
                      <div class="ermsg"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="label" for="name">Full Name</label>
                                            <input type="text" class="form-control" name="name" id="name" value="{{ Auth::user()->name }}" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-12"> 
                                        <div class="form-group">
                                            <label class="label" for="email">Email Address</label>
                                            <input type="email" class="form-control" name="email" id="email" value="{{ Auth::user()->email }}" placeholder="Email">
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

                <!--<form action="">-->
                <!--    <h6 class="mb-2 fw-bold">Sent Message</h6> <hr class="bg-secondary mb-3">-->

                <!--   <div class="col my-3">-->
                <!--      <input type="text" placeholder="Name" class="form-control ">-->
                <!--   </div>-->
                <!--   <div class="col my-3">-->
                <!--    <input type="text" placeholder="subject" class="form-control ">-->
                <!--   </div>-->
                <!--    <textarea class="form-control" cols="30" rows="5"></textarea>-->
                <!--    <button class="btn btn-theme mt-2 text-white">Send</button>-->
                <!--</form>-->
            </div>
            <div class="col-md-6  my-4 text-center">
               <img class="img-fluid" src="{{ asset('assets/user/images/messenger.png') }}" alt="">
            </div>

        </div>
    </section>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#usercontact").addClass('active');
    });
</script>

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
