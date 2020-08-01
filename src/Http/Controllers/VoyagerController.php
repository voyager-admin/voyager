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
            if (!empty($bread->global_search_field)) {
                $layout = $this->getLayoutForAction($bread, 'browse');
                if ($layout) {
                    $query = $bread->getModel()->select('*');
                    // TODO: This can be removed when the global search allows querying relationships
                    if ($layout->searchableFormfields()->where('column.type', 'column')->count() == 0) {
                        return;
                    }
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
        })->toArray();

        return response()->json([$disks]);
    }

    public function getThumbnailOptions(Request $request)
    {
        $selected = $request->get('selected', []);

        $options = [
            [
                'fit'    => __('voyager::media.thumbnails.fit'),
                'crop'   => __('voyager::media.thumbnails.crop'),
                'resize' => __('voyager::media.thumbnails.resize'),
                'label'  => __('voyager::media.thumbnails.label'),
            ]
        ];

        if (count($selected) > 0 && $selected[0] !== null) {
            if ($selected[0] == 'fit') {
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.width'),
                ];
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.height_optional'),
                ];
                $options[] = [
                    'top-left'      => __('voyager::media.thumbnails.pos.top_left'),
                    'top'           => __('voyager::media.thumbnails.pos.top'),
                    'top-right'     => __('voyager::media.thumbnails.pos.top_right'),
                    'left'          => __('voyager::media.thumbnails.pos.left'),
                    'center'        => __('voyager::media.thumbnails.pos.center'),
                    'right'         => __('voyager::media.thumbnails.pos.right'),
                    'bottom-left'   => __('voyager::media.thumbnails.pos.bottom_left'),
                    'bottom'        => __('voyager::media.thumbnails.pos.bottom'),
                    'bottom-right'  => __('voyager::media.thumbnails.pos.bottom_right'),
                    'label'         => __('voyager::media.thumbnails.position'),
                ];
                $options[] = [
                    'type'  => 'checkbox',
                    'label' => __('voyager::media.thumbnails.dont_upsize'),
                ];
            } elseif ($selected[0] == 'crop') {
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.width'),
                ];
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.height'),
                ];
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.x_optional'),
                ];
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.y_optional'),
                ];
            } elseif ($selected[0] == 'resize') {
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.width'),
                ];
                $options[] = [
                    'type'  => 'number',
                    'label' => __('voyager::media.thumbnails.height'),
                ];
                $options[] = [
                    'type'  => 'checkbox',
                    'label' => __('voyager::media.thumbnails.keep_aspect_ratio'),
                ];
                $options[] = [
                    'type'  => 'checkbox',
                    'label' => __('voyager::media.thumbnails.dont_upsize'),
                ];
            }
        }

        return response()->json($options);
    }
}
