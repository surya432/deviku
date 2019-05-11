<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Dldramaid.xyz</title>

    <!-- Bootstrap Core CSS -->
    <link href="{!! asset('theme/vendor/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="{!! asset('theme/vendor/metisMenu/metisMenu.min.css') !!}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{!! asset('theme/dist/css/sb-admin-2.css') !!}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{!! asset('theme/vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">

                        <form id="login-form" action="/admin/login" method="POST" role="form">
                            <div id="alert-danger" class="alert alert-danger" style="display:none"></div>
                            <fieldset>

                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail be Like aa@email.com" name="email"
                                        type="email" autofocus required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password"
                                        required>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit" value="Login" class="btn btn-success pull-right">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{!! asset('theme/vendor/jquery/jquery.min.js') !!}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{!! asset('theme/vendor/bootstrap/js/bootstrap.min.js') !!}"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="{!! asset('theme/vendor/metisMenu/metisMenu.min.js') !!}"></script>

    <!-- Custom Theme JavaScript -->
    <script src="{!! asset('theme/dist/js/sb-admin-2.js') !!}"></script>
    <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });
    </script>
    <script type="text/javascript">
    $('#login-form').on("submit", function() {
        event.preventDefault()
        var postData = {
            'email': $('input[name=email]').val(),
            'password': $('input[name=password]').val(),
            'remember_me': $('input[name=remember_me]').is(":checked"),
        }
        $.ajax({
            type: 'POST',
            url: '{{route("loginPost")}}',
            data: postData,
            success: function(data) {
                window.location.href = data.redirect
            },
            error: function(data) {
                $(".alert-danger").text(data.responseJSON.alert)
                $(".alert-danger").show()
            }
        })
    });
    </script>
</body>

</html>