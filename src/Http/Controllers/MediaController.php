<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ImageOptimizer;
use Intervention\Image\Facades\Image as Intervention;
use League\Flysystem\Plugin\ListWith;
use League\Flysystem\Util;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class MediaController extends Controller
{
    public $disk;
    public $path;

    protected $imagemimes = [
        'image/png',
        'image/gif',
        'image/jpeg',
        'image/svg+xml'
    ];

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

        $result = Storage::disk($this->disk)->putFileAs($path, $file, $name);

        if (in_array($file->getClientMimeType(), $this->imagemimes)) {
            if (VoyagerFacade::setting('media.optimize', true)) {
                ImageOptimizer::optimize(Storage::disk($this->disk)->path($path.$name));
            }

            extract(pathinfo($name));
            
            // Generate thumbnails
            VoyagerFacade::getThumbnailDefinitions()->each(function ($thumb) use ($path, $name, $filename, $extension) {
                $image = Intervention::make(Storage::disk($this->disk)->path($path.$name));
                $thumbname = $filename.'_'.$thumb['name'].'.'.$extension;

                extract($thumb);

                if ($method == 'fit') {
                    $image = $image->fit($width, $height, function ($constraint) use ($upsize) {
                        if ($upsize === false) {
                            $constraint->upsize();
                        }
                    }, $position);
                } elseif ($method == 'crop') {
                    $image = $image->crop($width, $height, $x, $y);
                } elseif ($method == 'resize') {
                    $image = $image->resize($width, $height, function ($constraint) use ($aspect, $upsize) {
                        if ($aspect === true) {
                            $constraint->aspectRatio();
                        }
                        if ($upsize === false) {
                            $constraint->upsize();
                        }
                    });
                }
                
                $image->save(Storage::disk($this->disk)->path($path.$thumbname));

                if (VoyagerFacade::setting('media.optimize', true)) {
                    ImageOptimizer::optimize(Storage::disk($this->disk)->path($path.$thumbname));
                }
            });
        }

        return response()->json([
            'result' => $result,
        ]);
    }

    public function listFiles(Request $request)
    {
        $hide_thumbnails = VoyagerFacade::setting('media.hide-thumbnails', true);
        $thumbnail_names = VoyagerFacade::getThumbnailDefinitions()->pluck('name')->transform(function ($name) {
            return '_'.$name;
        })->toArray();
        $thumbnails = [];
        $storage = Storage::disk($this->disk);
        $path = Util::normalizePath($this->path.$request->get('path', ''));
        $files = collect($storage->addPlugin(new ListWith())->listWith(['mimetype'], $path))->transform(function ($file) use ($storage, $path, $thumbnail_names, &$thumbnails, $hide_thumbnails) {
            $relative = Str::finish(str_replace('\\', '/', $file['dirname']), '/');

            $f = [
                'is_upload' => false,
                'file'      => [
                    'type'          => $file['type'] == 'dir' ? 'directory' : $file['mimetype'],
                    'name'          => $file['basename'],
                    'filename'      => $file['filename'],
                    'relative_path' => $relative,
                    'size'          => $file['size'] ?? 0,
                    'url'           => $storage->url($file['path']),
                    'disk'          => $this->disk,
                    'thumbnails'    => [],
                ],
            ];

            foreach ($thumbnail_names as $thumb) {
                if (Str::endsWith($file['filename'], $thumb)) {
                    $original = str_replace($thumb, '', $file['filename']);
                    $thumbnails[] = [
                        'original'  => $original,
                        'file'      => $f['file'],
                    ];

                    if ($hide_thumbnails) {
                        return null;
                    }
                }
            }
            
            return $f;
        })->filter(function ($file) {
            return $file !== null;
        })->transform(function ($file) use (&$thumbnails) {
            foreach ($thumbnails as $key => $thumb) {
                if ($thumb['original'] == $file['file']['filename']) {
                    unset($thumbnails[$key]);
                    $file['file']['thumbnails'][] = $thumb;
                }
            }

            return $file;
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
