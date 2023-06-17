@extends('frontend.layouts.charity')
@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Send a link
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
                            <label for="">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Benzion Yehuda Landau">
                        </div>
                    </div>


                    <div class="col-lg-12">
                        <div class="form-group ">
                            <label for="">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="info@initact.com">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount">
                        </div>
                    </div>
                    <div class="col-lg-12 mt-4">
                        <div class="form-group ">
                            {{-- <button class="btn-theme bg-primary">Send</button> --}}
                            <input type="button" id="submit" value="Send"  class="btn-theme bg-primary">
                        </div>
                    </div>
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
        var url = "{{URL::to('/charity/create-a-link')}}";
        $("#submit").click(function(){
                
            var name= $("#name").val();           
            var email= $("#email").val();
            var amount= $("#amount").val();
            $.ajax({
                url: url,
                method: "POST",
                data: {name,email,amount},
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
        // send mail end =
    });
</script>

@endsection