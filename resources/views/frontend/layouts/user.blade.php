<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tevini</title>
    <!-- FOR SEO -->
    <!-- <meta property='og:title' content='MarinOne soft'/>
    <meta property='og:image' content='./assets/images/link.jpg'/>
    <meta property='og:description' content='DESCRIPTION OF YOUR SITE'/>
    <meta property='og:url' content='URL OF YOUR WEBSITE'/>
    <meta property='og:image:width' content='1200' />
    <meta property='og:image:height' content='627' />
    <meta property="og:type" content='website'/> -->

    <title>Tevini - Dashboard</title>
    <link rel="icon" href="{{ asset('assets/user/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/bootstrap-5.1.3min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/user/css/animate.min.css') }}" />
    <link rel="stylesheet" href="{{URL::to('assets/vendor/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{URL::to('/css/datatables.min.css')}}">
    <link href="{{URL::to('/css/common.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/user/css/dashboard.css') }}">
    @yield('css')
    <style>
            /*loader css*/
            #loading {
            position: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            }

            #loading-image {
            z-index: 100;
            }
    </style>
</head>

<body>

    <div class="dashboard-wraper">
            {{-- sidebar start here  --}}

            @include('frontend.inc.user_sidebar')
        {{-- sidebar close here  --}}

        <div class="rightbar">
            <!--user header --   topbar -->
            {{-- topbar start here  --}}
            @include('frontend.inc.user_header')
            {{-- topbar close here  --}}

            <!-- content area -->
            <div class="content">
            {{-- dashbord content start here  --}}
            @yield('content')
            {{-- dashbord content close here  --}}

            </div>
        </div>

    </div>





    <script src="{{ asset('assets/user/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/user/js/bootstrap-5.bundle.min.js') }}"></script> --}}
    <script src="{{ asset('assets/user/js/iconify.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/app.js') }}"></script>
    <script src="{{URL::to('js/plugins/datatables.min.js')}}"></script>
    <script src="{{URL::to('js/plugins/dataTables.bootstrap.min.js')}}"></script>
    <script>
        new WOW().init();
    </script>
    @yield('script')
    <script>

function pagetop() {
        window.scrollTo({
        top: 30,
        behavior: 'smooth',
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

// datatable in
$('#exampleAll').DataTable({
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

// datatable in
$('#exampleOut2').DataTable({
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

$('#exampleOut').DataTable({
    pageLength: 25,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    responsive: true,
    columnDefs: [
        { targets: 0, visible: false, searchable: false, type: "num" } // force numeric sort
    ],
    order: [[0, 'desc']],
});




} );
    </script>

</body>

</html>
