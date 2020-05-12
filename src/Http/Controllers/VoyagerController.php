<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Traits\Bread\Browsable;

class VoyagerController extends Controller
{
    use Browsable;

    protected $breadmanager;

    public function __construct(BreadManager $breadmanager)
    {
        $this->breadmanager = $breadmanager;
    }

    private $mime_extensions = [
        'js'    => 'text/javascript',
        'css'   => 'text/css',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
    ];

    public function assets(Request $request)
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, Str::start(urldecode($request->path), '/'));
        $path = realpath(dirname(__DIR__, 3).'/resources/assets/dist').$path;

        if (realpath($path) != $path) {
            abort(404);
        }

        if (File::exists($path)) {
            $extension = Str::afterLast($path, '.');
            $mime = $this->mime_extensions[$extension] ?? File::mimeType($path);

            $response = response(File::get($path), 200, ['Content-Type' => $mime]);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        abort(404);
    }

    // Search all BREADS
    public function globalSearch(Request $request)
    {
        $q = $request->get('query');
        $breads = $this->breadmanager->getBreads();
        $results = collect([]);

        $this->breadmanager->getBreads()->each(function ($bread) use ($q, &$results) {
            if ($bread->global_search_field !== '') {
                $layout = $this->getLayoutForAction($bread, 'browse');
                if ($layout) {
                    $query = $bread->getModel()->select('*');
                    $query = $this->globalSearchQuery($q, $layout, VoyagerFacade::getLocale(), $query);
                    $bread_results = $query->get();
                    if (count($bread_results) > 0) {
                        $results[$bread->table] = [
                            'count'     => count($bread_results),
                            'results'   => $bread_results->take(3)->mapWithKeys(function ($result) use ($bread) {
                                return [$result->getKey() => $result->{$bread->global_search_field}];
                            }),
                        ];
                    }
                }
            }
        });

        return $results;
    }

    public function getDisks(Request $request)
    {
        $disks = collect(array_keys(config('filesystems.disks', [])))->mapWithKeys(function ($disk) {
            return [$disk => $disk];
        });
        return [
            $disks
        ];
    }
}
