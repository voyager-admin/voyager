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
        /* 'image/svg+xml' */
    ];

    public function __construct()
    {
        $this->disk = VoyagerFacade::setting('media.disk', 'public');
        $this->path = Str::finish(VoyagerFacade::setting('media.path', '/'), '/');

        parent::__construct();
    }

    public function index()
    {
        return $this->inertiaRender('Media', [
            'title'                 => __('voyager::generic.media'),
            'upload-url'            => route('voyager.media.upload'),
            'list-url'              => route('voyager.media.list'),
            'drag-text'             => __('voyager::media.drag_files_here'),
            'drop-text'             => __('voyager::media.drop_files'),
            'allowed-mime-types'    => VoyagerFacade::setting('media.mime-types', []),
        ]);
    }

    public function uploadFile(Request $request)
    {
        $path = Str::finish($this->path.$request->get('path', ''), '/');
        $files = $request->file('files', []);
        $result = [];

        foreach ($files as $file) {
            $result[] = $this->processUploadedFile($file, $path);
        }

        return response()->json($result);
    }

    protected function processUploadedFile($file, $path)
    {
        $start = microtime(true);
        $name = '';
        $count = 0;
        $thumbnails = 0;
        do {
            $name = $this->getFileName($file, $count);
            $count++;
        } while (Storage::disk($this->disk)->exists($path.$name));

        $uploaded = Storage::disk($this->disk)->putFileAs($path, $file, $name);
        if (in_array($file->getClientMimeType(), $this->imagemimes)) {
            // Orientate image
            $this->orientateImage(Storage::disk($this->disk)->path($path.$name));
    
            // Get watermark settings
            $watermark = VoyagerFacade::setting('watermark.image', []);
            if (is_array($watermark) && isset($watermark[0])) {
                $watermark = $watermark[0];
            }

            if (isset($watermark->relative_path) && Storage::disk($watermark->disk)->exists($watermark->relative_path.$watermark->name)) {
                $watermark = Storage::disk($watermark->disk)->path($watermark->relative_path.$watermark->name);
                $watermark = Intervention::make($watermark);
            } else {
                $watermark = null;
            }

            $watermark = array_merge([
                'image' => $watermark
            ], (array) VoyagerFacade::setting('watermark.settings', []));

            extract(pathinfo($name));

            // Generate thumbnails
            $this->getThumbnailDefinitions()->each(function ($thumb) use ($path, $name, $filename, $extension, $watermark, &$thumbnails) {
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
                // We don't add the watermark to the original image before because it could be croppped out that way.
                if ($watermark['image']) {
                    $this->addWatermark(Storage::disk($this->disk)->path($path.$thumbname), $watermark)->save();
                }

                if (VoyagerFacade::setting('media.optimize', true)) {
                    ImageOptimizer::optimize(Storage::disk($this->disk)->path($path.$thumbname));
                }

                $thumbnails++;
            });

            // Add watermark to the "main" image
            if ($watermark['image']) {
                $this->addWatermark(Storage::disk($this->disk)->path($path.$name), $watermark)->save();
            }

            // Optimize image
            if (VoyagerFacade::setting('media.optimize', true)) {
                ImageOptimizer::optimize(Storage::disk($this->disk)->path($path.$name));
            }
        }

        return [
            'uploaded'      => !($uploaded === false),
            'original'      => $file->getClientOriginalName(),
            'new'           => $uploaded,
            'thumbnails'    => $thumbnails,
        ];
    }

    public function download(Request $request)
    {
        $files = $request->get('files', []);
        if (count($files) == 1 && $files[0]['file']['type'] !== 'directory') {
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
        if (file_exists('download.zip')) {
            $content = file_get_contents('download.zip');
            unlink('download.zip');
        } else {
            return response()->json(__('voyager::media.empty_folder_error'), 500);
        }

        return $content;
    }

    public function listFiles(Request $request)
    {
        $hide_thumbnails = VoyagerFacade::setting('media.hide-thumbnails', true);
        $thumbnail_names = $this->getThumbnailDefinitions()->pluck('name')->transform(function ($name) {
            return '_'.$name;
        })->toArray();
        $exclude = VoyagerFacade::setting('media.exclude', []);
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
                    if (Str::startsWith($thumb, '_')) {
                        $thumb = substr($thumb, 1);
                    }
                    $thumbnails[] = [
                        'thumbnail' => $thumb,
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
        })->filter(function ($file) use ($exclude) {
            return !Str::contains(Str::lower($file['file']['url']), $exclude);
        })->transform(function ($file) use (&$thumbnails) {
            foreach ($thumbnails as $key => $thumb) {
                if ($thumb['original'] == $file['file']['filename']) {
                    unset($thumbnails[$key]);
                    $file['file']['thumbnails'][] = $thumb;
                }
            }

            return $file;
        });

        // Push thumbnails back because there is no "parent" image
        foreach ($thumbnails as $thumbnail) {
            $files = $files->push($thumbnail);
        }

        $files = $files->sortBy('file.name')->sortBy(function ($file) {
            return $file['file']['type'] == 'directory' ? 0 : 99999999;
        });

        return response()->json($files->values());
    }

    public function delete(Request $request)
    {
        $storage = Storage::disk($this->disk);
        $files_deleted = 0;
        $dirs_deleted = 0;

        foreach ($request->get('files', []) as $file) {
            $path = $file['file']['relative_path'].$file['file']['name'];

            if ($storage->getMimetype($path) == 'directory') {
                $storage->deleteDirectory($path);
                $dirs_deleted++;
            } else {
                $storage->delete($path);
                $files_deleted++;

                if (VoyagerFacade::setting('media.delete-thumbnails', true)) {
                    foreach($file['file']['thumbnails'] ?? [] as $thumb) {
                        $path = $thumb['file']['relative_path'].$thumb['file']['name'];
                        $storage->delete($path);
                        $files_deleted++;
                    }
                }
            }
        }

        return response()->json([
            'files'         => $files_deleted,
            'dirs'          => $dirs_deleted,
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

    private function addWatermark($original, $settings)
    {
        $original = Intervention::make($original);
        $watermark = $settings['image'];

        if ($settings['opacity'] < 100) {
           $watermark->opacity($settings['opacity']);
        }

        $width = $original->width() * ($settings['size'] / 100);
        $watermark->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $original->insert(
            $watermark,
            $settings['position'],
            $settings['x'],
            $settings['y'],
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

    /**
     * Get sanitized thumbnail definitions made in the settings.
     *
     * @return Collection The thumbnail definitions.
     */
    private function getThumbnailDefinitions()
    {
        $thumbs = collect(VoyagerFacade::setting('thumbnails'));

        return $thumbs->map(function ($thumb, $name) {
            $name = Str::after($name, 'thumbnails.');
            if (is_object($thumb)) {
                if ($thumb->method == 'fit') {
                    return [
                        'name'      => $name,
                        'method'    => 'fit',
                        'width'     => $thumb->width,
                        'height'    => empty($thumb->height) ? null : $thumb->height,
                        'position'  => empty($thumb->position) ? 'center' : $thumb->position,
                        'upsize'    => empty($thumb->upsize) ? false : $thumb->upsize,
                    ];
                } elseif ($thumb->method == 'crop') {
                    return [
                        'name'      => $name,
                        'method'    => 'crop',
                        'width'     => $thumb->width,
                        'height'    => $thumb->height,
                        'x'         => empty($thumb->x) ? null : $thumb->x,
                        'y'         => empty($thumb->y) ? null : $thumb->y,
                    ];
                } elseif ($thumb->method == 'resize') {
                    return [
                        'name'      => $name,
                        'method'    => 'resize',
                        'width'     => empty($thumb->width) ? null : $thumb->width,
                        'height'    => empty($thumb->height) ? null : $thumb->height,
                        'aspect'    => empty($thumb->keep_aspect_ratio) ? true : $thumb->keep_aspect_ratio,
                        'upsize'    => empty($thumb->upsize) ? false : $thumb->upsize,
                    ];
                }
            }

            return null;
        })->filter(function ($thumb) {
            return $thumb !== null;
        });
    }
}
