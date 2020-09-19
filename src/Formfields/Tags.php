<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Contracts\Formfields\Features\BrowseArray;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Add;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Browse;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Edit;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Store;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Update;
use Voyager\Admin\Contracts\Formfields\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Tags implements Formfield, BrowseArray, Add, Browse, Edit, Store, Update
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
