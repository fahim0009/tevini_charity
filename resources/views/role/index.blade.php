@extends('layouts.admin')



@section('content')
    <main class="app-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">View Staff Role</div>
            </div>
        </section>
        <div id="addThisFormContainer">

            <div class="row">
                <div class="col-md-3">
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>New  Role</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="ermsg">
                                </div>
                                <div class="container">

                    {!! Form::open(['url' => 'admin/role/create','id'=>'permissionForm']) !!}
                    {!! Form::hidden('roleid','', ['id' => 'roleid']) !!}

                    <div>
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>



                <div class="panel-heading">
                    <h3 class="panel-title">{{ __('Permissions') }}</h3>
                </div>

                <div class="form-group" id="chkId">

                        <!--  permissions 1 -->
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label">{{ __('Admin') }}</label>
                                </div>

                                <div class="col-md-2">
                                <div class="toggle lg">
                                    <label>
                                    <input type="checkbox" name="permissions[]" id="p1" value="1"><span class="button-indecator"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            <!-- #######  End 1 ####### -->

                        <!--  permissions 2 -->
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label">{{ __('Staff') }}</label>
                                </div>

                                <div class="col-md-2">
                                <div class="toggle lg">
                                    <label>
                                        <input type="checkbox" name="permissions[]" id="p2" value="2"><span class="button-indecator"></span>
                                    </label>
                                    </div>
                                </div>
                            </div>
                        <!-- #######  End 2 ####### -->

                        <!--  permissions 3 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Role') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p3" value="3"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 3 ####### -->

                        <!--  permissions 4 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('User') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p4" value="4"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 4 ####### -->

                        <!--  permissions 5 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Products') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p5" value="5"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 5 ####### -->

                        <!--  permissions 6 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Products') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p6" value="6"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 6 ####### -->

                        <!--  permissions 7 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Products') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p7" value="7"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 7 ####### -->

                        <!--  permissions 8 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Products') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p8" value="8"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 8 ####### -->


                        <!--  permissions 9 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Products') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p9" value="9"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 9 ####### -->

                        <!--  permissions 10 -->
                        <div class="row">
                            <div class="col-md-10">
                                <label class="control-label">{{ __('Products') }}</label>
                            </div>

                            <div class="col-md-2">
                            <div class="toggle lg">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="p10" value="10"><span class="button-indecator"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    <!-- #######  End 10 ####### -->



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
                <div class="col-md-3">
                </div>
            </div>

        </div>

        <button id="newBtn" type="button" class="btn btn-info">Add New</button>
        <hr>

        <div id="contentContainer">


            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3> Staff Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="container">


                                    <table class="table table-bordered table-hover" id="example">
                                        <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $n = 1;
                                        ?>
                                        @forelse ($roles as $staff)
                                            <tr>
                                                <td>{{$n++}}</td>
                                                <td>{{$staff->name}}</td>
                                                <td>
                                                <a id="EditBtn" rid="{{$staff->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                    <a id="deleteBtn" rid="{{$staff->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <h3>No post found from you. Create a new Staff</h3>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

@endsection
@section('script')

{{-- sweetalart code --}}
<script>
    $('#demoSwal').click(function(){
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this imaginary file!",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel plx!",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            swal("Deleted!", "Your imaginary file has been deleted.", "success");
        } else {
            swal("Cancelled", "Your imaginary file is safe :)", "error");
        }
    });
});
</script>
{{-- sweetalart code --}}

{{-- update alart code  --}}
<script>
$('#demoNotify').click(function(){
    $.notify({
        title: "Update Complete : ",
        message: "Something cool is just updated!",
        icon: 'fa fa-check'
    },{
        type: "info"
    });
});
</script>
{{-- update alart code  --}}

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

            var url = "{{URL::to('/admin/role')}}";
            var url2 = "{{URL::to('/admin/roleupdate')}}";
            // console.log(url);
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {

                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                // Permission IDs start
                var permissionIDs = $("#permissionForm input:checkbox:checked").map(function(){
                    form_data.append("permission[]", $(this).val());
                    }).get();

                $.ajax({
                        url: url,
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data:form_data,
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                            pagetop();
                            success("Insert Successfully!!");
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

                    var form_data = new FormData();
                    form_data.append("roleid", $("#roleid").val());
                    form_data.append("name", $("#name").val());
                    // Permission IDs start
                    var permissionIDs = $("#permissionForm input:checkbox:checked").map(function(){
                    form_data.append("permission[]", $(this).val());
                    }).get();
                    $.ajax({
                        url: url2,
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data:form_data,
                        success: function (d) {
                        console.log(d);
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                            pagetop();
                            success("Updated Successfully!!");
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });
                }
                //Update

            });


            //Edit
            $("#contentContainer").on('click','#EditBtn', function(){
                $codeid = $(this).attr('rid');
                $info_url = url + '/'+$codeid+'/edit';
                $.get($info_url,{},function(d){
                    console.log(d);
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit  end

            //Delete
            $("#contentContainer").on('click','#deleteBtn', function(){

                if(!confirm('Sure?')) return;
                $codeid = $(this).attr('rid');
                $info_url = url + '/'+$codeid;
                $.ajax({
                    url:$info_url,
                    method: "DELETE",
                    type: "DELETE",
                    data:{
                    },
                    success: function(d){
                        if(d.success) {
                            alert(d.message);
                            location.reload();
                        }
                    },
                    error:function(d){
                        console.log(d);
                    }
                });
            });
            //Delete
            function populateForm(data){
                $("#p1").prop("checked", false);
                $("#p2").prop("checked", false);
                $("#p3").prop("checked", false);
                $("#p4").prop("checked", false);
                $("#p5").prop("checked", false);
                $("#p5").prop("checked", false);
                $("#p6").prop("checked", false);
                $("#p7").prop("checked", false);
                $("#p8").prop("checked", false);
                $("#p9").prop("checked", false);
                $("#p10").prop("checked", false);

                $("#roleid").val(data.id);
                $("#name").val(data.name);
                var prmns = data.permissions;

                if(prmns.includes("1")){
                $("#p1").prop("checked", true);
                }

                if(prmns.includes("2")){
                $("#p2").prop("checked", true);
                }

                if(prmns.includes("3")){
                $("#p3").prop("checked", true);
                }

                if(prmns.includes("4")){
                $("#p4").prop("checked", true);
                }

                if(prmns.includes("5")){
                $("#p5").prop("checked", true);
                }

                if(prmns.includes("6")){
                $("#p6").prop("checked", true);
                }

                if(prmns.includes("7")){
                $("#p7").prop("checked", true);
                }

                if(prmns.includes("8")){
                $("#p8").prop("checked", true);
                }

                if(prmns.includes("9")){
                $("#p9").prop("checked", true);
                }

                if(prmns.includes("10")){
                $("#p10").prop("checked", true);
                }

                $("#roleid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }


            function clearform(){
                $('#permissionForm')[0].reset();
                $("#addBtn").val('Create');
            }


        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#role").addClass('active');
        });
    </script>

@endsection
