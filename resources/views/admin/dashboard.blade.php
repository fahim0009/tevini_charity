
@extends('layouts.admin')

@section('content')


<div class="dashboard-content py-2 px-4">
    <div class="rows bg-white shadow-sm my-3">
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".25s" class="wow fadeIn box text-center theme-1 p-3 ">
                    <span class="iconify bg-violet" data-icon="mdi:white-balance-incandescent"></span>
                    <div class="inner theme-txt-violet">
                        <h1 class="my-0 ">£{{$donation}}</h1>
                        <h5 class="my-2 ">Total Donation In</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".30s" class="wow fadeIn box text-center theme-2 p-3 ">
                    <span class="iconify bg-pink" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-pink">
                        <h1 class="my-0 ">£{{$transaction}}</h1>
                        <h5 class="my-2 ">Total Charity Out</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".35s" class="wow fadeIn box text-center theme-yellow p-3 ">
                    <span class="iconify bg-yellow" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-yellow">
                        <h1 class="my-0 ">£{{$voucherout}}</h1>
                        <h5 class="my-2 ">Total Voucher In</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rows bg-white shadow-sm my-3">
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".25s" class="wow fadeIn box text-center theme-1 p-3 ">
                    <span class="iconify bg-violet" data-icon="mdi:white-balance-incandescent"></span>
                    <div class="inner theme-txt-violet">
                        <h1 class="my-0 ">£{{ $commission }}</h1>
                        <h5 class="my-2 ">Total Commission</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".30s" class="wow fadeIn box text-center theme-2 p-3 ">
                    <span class="iconify bg-pink" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-pink">
                        <h1 class="my-0 ">{{$processvoucher}}</h1>
                        <h5 class="my-2 ">Total Voucher process</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="rows bg-white shadow-sm my-3">
        <div class="cols" id="contentContainer">
            <div class="card">
                <h1 class="text-center">Notification</h1>
                @foreach (\App\Models\Usertransaction::where('notification','=', 1)->get() as $item)
                    
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Holy guacamole!</strong> You should check in on some of those fields below.
                        <input type="hidden" id="codeid" name="codeid" value="{{$item->id}}">
                        <a id="deleteBtn" rid="{{$item->id}}"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                        
                      </div>
                @endforeach
            </div>
        </div>
    </div>


</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {


            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                        //

        var url = "{{URL::to('/admin/notification')}}";


        //Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
                var form_data = new FormData();
                form_data.append('_method', 'put');

                    // console.log(image);
                    $.ajax({
                        url:url+'/'+$("#codeid").val(),
                        type: "POST",
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        data:form_data,
                        success: function(d){
                            console.log(d);
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                                pagetop();
                            }else if(d.status == 300){
                                pagetop();
                                $(".ermsg").html(d.message);
                                // success("Data Deleted Successfully!!");
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        
                        error:function(d){
                            console.log(d);
                        }
                    });

            });
            //Delete
    });
</script>

@endsection
