<div class="box-footer">
    {{ csrf_field() }}
    <div class="col-md-{{$width['label']}}">
    </div>
    <div class="col-md-{{$width['field']}}">
    @if($footer_html)
        <!--hongnn-->
            <div class="btn-group-{{ config('admin.form-style') }} pull-left" style="margin-left: 15px">
                @foreach($footer_html as $footer)
                    {!! $footer !!}
                @endforeach
            </div>
        @endif
        @if(in_array('submit', $buttons))
            <div class="btn-group btn-group-{{ config('admin.form-style') }} pull-left" style="margin-left: 15px">
                <button type="submit" class="btn btn-primary btn-{{ config('admin.form-style') }}"
                        onclick="this.form.submit();this.disabled = true;">{{ trans('admin.submit') }}</button>
            </div>

            @foreach($submit_redirects as $value => $redirect)
                @if(in_array($redirect, $checkboxes))
                    <label class="pull-right" style="margin: 5px 10px 0 0;">
                        <input type="checkbox" class="after-submit" name="after-save"
                               value="{{ $value }}" {{ ($default_check == $redirect) ? 'checked' : '' }}> {{ trans("admin.{$redirect}") }}
                    </label>
                @endif
            @endforeach

        @endif

        @if(in_array('reset', $buttons))
            <div class="btn-group btn-group-{{ config('admin.form-style') }} pull-left" style="margin-left: 10px">
                <button type="reset"
                        class="btn btn-warning btn-{{ config('admin.form-style') }}">{{ trans('admin.reset') }}</button>
            </div>
        @endif
    </div>
</div>