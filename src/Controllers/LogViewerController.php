<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Extension\LogViewer;
use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class LogViewerController extends Controller{
    use ModelForm;
    public function index($file = null, Request $request){
        if($file === null){
            $file = (new LogViewer())->getLastModifiedLog();
        }
        return Admin::content(function(Content $content) use ($file, $request){
            $offset = $request->get('offset');
            $viewer = new LogViewer($file);
            $content->body(view('admin::log-viewer', [
                'logs' => $viewer->fetch($offset),
                'logFiles' => $viewer->getLogFiles(),
                'fileName' => $viewer->file,
                'end' => $viewer->getFilesize(),
                'tailPath' => route('admin.logviewer.tail', ['file' => $viewer->file]),
                'prevUrl' => $viewer->getPrevPageUrl(),
                'nextUrl' => $viewer->getNextPageUrl(),
                'filePath' => $viewer->getFilePath(),
                'size' => static::bytesToHuman($viewer->getFilesize()),
            ]));
            $content->header($viewer->getFilePath());
        });
    }

    public function tail($file, Request $request){
        $offset = $request->get('offset');
        $viewer = new LogViewer($file);
        list($pos, $logs) = $viewer->tail($offset);
        return compact('pos', 'logs');
    }

    protected static function bytesToHuman($bytes){
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB'
        ];
        for($i = 0; $bytes > 1024; $i++){
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
