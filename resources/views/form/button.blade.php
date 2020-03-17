<div class="{{$viewClass['form-group']}} form-group-{{ config('admin.form-style') }}">
    <label class="{{$viewClass['label']}} control-label"></label>
    <div class="{{$viewClass['field']}}">
        <input type='button' value='{{$label}}' class="btn {{ $class }} btn-{{ config('admin.form-style') }}" {!! $attributes !!} />
    </div>
</div>