<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenvini</title>
    <link href="{{ asset('assets/user/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/user/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/user/css/animate.min.css') }}" />
    <link rel="stylesheet" href="{{URL::to('/css/datatables.min.css')}}">
    <link href="{{URL::to('/css/common.css')}}" rel="stylesheet">
    @yield('css')
</head>

<body>
    <!-- dashboard  section -->
    <div class="dashbaord-main">


            {{-- sidebar start here  --}}

            @include('frontend.inc.user_sidebar')
        {{-- sidebar close here  --}}


        <div class="rightSection">

            {{-- topbar start here  --}}

            @include('frontend.inc.user_header')
            {{-- topbar close here  --}}


            {{-- dashbord content start here  --}}
            @yield('content')
            {{-- dashbord content close here  --}}



        </div>
    </div>

    @include('frontend.inc.user_footer')

    <script src="{{ asset('assets/user/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/iconify.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/wow.min.js') }}"></script>
    <script src='{{ asset('assets/user/js/app.js') }}'> </script>
    <script src="{{URL::to('js/plugins/datatables.min.js')}}"></script>
    <script src="{{URL::to('js/plugins/dataTables.bootstrap.min.js')}}"></script>
    <script>
        new WOW().init();
    </script>
    @yield('script')
    <script>
        function pagetop() {
        window.scrollTo({
        top: 130,
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





} );
    </script>

</body>

</html>
