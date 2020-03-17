<div class="{{$viewClass['form-group']}} form-group-{{ config('admin.form-style') }} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <div class="input-group input-group-{{ config('admin.form-style') }}">
            @if ($prepend)
                <span class="input-group-addon">{!! $prepend !!}</span>
            @endif
            <input {!! $attributes !!} />
            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif
        </div>
        @include('admin::form.help-block')
    </div>
</div>