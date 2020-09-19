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

class SimpleArray implements Formfield, BrowseArray, Add, Browse, Edit, Store, Update
{
    public function type(): string
    {
        return 'simple_array';
    }

    public function name(): string
    {
        return __('voyager::formfields.simple_array.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-simple-array';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-simple-array-builder';
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
