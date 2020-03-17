<div class="row">
    @foreach($columnObj->getColumns() as $column)
        <div class="col-md-{{ $columnObj->width() }}">
            <div class="box-body">
                <div class="fields-group">
                    @foreach($column['fields'] as $field)
                        {!! $field->render() !!}
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>