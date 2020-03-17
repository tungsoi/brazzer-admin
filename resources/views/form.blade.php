<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $form->title() }}</h3>
        <div class="box-tools">
            {!! $form->renderTools() !!}
        </div>
    </div>
    {!! $form->open(['class' => "form-horizontal"]) !!}
    <div class="box-body">
        @if(!$tabObj->isEmpty())
            @include('admin::form.tab', compact('tabObj'))
        @elseif(isset($columnObj) && !$columnObj->isEmpty())
            @include('admin::form.column', compact('columnObj'))
        @else
            <div class="fields-group">
                @if($form->hasRows())
                    @foreach($form->getRows() as $row)
                        {!! $row->render() !!}
                    @endforeach
                @else
                    @foreach($layout->columns() as $column)
                        <div class="col-md-{{ $column->width() }}">
                            @foreach($column->fields() as $field)
                                {!! $field->render() !!}
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
    {!! $form->renderFooter() !!}
    @foreach($form->getHiddenFields() as $field)
        {!! $field->render() !!}
    @endforeach
    {!! $form->close() !!}
</div>

