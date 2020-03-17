<div class="{{$viewClass['form-group']}} form-group-{{ config('admin.form-style') }}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        <input type="text" id="{{$id}}" name="{{$name}}" value="{{$value}}" class="form-control" readonly {!! $attributes !!} />
        @include('admin::form.help-block')
    </div>
</div>