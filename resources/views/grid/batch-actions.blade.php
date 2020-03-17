{{--<input type="checkbox" class="{{ $selectAllName }}" />&nbsp;--}}

@if(!$isHoldSelectAllCheckbox)
<div class="btn-group {{$selectAllName}}-btn btn-{{ config('admin.form-style') }}" style="display:none;margin-right: 5px;">
    <a class="btn btn-{{ config('admin.form-style') }} btn-default" noloading><span class="selected"></span></a>
    <button type="button" class="btn btn-{{ config('admin.form-style') }} btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    @if(!$actions->isEmpty())
    <ul class="dropdown-menu" role="menu">
        @foreach($actions as $action)
            @if($action instanceof \Brazzer\Admin\Actions\BatchAction)
                <li>{!! $action->render() !!}</li>
            @else
                <li><a href="#" class="{{ $action->getElementClass(false) }}" noloading>{!! $action->render() !!} </a></li>
            @endif
        @endforeach
    </ul>
    @endif
</div>
@endif