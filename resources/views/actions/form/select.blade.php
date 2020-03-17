<div class="form-group form-group-{{ config('admin.form-style') }}">
    <label>{{ $label }}</label>

    <select class="form-control form-control-{{ config('admin.form-style') }} {{$class}}" style="width: 100%;"
            name="{{$name}}" {!! $attributes !!} >

        <option value=""></option>
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ $select == old($column, $value) ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
    @include('admin::actions.form.help-block')
</div>

