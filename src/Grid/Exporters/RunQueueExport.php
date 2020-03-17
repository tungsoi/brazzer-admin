<?php

namespace Brazzer\Admin\Grid\Exporters;

use Brazzer\Admin\Auth\Database\Notify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RunQueueExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $file_name;
    protected $file_path;
    protected $disk;
    public $timeout = 600;
    public $tries = 3;

    public $sleep = 5;

    public function __construct($data, $file_path, $file_name, $disk = 'admin')
    {
        $this->data = $data;
        $this->file_name = $file_name;
        $this->file_path = $file_path;
        $this->disk = $disk;
    }

    public function handle()
    {
        $data = $this->data;
        if ($data && isset($data['sql']) && isset($data['columns']) && isset($data['sql_binding'])) {
            $user_id = isset($data['user_id']) ? $data['user_id'] : 0;
            $sql = $data['sql'];
            $columns = $data['columns'];
            $sql_binding = $data['sql_binding'];
            $column_fields = array_keys($columns);
            $sql = str_replace('*', implode(', ', $column_fields), $sql);
            $result = DB::select($sql, $sql_binding);
            $path = \Storage::disk($this->disk)->path($this->file_path);
            if (in_array($this->disk, ['admin', 'public', 'local'])) {
                \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
            }
            $result = json_decode(json_encode($result), true);
            $result = array_merge([$columns], $result);
            try {
                (new Collection($result))->storeExcel(
                    $this->file_path . $this->file_name,
                    $this->disk,
                    $writerType = null,
                    $headings = false
                );
                if ($user_id > 0) {
                    $link = \Storage::disk($this->disk)->url($this->file_path . $this->file_name);
                    $field = [
                        'user_id' => $user_id,
                        'type' => 2,
                        'icon' => 'download',
                        'messenger' => $this->file_name,
                        'link' => $link,
                        'target' => '_blank',
                    ];
                    Notify::create($field);
                }
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }
    }

}
