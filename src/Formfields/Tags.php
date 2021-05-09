<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Contracts\Formfields\Features\BrowseArray;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Tags extends Formfield implements BrowseArray
{
    public function type(): string
    {
        return 'tags';
    }

    public function name(): string
    {
        return __('voyager::formfields.tags.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-tags';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-tags-builder';
    }

    public function add()
    {
        return [];
    }

    public function browse($value)
    {
        if (!is_array($value)) {
            return VoyagerFacade::getJson($value, []);
        }

        return $value;
    }

    public function edit($value)
    {
        return $this->browse($value);
    }

    public function store($value)
    {
        return json_encode($value);
    }

    public function update($model, $value, $old)
    {
        return $this->store($value);
    }
}
