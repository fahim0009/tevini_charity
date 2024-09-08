@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">User delete request List </div>
            </div>
        </section>


        @if(session()->has('message'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('message') }}</div>
            </div>
        </section>
        @endif
        @if(session()->has('error'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
            </div>
        </section>
        @endif



        <section class="px-4"  id="contentContainer">
            <div class="row my-3">

            <!-- Image loader -->
            <div id='loading' style='display:none ;'>
                <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
            </div>
            <!-- Image loader -->

            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="donorexample">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th style="min-width:160px">Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                
                                    <tr>
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->name}} {{$user->surname}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>{{$user->town}}</td>
                                        <td>
                                       <div class="py-1 text-center">
                                        <a href="{{ route('donor.profile', $user->id)}}"><i class="fa fa-eye" style="color: #09a311;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{$user->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                       </div>
                                        </td>
                                    </tr>
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


  <!-- Modal -->
  <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel2">Update Overdrawn </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsgod"></div>
            <div class="mb-3">
                <label for="overdrawnno" class="form-label">Overdrawn Amount</label>
                <input type="text" class="form-control" id="overdrawnno">
                <input type="hidden" class="form-control" value="" id="overdrawnid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="overdrawnBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->



  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Donner Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsg"></div>
            <div class="mb-3">
                <label for="updaccno" class="form-label">Account</label>
                <input type="text" class="form-control" id="updaccno">
                <input type="hidden" class="form-control" value="" id="donnerid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addaccBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->


@endsection

@section('script')

<script>

window.onload = (event) => {
   let k = document.getElementById("example_wrapper");
   k.classList.add('px-0');
};


    $(document).ready(function () {

            setTimeout(function() {
                $('#successMessage').fadeOut('fast');
                $('#errMessage').fadeOut('fast');
            }, 3000);

     //header for csrf-token is must in laravel
     $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //

        var urlDlt = "{{URL::to('/admin/donor/delete')}}";
        // Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Are you sure?')) return;
            donorId = $(this).attr('rid');
            $.ajax({
            url: urlDlt,
            method: "POST",
            data: {donorId:donorId},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                }
            },
            error: function (d) {
                console.log(d);
            }
        });
        });
        // Delete



// datatable

var title = 'Report: ';
var data = 'Data: ';

$('#donorexample').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [ { type: 'date', 'targets': [0] } ],
        order: [[ 1, 'desc' ]],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            // {extend: 'pdfHtml5',
            // title: 'Report',
            // orientation : 'portrait',
            //     header:true,
            //     customize: function ( doc ) {
            //         doc.content.splice(0, 1, {
            //                 text: [

            //                            { text: data+'\n',bold:true,fontSize:12 },
            //                            { text: title+'\n',bold:true,fontSize:15 }

            //                 ],
            //                 margin: [0, 0, 0, 12],
            //                 alignment: 'center'
            //             });
            //         doc.defaultStyle.alignment = 'center'
            //     }
            // },
            {extend: 'print',
            exportOptions: {
               stripHtml: false
           },
            title: "<p style='text-align:center;'>"+data+"<br>"+title+"</p>",
            header:true,
                customize: function (win){
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', 'inherit');
            }
            }
        ]
    });








    });





</script>
@endsection
