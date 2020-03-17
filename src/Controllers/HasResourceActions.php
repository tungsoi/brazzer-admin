<?php

namespace Brazzer\Admin\Controllers;

use Illuminate\Http\Request;

trait HasResourceActions
{
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->form()->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->form()->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(in_array('is_delete', $this->form()->model()->getFillable())){
            if($this->form()->model()->find($id)->update(['is_delete' => 1])){
                $data = [
                    'status'  => true,
                    'message' => trans('admin.delete_succeeded'),
                ];
            }else{
                $data = [
                    'status'  => false,
                    'message' => trans('admin.delete_failed'),
                ];
            }
        }else{
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }
        return response()->json($data);
    }
    function upload_file_import(Request $request, $result = [], $folder_name = 'default'){
        set_time_limit(0);
        if($request->getContent() != ''){
            $content_disposition_header = $request->header('Content-Disposition');
            $file_name                  = $content_disposition_header ? rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $content_disposition_header)) : null;
            $content_type               = $request->header('Content-Type');
            $content_type               = explode('/', $content_type);
            $extension                  = end($content_type);
            if($content_type && $extension && $file_name != ''){
                $allowed = array(
                    'xls',
                    'csv',
                    'xlsx',
                    'vnd.ms-excel',
                    'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                );
                if(!in_array($extension, $allowed)){
                    $result['message'] = 'File upload không đúng định dạng cho phép.';
                }else{
                    $imagePath = 'uploads/' . $folder_name . '/' . date('Y/');
                    $filename  = str_replace('.jpg', '.jpeg', $file_name);
                    $path      = storage_path($imagePath);
                    \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
                    $fileput = file_put_contents($path . '/' . $filename, $request->getContent(), FILE_APPEND | LOCK_EX);
                    if($fileput){
                        $content_range = $request->header('Content-Range');
                        if($content_range && $content_range != ''){
                            preg_match('/(.*)-(.*)\/(.*)/', $content_range, $matches);
                            list($all, $start, $end, $total) = $matches;
                            $filesize = filesize($path . '/' . $filename);
                            if($filesize == $total || $filesize == $end || $end == $total){
                                $result['success'] = true;
                                $result['data']    = $imagePath . '/' . $filename;
                            }
                        }else{
                            $content_length = $request->header('Content-Length') && $request->header('Content-Length') > 0 ? $request->header('Content-Length') : 0;
                            if($content_length > 0){
                                $filesize = filesize($path . '/' . $filename);
                                if($filesize > 0){
                                    $result['success'] = true;
                                    $result['data']    = $imagePath . '/' . $filename;
                                }
                            }
                        }
                    }
                }
            }else{
                $result['message'] = 'Upload file lỗi. Vui lòng upload lại sau.';
            }
        }else{
            $result['message'] = 'Quá trình bạn upload file không đúng.';
        }
        return $result;
    }
}
