<?php

namespace Brazzer\Admin\Grid\Exporters;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

abstract class ExcelExporter extends AbstractExporter implements FromQuery, WithHeadings
{
    use Exportable;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var array
     */
    protected $headings = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @return array
     */
    public function headings(): array
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }
        if (!empty($this->columns)) {
            return array_values($this->columns);
        }

        return $this->headings;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function query()
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }
        if (!empty($this->columns)) {
            $columns = array_keys($this->columns);

            $eagerLoads = array_keys($this->getQuery()->getEagerLoads());

            $columns = collect($columns)->reject(function ($column) use ($eagerLoads) {
                return Str::contains($column, '.') || in_array($column, $eagerLoads);
            });

            return $this->getQuery()->select($columns->toArray());
        }

        return $this->getQuery();
    }

    public function columns(): array
    {
        $fields = Request::get('fields', []) ? json_decode(Request::get('fields'), true) : $this->getFields();
        if ($fields) {
            $this->columns = $fields;
        }
        return $this->columns;
    }

    private function getFields()
    {
        $fields = [];
        if ($this->grid) {
            foreach ($this->grid->columns() as $column) {
                $fields[$column->getName()] = $column->getLabel();
            }
        }
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function export()
    {
        $this->download($this->fileName)->prepare(request())->send();

        exit;
    }
}
