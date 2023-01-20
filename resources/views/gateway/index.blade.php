@extends('layouts.admin')
@section('content')
<div class="rightSection">
    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Gateway List </div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <button id="newBtn" type="button" class="btn btn-info">Add New</button>
            </div>
        </section>

        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">
                <div class="col-md-3">
                </div>
            <div class="col-md-6  my-4 bg-white">
                <div class="card">
                    <div class="card-header">
                        <h3>Gateway</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="ermsg">
                            </div>
                            <div class="container">

                                {!! Form::open(['url' => 'admin/gateway/create','id'=>'createThisForm']) !!}
                                {!! Form::hidden('gatewayid','', ['id' => 'gatewayid']) !!}

                                <div>
                                    <label for="gatewayname">Gateway Name</label>
                                    <input type="text" id="gatewayname" name="gatewayname" class="form-control">
                                </div>
                                <div>
                                    <label for="returnurl">Return URL</label>
                                    <input type="text" id="returnurl" name="returnurl" class="form-control">
                                </div>
                                 <hr>
                                <input type="button" id="addBtn" value="Create" class="btn btn-primary">
                                <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
                                {!! Form::close() !!}

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </section>

    <section class="px-4"  id="contentContainer">
        <div class="row my-3">
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="donorexample">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Gateway Name</th>
                                    <th>Identifier</th>
                                    <th>Return URL</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($gateways as $gateway)
                                    <tr>
                                        <td>{{$n}}</td>
                                        <td>{{$gateway->gateway_name}}</td>
                                        <td>{{$gateway->id}}</td>
                                        <td>{{$gateway->return_url}}</td>
                                        <td>
                                            <a id="EditBtn" rid="{{$gateway->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                <a id="deleteBtn" rid="{{$gateway->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    $n++;
                                    ?>
                                @empty
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </section>


    </div>
</div>


<!-- Button trigger modal -->
@endsection

@section('script')
<script>
    $(document).ready(function () {

            $("#addThisFormContainer").hide();
            $("#newBtn").click(function(){
                clearform();
                $("#newBtn").hide(100);
                $("#addThisFormContainer").show(300);

            });
            $("#FormCloseBtn").click(function(){
                $("#addThisFormContainer").hide(200);
                $("#newBtn").show(100);
                clearform();
            });


            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/admin/gateway')}}";
            $("#addBtn").click(function(){

                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            gatewayname: $("#gatewayname").val(),
                            returnurl: $("#returnurl").val()
                        },
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
                }

                //create  end
                //Update
                if($(this).val() == 'Update'){
                    var gatewayid= $("#gatewayid").val();
                    var gatewayname= $("#gatewayname").val();
                    var returnurl= $("#returnurl").val();
                    $.ajax({
                        url:url+'/'+gatewayid,
                        method: "PUT",
                        type: "PUT",
                        data:{ gatewayname,returnurl},
                        success: function(d){
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                                pagetop();
                            }else if(d.status == 300){
                                pagetop();
                                success("Gateway Updated Successfully!!");
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error:function(d){
                            console.log(d);
                        }
                    });
                }
                //Update
            });
            //Edit
            $("#contentContainer").on('click','#EditBtn', function(){

                codeid = $(this).attr('rid');
                info_url = url + '/'+codeid+'/edit';
                // alert(info_url);
                $.get(info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit  end

            //Delete
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                codeid = $(this).attr('rid');
                info_url = url + '/'+codeid;
                $.ajax({
                    url:info_url,
                    method: "DELETE",
                    type: "DELETE",
                    data:{
                    },
                    success: function(d){
                        console.log(d);
                        if(d.success) {
                            success("Deleted Successfully!!");
                            window.setTimeout(function(){location.reload()},2000)
                        }
                    },
                    error:function(d){
                        console.log(d);
                    }
                });
            });
            //Delete


            function populateForm(data){
                $("#gatewayname").val(data.gateway_name);
                $("#returnurl").val(data.return_url);
                $("#gatewayid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }
            function clearform(){
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
            }


        });


</script>
@endsection
