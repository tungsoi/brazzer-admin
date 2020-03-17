<div class="form-group form-group-{{ config('admin.form-style') }}">
    <label class="col-sm-2 control-label control-label-{{ config('admin.form-style') }}">{{$label}}</label>
    <div class="col-sm-8" style="width: 390px">
        <div class="input-group input-group-{{ config('admin.form-style') }}">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control" id="{{$id['start']}}" placeholder="{{$label}}" name="{{$name['start']}}" value="{{ request($name['start'], \Illuminate\Support\Arr::get($value, 'start')) }}">
            <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
            <input type="text" class="form-control" id="{{$id['end']}}" placeholder="{{$label}}" name="{{$name['end']}}" value="{{ request($name['end'], \Illuminate\Support\Arr::get($value, 'end')) }}">
        </div>
    </div>
</div>