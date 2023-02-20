@extends('frontend.layouts.user')
@section('content')


<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Contact Tevini
            </div>
        </div>
    </div>
    <form action="">
        <div class="row ">
            <div class="col-lg-6  px-3">
                <div class="ermsg"></div>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="label" for="name">Full Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ Auth::user()->name }}" placeholder="Name">
                        </div>
                    </div>


                    <div class="col-lg-12">
                        <div class="form-group ">
                            <label class="label" for="email">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{ Auth::user()->email }}" placeholder="Email">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group ">
                            <label class="label" for="subject">Subject</label>
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group ">
                            <label class="label" for="#">Message</label>
                            <textarea name="message" class="form-control" id="message" cols="30" rows="4" placeholder="Message"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-4">
                        <div class="form-group ">
                            <input type="button" id="submit" value="Submit"  class="btn-theme bg-primary">
                        </div>
                    </div>
                </div>
                <div class="row p-3  " style="background-color: #D9D9D9;">
                    <div>
                        <span class="txt-secondary fs-36">Phone:</span> <br>
                        <span class="txt-secondary fs-20"> 07490 956 227</span> <br>
                    </div>
                    <div>
                        <span class="txt-secondary fs-36">Email:</span> <br>
                        <span class="txt-secondary fs-20">  info@tevini.co.uk</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 border-left-lg px-3">
                <div class="user mt-3">
                    Top up your account
                </div>
                <br>
                <div class="fs-24 fw-bold txt-secondary">
                    To Top your Account Please use one of the following:
                </div><br>
                <div class="fs-24 fw-bold txt-secondary">
                    1. Transfer funds to our bank account:
                </div>
                <div class="fs-16 my-3 txt-secondary">
                    Tevini Ltd, <br>
                    Sort Code: 40-52-40 <br>
                    Account no: 00024463
                </div>
                <div class="fs-24 fw-bold txt-secondary">
                    2. Send a cheque
                </div>
                <div class="fs-16 my-3 txt-secondary">
                    payable to: Tevini Ltd Tevini Ltd <br>
                    Tevini Ltd <br>
                    5A Holmdale Terrace <br>
                    London, N156PP
                </div>
                <div class="fs-24 fw-bold txt-secondary">
                    3. Top-Up using your card: <br> 
                    <a href="{{ route('stripeDonation')}}" class="btn-theme bg-ternary">Top up account</a>
                </div>
            </div>
        </div>
    </form>
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
