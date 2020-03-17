<div class="form-group form-group-{{ config('admin.form-style') }}">
    <label class="col-sm-2 control-label control-label-{{ config('admin.form-style') }}"> {{$label}}</label>
    <div class="col-sm-8">
        @include($presenter->view())
    </div>
</div>