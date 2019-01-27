<div class="navbar-default sidebar" role="navigation">

    <div class="sidebar-nav navbar-collapse">

        <ul class="nav" id="side-menu">

          {{--   <li class="sidebar-search">

                <div class="input-group custom-search-form">

                    <input type="text" class="form-control" placeholder="Search...">

                    <span class="input-group-btn">

                    <button class="btn btn-default" type="button">

                        <i class="fa fa-search"></i>

                    </button>

                </span>

                </div>

                <!-- /input-group -->

            </li> --}}

            <li>

                <a href="{{ route('admin') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>

            </li>
            <li>

                <a href="#"><i class="fa fa-film fa-fw"></i> Content <span class="fa arrow"></span></a>

                    <ul class="nav nav-second-level">                     

                        <li>
    
                            <a href="{{ route('drama') }}"><i class="fa fa-film fa-fw"></i> Drama </a>
    
                        </li>
                        <li>
    
                            <a href="{{ route('country') }}"><i class="fa fa-flag fa-fw"></i> Country </a>
    
                        </li>
                        
                        <li>
    
                            <a href="{{ route('type') }}"><i class="fa fa-television fa-fw"></i> Types</a>
    
                        </li>
                    </ul>

            </li>

            @if(Sentinel::getUser()->roles()->first()->slug == 'admin')
            <li>

                <a href="#"><i class="fa fa-users fa-fw"></i> Gmail Accounts <span class="fa arrow"></span></a>

                    <ul class="nav nav-second-level">                     

                        <li>
    
                            <a href="{{ route('gmail') }}">List Gmail Accounts</a>
    
                        </li>
                        
                        <li>
    
                            <a href="#">Drive File</a>
    
                        </li>
                    </ul>

            </li>
            <li>

                    <a href="#"><i class="fa fa-sitemap fa-fw"></i> Site Wordpress <span class="fa arrow"></span></a>
    
                        <ul class="nav nav-second-level">                     
    
                            <li>
        
                                <a href="{{ route('webfront') }}">List Site</a>
        
                            </li>

                        </ul>
    
                </li>

                <li>
                    
                    <a href="#"><i class="fa fa-user fa-fw"></i> Users<span class="fa arrow"></span></a>
    
                    <ul class="nav nav-second-level">                     
    
                        <li>
    
                            <a href="{{ route('users') }}">List Users</a>
    
                        </li>
                        
                        <li>
    
                            <a href="{{ route('users.roles') }}">Roles</a>
    
                        </li>
                    </ul>
    
                    <!-- /.nav-second-level -->
    
                </li>

                <li>

                    <a href="/admin/setting"><i class="fa fa-cogs fa-fw"></i> Setting</a>

                </li>
            @endif
        
        </ul>

    </div>

    <!-- /.sidebar-collapse -->

</div>

<!-- /.navbar-static-side -->