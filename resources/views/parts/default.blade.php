<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">

    <meta name="author" content="">
    <link rel="stylesheet" href="{!! asset('theme/vendor/datatables/css/jquery.dataTables.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('theme/vendor/datatables/css/dataTables.bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! asset('theme/vendor/datatables-responsive/dataTables.responsive.css') !!}">
    <script scr="{!! asset('theme/js/jwplayer.js') !!}"></script>
    <script scr="//cdn.datatables.net/plug-ins/1.10.6/sorting/date-euro.js"></script>

    <!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->

    <title> @yield('title-page') - {{config('app.name')}} </title>

    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <!-- Bootstrap Core CSS -->

    <link href="{!! asset('theme/vendor/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet">



    <!-- MetisMenu CSS -->

    <link href="{!! asset('theme/vendor/metisMenu/metisMenu.min.css') !!}" rel="stylesheet">



    <!-- Custom CSS -->

    <link href="{!! asset('theme/dist/css/sb-admin-2.css') !!}" rel="stylesheet">



    <!-- Morris Charts CSS -->

    <link href="{!! asset('theme/vendor/morrisjs/morris.css') !!}" rel="stylesheet">



    <!-- Custom Fonts -->

    <link href="{!! asset('theme/vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>


</head>

<body>



    <div id="wrapper">



        <!-- Navigation -->

        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

            @include('parts.header')

            @include('parts.sidebar')

        </nav>



        <div id="page-wrapper">

            @yield('content')

        </div>

        <!-- /#page-wrapper -->



    </div>

    <!-- /#wrapper -->



    <!-- jQuery -->

    <script src="{!! asset('theme/vendor/jquery/jquery.min.js') !!}"></script>



    <!-- Bootstrap Core JavaScript -->

    <script src="{!! asset('theme/vendor/bootstrap/js/bootstrap.min.js') !!}"></script>



    <!-- Metis Menu Plugin JavaScript -->

    <script src="{!! asset('theme/vendor/metisMenu/metisMenu.min.js') !!}"></script>



    <!-- Morris Charts JavaScript 

    <script src="{!! asset('theme/vendor/raphael/raphael.min.js') !!}"></script>

    <script src="{!! asset('theme/vendor/morrisjs/morris.min.js') !!}"></script>

    <script src="{!! asset('theme/data/morris-data.js') !!}"></script>-->


    <!-- DataTables -->
    <script src="{!! asset('theme/vendor/datatables/js/jquery.dataTables.min.js') !!}"></script>
    <script src="{!! asset('theme/vendor/datatables/js/dataTables.bootstrap.min.js') !!}"></script>
    <script src="{!! asset('theme/vendor/datatables/js/dataTables.bootstrap.js') !!}"></script>
    <script src="{!! asset('theme/vendor/datatables/js/dataTables.bootstrap4.js') !!}"></script>

    <!-- Custom Theme JavaScript -->

    <script src="{!! asset('theme/dist/js/sb-admin-2.js') !!}"></script>
    <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });
    </script>
    @yield('scripts')



</body>



</html>