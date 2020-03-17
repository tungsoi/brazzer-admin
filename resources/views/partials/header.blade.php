<header class="main-header">
    <a href="{{ admin_base_path('/') }}" class="logo">
        <span class="logo-mini">{{ config('admin.name') }}</span>
        <span class="logo-lg">{{ config('admin.name') }}</span>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <ul class="nav navbar-nav">
            @if(config('admin.enable_menu_breadcrumb'))
                @if(Admin::menuBreadCrumb())
                    @foreach(Admin::menuBreadCrumb() as $menuBreadCrumb)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($menuBreadCrumb['title'])))))
                                    {{ __($titleTranslation) }}
                                @else
                                    {{ admin_trans($menuBreadCrumb['title']) }}
                                @endif
                                <span class="caret"></span>
                            </a>
                            @if(isset($menuBreadCrumb['list_data']))
                                <ul class="dropdown-menu" role="menu">
                                    @each('admin::partials.menu-dropdown', $menuBreadCrumb['list_data'], 'item')
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endif
            {!! Admin::getNavbar()->render('left') !!}
        </ul>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                {{-- {!! Admin::getNavbar()->render() !!} --}}
                {{-- <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-danger" id="notify_total"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <ul class="menu" data-id="{{ Admin::user()->id }}"></ul>
                        </li>
                        <li class="footer">
                            <a href="{{ route('admin.notifies.index') }}">{{ trans('admin.view_all') }}</a>
                        </li>
                    </ul>
                </li> --}}
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/img/noavatar.gif" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ Admin::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @if(Admin::user()->isAdministrator())
                            @each('admin::partials.menu-dropdown-left', Admin::menuData(['2']), 'item')
                        @endif
                        <li class="{{ (request()->path() == 'auth/setting' ? 'active': '') }}">
                            <a href="{{ admin_base_path('auth/setting') }}">
                                <i class="fa fa-gears"></i>
                                <span>{{ trans('admin.setting') }}</span>
                            </a>
                        </li>
                        <li class="{{ (request()->path() == 'auth/logout' ? 'active': '') }}">
                            <a href="{{ admin_base_path('auth/logout') }}">
                                <i class="fa fa-sign-out"></i>
                                <span>{{ trans('admin.logout') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>
