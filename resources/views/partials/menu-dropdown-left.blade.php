@if(Admin::user()->visible($item['roles']) && (empty($item['permission']) ?: Admin::user()->can($item['permission'])))
    @if(!isset($item['children']))
        <li class="{{ (url($item['uri']) == url()->current() ? 'active' : '') }}">
            <a href="{{ admin_base_path($item['uri']) }}">
                <i class="fa {{$item['icon']}}"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <span>{{ __($titleTranslation) }}</span>
                @else
                    <span>{{ admin_trans($item['title']) }}</span>
                @endif
            </a>
        </li>
    @else
        <li class="dropdown-submenu">
            <a href="#">
                <i class="fa {{ $item['icon'] }}"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <span>{{ __($titleTranslation) }}</span>
                @else
                    <span>{{ admin_trans($item['title']) }}</span>
                @endif
                <i class="fa fa-angle-right pull-right"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-left" >
                @foreach($item['children'] as $item)
                    @include('admin::partials.menu-dropdown-left', $item)
                @endforeach
            </ul>
        </li>
    @endif
@endif