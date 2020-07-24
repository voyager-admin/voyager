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
            // Orientate image
            $this->orientateImage(Storage::disk($this->disk)->path($path.$name));
    
            // Add waterwark to image
            $wm = VoyagerFacade::setting('watermark.image', []);
            if (is_array($wm) && isset($wm[0])) {
                $wm = $wm[0];
            }
            $wm_add = false;
            $wm_pos = VoyagerFacade::setting('watermark.position', 'bottom-right');
            $wm_size = VoyagerFacade::setting('watermark.size', 15);
            $wm_x = VoyagerFacade::setting('watermark.x', 10);
            $wm_y = VoyagerFacade::setting('watermark.y', 10);
            $wm_opac = VoyagerFacade::setting('watermark.opacity', 50);

            $wm_path = '';

            
            if (isset($wm->relative_path) && Storage::disk($this->disk)->exists($wm->relative_path.$wm->name)) {
                $wm_path = Storage::disk($this->disk)->path($wm->relative_path.$wm->name);
                $wm_add = true;
            }

            extract(pathinfo($name));
            
            // Generate thumbnails
            VoyagerFacade::getThumbnailDefinitions()->each(function ($thumb) use ($path, $name, $filename, $extension, $wm_add, $wm_pos, $wm_size, $wm_x, $wm_y, $wm_opac, $wm_path) {
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

                // Add watermark to thumbnail
                if ($wm_add) {
                    $this->addWatermark(
                        Storage::disk($this->disk)->path($path.$thumbname),
                        $wm_path,
                        $wm_size,
                        $wm_x,
                        $wm_y,
                        $wm_pos,
                        $wm_opac
                    )->save();
                }

                if (VoyagerFacade::setting('media.optimize', true)) {
                    ImageOptimizer::optimize(Storage::disk($this->disk)->path($path.$thumbname));
                }
            });

            // Add watermark to the "main" image
            if ($wm_add) {
                $this->addWatermark(
                    Storage::disk($this->disk)->path($path.$name),
                    $wm_path,
                    $wm_size,
                    $wm_x,
                    $wm_y,
                    $wm_pos,
                    $wm_opac
                )->save();
            }

            // Optimize image
            if (VoyagerFacade::setting('media.optimize', true)) {
                ImageOptimizer::optimize(Storage::disk($this->disk)->path($path.$name));
            }
        }

        return response()->json([
            'result' => $result,
        ]);
    }

    public function download(Request $request)
    {
        $files = $request->get('files', []);
        if (count($files) == 1) {
            return Storage::disk($this->disk)->get($files[0]['file']['relative_path'].$files[0]['file']['name']);
        }

        $zip = new \ZipArchive();
        $zip->open('download.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($files as $file) {
            $path = Storage::disk($this->disk)->path($file['file']['relative_path'].$file['file']['name']);

            if ($file['file']['type'] == 'directory') {
                $zip = $this->addDirectoryToZip($zip, $path, $file['file']['name']);
            } else {
                $zip->addFile($path, $file['file']['name']);
            }
        }
        $zip->close();

        $content = file_get_contents('download.zip');
        unlink('download.zip');

        return $content;
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

    private function addWatermark($original, $watermark, $size, $x, $y, $position, $opacity)
    {
        $original = Intervention::make($original);
        $watermark = Intervention::make($watermark);

        if ($opacity < 100) {
            $watermark->opacity($opacity);
        }

        $width = $original->width() * ($size / 100);
        $watermark->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $original->insert(
            $watermark,
            $position,
            $x,
            $y,
        );

    }

    private function orientateImage($path)
    {
        try {
            $image = Intervention::make($path);
            $orientation = $image->exif('Orientation');

            if ($orientation !== 1) {
                $image->orientate()->save();
            }

            $image->destroy();
        }
        catch (\Exception $e) { }
    }

    private function addDirectoryToZip($zip, $path, $relative)
    {
        $files = array_diff(scandir($path), ['.', '..']);
        
        foreach ($files as $file) {
            $new_path = $path.'/'.$file;
            $new_relative = $relative.'/'.$file;
            if (is_dir($new_path)) {
                $zip->addEmptyDir($new_relative);
                $zip = $this->addDirectoryToZip($zip, $new_path, $new_relative);
            } else {
                $zip->addFile($new_path, $new_relative);
            }
        }

        return $zip;
    }
}
