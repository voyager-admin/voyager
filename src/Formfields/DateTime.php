<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class DateTime extends Formfield
{
    public function type(): string
    {
        return 'date_time';
    }

    public function name(): string
    {
        return __('voyager::formfields.date_time.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-date-time';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-date-time-builder';
    }

    /*public function add()
    {
        return null;
    }

    public function browse($value)
    {
        return $value;
    }

    public function read($value)
    {
        return $this->browse($value);
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
    }*/
}
