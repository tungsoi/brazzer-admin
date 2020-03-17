<?php

namespace Brazzer\Admin\Grid\Exporters;

use Brazzer\Admin\Facades\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExcelDefault extends ExcelExporter implements FromQuery, WithHeadings, ShouldQueue
{
    use Exportable, SerializesModels;
    protected $fileName = 'Article list.xlsx';
    public $type = 'download';
    protected $disk = 'admin';

    public function __construct($filename = 'export_excel', $type = 'download', $disk = 'admin')
    {
        $this->fileName = $filename;
        $this->type = $type;
        $this->disk = $disk;
    }

    public function export()
    {
        $file_path = 'excels/' . date('Y/m/d/');
        if ($this->type == 'store') {
            $this->store($file_path . $this->fileName, $this->disk);
        } elseif ($this->type == 'queue') {
            $this->fileName = $this->fileName . '-' . time() . '.xlsx';
            $columns = $this->columns();
            $model = $this->getQuery();
            $data = [
                'user_id' => Admin::user()->id,
                'columns' => $columns,
                'sql' => $model->toSql(),
                'sql_binding' => $model->getBindings(),
            ];
            $job = new RunQueueExport($data, $file_path, $this->fileName, $this->disk);
            dispatch($job);
        } else {
            $this->fileName = $this->fileName . '-' . time() . '.xlsx';
            $this->download($this->fileName)->prepare(request())->send();
        }
        exit;
    }
}