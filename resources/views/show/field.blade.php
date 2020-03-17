<div class="form-group form-group-{{ config('admin.form-style') }} ">
    <label class="col-sm-{{$width['label']}} control-label control-label-{{ config('admin.form-style') }}">{{ $label }}</label>
    <div class="col-sm-{{$width['field']}}">
        @if($wrapped)
            <div class="box box-solid box-default no-margin box-show">
                <div class="box-body">
                    @if($escape)
                        {{ $content }}&nbsp;
                    @else
                        {!! $content !!}&nbsp;
                    @endif
                </div>
            </div>
        @else
            @if($escape)
                {{ $content }}
            @else
                {!! $content !!}
            @endif
        @endif
    </div>
</div>