<form {!! $attributes !!}>
    <div class="box-body fields-group">
        @foreach($fields as $field)
            {!! $field->render() !!}
        @endforeach
    </div>
    @if ($method != 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif
    @if(count($buttons) > 0)
        <div class="box-footer">
            <div class="col-md-{{$width['label']}}"></div>
            <div class="col-md-{{$width['field']}}">
                @if(in_array('submit', $buttons))
                    <div class="btn-group pull-left">
                        <button type="submit"
                                class="btn btn-info btn-{{ config('admin.form-style') }} pull-right">{{ trans('admin.submit') }}</button>
                    </div>
                @endif
                @if(in_array('reset', $buttons))
                    <div class="btn-group pull-left" style="margin-left: 10px">
                        <button type="reset"
                                class="btn btn-default btn-{{ config('admin.form-style') }} pull-right">{{ trans('admin.reset') }}</button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</form>
