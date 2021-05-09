<?php

namespace Voyager\Admin\Classes;

class Formfield
{
    public function browse($value)
    {
        return $value;
    }

    public function read($value)
    {
        return $value;
    }

    public function edit($value)
    {
        return $value;
    }

    public function update($model, $value, $old)
    {
        return $value;
    }

    public function updated($model, $value) {}

    public function add()
    {
        return '';
    }

    public function store($value)
    {
        return $value;
    }

    public function stored($model, $value) {}
}