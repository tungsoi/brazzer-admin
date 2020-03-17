<div class="grid-dropdown-actions dropdown">
    @foreach($default as $action)
        {!! $action->render() !!}
    @endforeach
        @if(!empty($custom))
            @if(!empty($default))
                <span class="divider"></span>
            @endif
            @foreach($custom as $action)
                {!! $action->render() !!}
            @endforeach
        @endif
</div>