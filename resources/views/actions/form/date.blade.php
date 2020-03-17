<div class="form-group form-group-{{ config('admin.form-style') }}">
    <label>{{ $label }}</label>
    <input style="width: 100%" {!! $attributes !!} />
    @include('admin::actions.form.help-block')
</div>