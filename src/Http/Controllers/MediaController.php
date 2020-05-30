<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Util;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class MediaController extends Controller
{
    public $disk;
    public $path;

    public function __construct()
    {
        $this->disk = VoyagerFacade::setting('media.disk', 'public');
        $this->path = Str::finish(VoyagerFacade::setting('media.path', '/'), '/');
    }

    public function index()
    {
        return view('voyager::media.browse');
    }

    public function uploadFile(Request $request)
    {
        $path = Str::finish($this->path.$request->get('path', ''), '/');
        $file = $request->file('file');
        $name = '';
        $count = 0;
        do {
            $name = $this->getFileName($file, $count);
            $count++;
        } while (Storage::disk($this->disk)->exists($path.$name));

        return response()->json([
            'result' => Storage::disk($this->disk)->putFileAs($path, $request->file('file'), $name),
        ]);
    }

    public function listFiles(Request $request)
    {
        $storage = Storage::disk($this->disk);
        $path = Util::normalizePath($this->path.$request->get('path', ''));
        $files = collect($storage->listContents($path))->transform(function ($file) use ($storage, $path) {
            return [
                'is_upload' => false,
                'file'      => [
                    'name'          => $file['basename'],
                    'relative_path' => Str::finish(str_replace('\\', '/', $file['dirname']), '/'),
                    'url'           => $storage->url($file['path']),
                    'type'          => $storage->getMimetype($file['path']),
                    'size'          => $file['size'] ?? 0,
                ],
            ];
        })->sortBy('file.name')->sortBy(function ($file) {
            return $file['file']['type'] == 'directory' ? 0 : 99999999;
        })->values();

        return response()->json($files);
    }

    public function delete(Request $request)
    {
        $storage = Storage::disk($this->disk);
        $files_deleted = 0;
        $dirs_deleted = 0;

        foreach ($request->get('files', []) as $file) {
            if ($storage->getMimetype($file) == 'directory') {
                $storage->deleteDirectory($file);
                $dirs_deleted++;
            } else {
                $storage->delete($file);
                $files_deleted++;
            }
        }

        return response()->json([
            'files' => $files_deleted,
            'dirs'  => $dirs_deleted,
        ]);
    }

    public function createFolder(Request $request)
    {
        return Storage::disk($this->disk)->makeDirectory(
            Str::finish($this->path.$request->get('path', ''), '/').$request->get('name', '')
        );
    }

    // Checks if a file exists and add a numbered prefix until the file does not exist.
    private function getFileName($file, $count = 0)
    {
        $pathinfo = pathinfo($file->getClientOriginalName());
        $name = $pathinfo['filename'];
        if ($count >= 1) {
            $name .= '_'.$count;
        }

        return $name.'.'.$pathinfo['extension'];
    }
}
