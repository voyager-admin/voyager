<?php

namespace Voyager\Admin\Classes;

use Voyager\Admin\Manager\Breads as BreadManager;

class Layout implements \JsonSerializable
{
    public $name;
    public $type = 'list';
    public $options = [];
    public $formfields = [];
    protected $breadmanager;

    public function __construct($json)
    {
        $this->breadmanager = resolve(BreadManager::class);
        $this->formfields = collect();
        collect($json)->each(function ($value, $key) {
            if ($key == 'formfields') {
                foreach ($value as $f) {
                    $formfield = $this->breadmanager->getFormfield($f->type);
                    if (!$formfield) {
                        throw new \Exception('Formfield with type "'.$f->type.'" does not exist!');
                    }
                    $formfield = clone $formfield;
                    foreach ($f as $key => $prop) {
                        $formfield->{$key} = $prop;
                    }
                    $this->formfields->push($formfield);
                }
            } else {
                $this->{$key} = $value;
            }
        });
    }

    public function searchableFormfields()
    {
        return $this->formfields->where('searchable');
    }

    public function getFormfieldByColumn($column)
    {
        return $this->formfields->where('column.column', $column)->first();
    }

    public function getFormfieldsByColumnType($type)
    {
        return $this->formfields->where('column.type', $type);
    }

    public function hasTranslatableFormfields()
    {
        return $this->formfields->filter(function ($formfield) {
            return $formfield->translatable ?? false;
        })->count() > 0;
    }

    public function jsonSerialize()
    {
        return [
            'name'       => $this->name,
            'type'       => $this->type,
            'options'    => $this->options,
            'formfields' => $this->formfields,
        ];
    }
}
