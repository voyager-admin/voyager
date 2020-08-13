<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Voyager\Admin\Contracts\Plugins\Features\LayoutFilter;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Plugins\AuthenticationPlugin;
use Voyager\Admin\Plugins\AuthorizationPlugin;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    protected $pluginmanager;

    public function __construct(PluginManager $pluginmanager)
    {
        $this->pluginmanager = $pluginmanager;
    }

    protected function getAuthenticationPlugin()
    {
        return $this->pluginmanager->getPluginByType('authentication', AuthenticationPlugin::class);
    }

    protected function validateData($formfields, $data, $all_locales = false): array
    {
        $errors = [];

        $formfields->each(function ($formfield) use (&$errors, $data, $all_locales) {
            $formfield->validation = $formfield->validation ?? [];
            $value = $data[$formfield->column->column] ?? '';
            if ($formfield->translatable && is_array($value) && !$all_locales) {
                $value = $value[VoyagerFacade::getLocale()] ?? $value[VoyagerFacade::getFallbackLocale()] ?? '';
            }
            foreach ($formfield->validation as $rule) {
                if ($rule->rule == '') {
                    continue;
                }
                if ($all_locales && $formfield->translatable && is_array($value)) {
                    $locales = VoyagerFacade::getLocales();
                    foreach ($locales as $locale) {
                        $val = $value[$locale];
                        $result = $this->validateField($val, $rule->rule, $rule->message);
                        if (!is_null($result)) {
                            $errors[$formfield->column->column][] = strtoupper($locale.': ').$result;
                        }
                    }
                } else {
                    $result = $this->validateField($value, $rule->rule, $rule->message);
                    if (!is_null($result)) {
                        $errors[$formfield->column->column][] = $result;
                    }
                }
            }
        });

        return $errors;
    }

    protected function validateField($value, $rule, $message)
    {
        $validator = Validator::make(['col' => $value], ['col' => $rule]);

        if ($validator->fails()) {
            if (is_object($message)) {
                $message = $message->{VoyagerFacade::getLocale()} ?? $message->{VoyagerFacade::getFallbackLocale()} ?? '';
            } elseif (is_array($message)) {
                $message = $message[VoyagerFacade::getLocale()] ?? $message[VoyagerFacade::getFallbackLocale()] ?? '';
            }
           return $message;
        }

        return null;
    }

    protected function getBread(Request $request)
    {
        return $request->route()->getAction()['bread'] ?? abort(404);
    }

    protected function getLayoutForAction($bread, $action)
    {
        $layouts = $bread->layouts->whereIn('name', $bread->layout_map->{$action})->where('type', $action == 'browse' ? 'list' : 'view');

        $this->pluginmanager->getAllPlugins()->each(function ($plugin) use ($bread, $action, &$layouts) {
            if ($plugin instanceof LayoutFilter) {
                $layouts = $plugin->filterLayouts($bread, $action, $layouts);
            }
        });

        if ($layouts->count() < 1) {
            throw new \Exception(__('voyager::bread.no_layout_assigned', ['action' => ucfirst($action)]));
        }

        return $layouts->first();
    }
}
