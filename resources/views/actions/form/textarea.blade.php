<div class="form-group form-group-{{ config('admin.form-style') }}">
    <label>{{ $label }}</label>
    <textarea name="{{$name}}" class="form-control form-control-{{ config('admin.form-style') }} {{$class}}"
              rows="{{ $rows }}"
              placeholder="{{ $placeholder }}" {!! $attributes !!} >{{ old($column, $value) }}</textarea>
    @include('admin::actions.form.help-block')
</div>