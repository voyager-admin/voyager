<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Voyager\Admin\Classes\DynamicInput;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Manager\Settings as SettingsManager;
use Voyager\Admin\Traits\Bread\Browsable;

class VoyagerController extends Controller
{
    use Browsable;

    protected $breadmanager;
    protected $pluginmanager;
    protected $settingmanager;

    public function __construct(BreadManager $breadmanager, PluginManager $pluginmanager, SettingsManager $settingmanager)
    {
        $this->breadmanager = $breadmanager;
        $this->pluginmanager = $pluginmanager;
        $this->settingmanager = $settingmanager;
        parent::__construct($pluginmanager);
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
        if (Str::startsWith($request->path, 'plugin/')) {
            $name = Str::after($request->path, 'plugin/');
            $asset = $this->pluginmanager->getAssets()->where('name', $name)->first();

            if ($asset) {
                $extension = Str::afterLast($name, '.');
                $mime = $this->mime_extensions[$extension];

                return $this->returnAsset($asset['content'], $mime);
            }

            return abort(404);
        }

        $path = Str::before(str_replace('/', DIRECTORY_SEPARATOR, Str::start(urldecode($request->path), '/')), '?');
        $path = realpath(dirname(__DIR__, 3).'/resources/assets/dist').$path;

        if (realpath($path) === $path && File::exists($path)) {
            $extension = Str::afterLast($path, '.');
            $mime = $this->mime_extensions[$extension] ?? File::mimeType($path);

            return $this->returnAsset(File::get($path), $mime);
        }

        abort(404);
    }

    private function returnAsset($content, $mime)
    {
        $response = response($content, 200, [
            'Content-Type'           => $mime,
            'x-content-type-options' => 'nosniff'
        ]);
        $response->setSharedMaxAge(31536000);
        $response->setMaxAge(31536000);
        $response->setExpires(new \DateTime('+1 year'));

        return $response;
    }

    public function dashboard()
    {
        return $this->inertiaRender('Dashboard', [
            'title'   => __('voyager::generic.dashboard'),
            'widgets' => VoyagerFacade::getWidgets(),
        ]);
    }

    public function ui()
    {
        return $this->inertiaRender('UI', [
            'title'   => 'UI',
        ]);
    }

    // Search all BREADS
    public function globalSearch(Request $request)
    {
        $q = $request->get('query');
        $results = collect([]);

        $this->breadmanager->getBreads()->each(function ($bread) use ($q, &$results) {
            if (!empty($bread->global_search_field)) {
                $layout = $this->getLayoutForAction($bread, 'browse');
                if ($layout) {
                    $query = $bread->getModel()->select();
                    // TODO: This can be removed when the global search allows querying relationships
                    if ($layout->searchableFormfields()->where('column.type', 'column')->count() == 0) {
                        return;
                    }
                    $query = $this->globalSearchQuery($q, $layout, VoyagerFacade::getLocale(), $query);
                    $count = $query->count();
                    $bread_results = $query->take(3)->get();
                    if (count($bread_results) > 0) {
                        $results[$bread->table] = [
                            'count'     => $count,
                            'results'   => $bread_results->mapWithKeys(function ($result) use ($bread) {
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
        $disks = collect(array_keys(config('filesystems.disks')))->mapWithKeys(function ($disk) {
            return [$disk => Str::title($disk)];
        })->toArray();
        $select = (new DynamicInput())->addSelect(null, null, $disks, false, 'public');

        return response()->json($select);
    }

    public function getThumbnailOptions(Request $request)
    {
        $method = $request->input('method', 'fit');

        $select = (new DynamicInput())
        ->addText('name', 'Name', 'Name')
        ->addSelect('method', 'Method', [
            'fit'    => __('voyager::media.thumbnails.fit'),
            'crop'   => __('voyager::media.thumbnails.crop'),
            'resize' => __('voyager::media.thumbnails.resize'),
        ], false, $method);

        if ($method) {
            $select->addNumber('width', __('voyager::media.thumbnails.width'), __('voyager::media.thumbnails.width'), null, 0);

            $heightTitle = $method == 'fit' ? __('voyager::media.thumbnails.height_optional') : __('voyager::media.thumbnails.height');
            $select->addNumber('height', $heightTitle, $heightTitle, null, 0);

            if ($method == 'fit') {
                $positions = collect(__('voyager::media.positions'))->mapWithKeys(function ($value, $key) {
                    return [Str::slug($key) => $value];
                })->toArray();
                $select->addSelect('position', __('voyager::media.thumbnails.position'), $positions, false, 'center');
                $select->addSwitch('upsize', __('voyager::media.thumbnails.upsize'), false);
            } elseif ($method == 'crop') {
                $select->addNumber('x', __('voyager::media.thumbnails.x_optional'), __('voyager::media.thumbnails.x_optional'), null, 0);
                $select->addNumber('y', __('voyager::media.thumbnails.y_optional'), __('voyager::media.thumbnails.y_optional'), null, 0);
            } elseif ($method == 'resize') {
                $select->addSwitch('keep_aspect_ratio', __('voyager::media.thumbnails.keep_aspect_ratio'), true);
                $select->addSwitch('upsize', __('voyager::media.thumbnails.upsize'), false);
            }
        }

        return $select;
    }

    public function getWatermarkOptions(Request $request)
    {
        $positions = collect(__('voyager::media.positions'))->mapWithKeys(function ($value, $key) {
            return [Str::slug($key) => $value];
        })->toArray();

        return (new DynamicInput())
            ->addText('name', 'Name', 'Name')
            ->addSelect('position', __('voyager::media.thumbnails.position'), $positions, false, 'bottom-right')
            ->addNumber('x', 'X', 'X', null, 0)
            ->addNumber('y', 'Y', 'Y', null, 0)
            ->addNumber('size', __('voyager::media.watermark.size_in_perc'), __('voyager::media.watermark.size_in_perc'), 10, 0, 100)
            ->addNumber('opacity', __('voyager::media.watermark.opacity'), __('voyager::media.watermark.opacity'), 50, 0, 100);
    }
}
