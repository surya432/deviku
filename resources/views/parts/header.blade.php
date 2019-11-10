<div class="navbar-header">

    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

        <span class="sr-only">Toggle navigation</span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

    </button>

    <a class="navbar-brand" href="/admin">{{config('app.name')}} </a>

</div>

<!-- /.navbar-header -->



<ul class="nav navbar-top-links navbar-right">

    <li class="dropdown">

        <a class="dropdown-toggle" data-toggle="dropdown" href="#">

            <i class="fa fa-user fa-fw"></i>
            {{ Sentinel::getUser()->first_name}}
            <i class="fa fa-caret-down"></i>
        </a>

        <ul class="dropdown-menu dropdown-user">

            <li><a href="/admin/profile"><i class="fa fa-user fa-fw"></i> Profile</a>

            </li>

            <li><a href="/admin/setting"><i class="fa fa-gear fa-fw"></i> Settings</a>

            </li>

            <li class="divider"></li>
            <form action="/admin/logout" method="POST" role="form" id="logout-form">
                {{ csrf_field() }}
                <li><a href="#" onclick="document.getElementById('logout-form').submit()"><i
                            class="fa fa-sign-out fa-fw"></i> Logout</a>
                </li>

            </form>

        </ul>

        <!-- /.dropdown-user -->

    </li>

    <!-- /.dropdown -->

</ul>

<!-- /.navbar-top-links -->