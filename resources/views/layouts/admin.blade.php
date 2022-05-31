
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charity</title>
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/animate.min.css') }}" />
    <link rel="stylesheet" href="{{URL::to('assets/vendor/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{URL::to('/css/datatables.min.css')}}">
    <link href="{{URL::to('/css/common.css')}}" rel="stylesheet">
    @yield('css')
</head>

<body>
    <!-- dashboard  section -->
    <div class="dashbaord-main">




        {{-- sidebar start here  --}}
        @include('inc.admin_sidebar')
        {{-- sidebar end here  --}}




        <div class="rightSection">



            {{-- admin topbar start  --}}

            @include('inc.admin_header')


            {{-- admin topbar end  --}}



            {{-- main body start here  --}}
            @yield('content')
            {{-- main body end here  --}}

        </div>
    </div>

    <!-- footer  -->


        @include('inc.admin_footer')


    <script src="{{ asset('assets/admin/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/iconify.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/app.js') }}"> </script>
    <script src="{{URL::to('js/plugins/datatables.min.js')}}"></script>
    <script src="{{URL::to('js/plugins/dataTables.bootstrap.min.js')}}"></script>
    @yield('script')
    <script>
        new WOW().init();
    </script>

    <script>
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
                datasets: [
                    {
                        label: "# of Votes",
                        data: [12, 19, 3, 5, 2, 3]
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true
                        }
                    }
                }
            }
        });
    </script>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

<script>
    // page schroll top
    function pagetop() {
        window.scrollTo({
            top: 130,
            behavior: 'smooth',
        });
    }


    function success(msg){
           $.notify({
                   // title: "Update Complete : ",
                   message: msg,
                   // icon: 'fa fa-check'
               },{
                   type: "info"
               });

       }
   function dlt(){
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


   }

$(document).ready(function() {

    var title = 'Report: ';
    var data = 'Data: ';


    // datatable common
    $('#example').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [ { type: 'date', 'targets': [0] } ],
        order: [[ 0, 'desc' ]],
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


    // datatable in
    $('#exampleIn').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [ { type: 'date', 'targets': [0] } ],
        order: [[ 0, 'desc' ]],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            {extend: 'pdfHtml5',
            title: 'Report',
            orientation : 'portrait',
                header:true,
                customize: function ( doc ) {
                    doc.content.splice(0, 1, {
                            text: [

                                       { text: data+'\n',bold:true,fontSize:12 },
                                       { text: title+'\n',bold:true,fontSize:15 }

                            ],
                            margin: [0, 0, 0, 12],
                            alignment: 'center'
                        });
                    doc.defaultStyle.alignment = 'center'
                }
            },
            {extend: 'print',
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


    // datatable All transaction
    $('#exampleall').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [ { type: 'date', 'targets': [0] } ],
        order: [[ 0, 'desc' ]],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            {extend: 'pdfHtml5',
            title: 'Report',
            orientation : 'portrait',
                header:true,
                customize: function ( doc ) {
                    doc.content.splice(0, 1, {
                            text: [

                                       { text: data+'\n',bold:true,fontSize:12 },
                                       { text: title+'\n',bold:true,fontSize:15 }

                            ],
                            margin: [0, 0, 0, 12],
                            alignment: 'center'
                        });
                    doc.defaultStyle.alignment = 'center'
                }
            },
            {extend: 'print',
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





} );
</script>
<script type="text/javascript" src="{{asset('js/plugins/bootstrap-notify.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/plugins/sweetalert.min.js')}}"></script>

</body>

</html>
