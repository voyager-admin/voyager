<?php

namespace Voyager\Admin\Classes;

use Illuminate\Database\Eloquent\SoftDeletes;
use Voyager\Admin\Traits\Translatable;

class Bread implements \JsonSerializable
{
    use Translatable;

    private $translatable = ['slug', 'name_singular', 'name_plural'];

    public $table;
    protected $slug;
    protected $name_singular;
    protected $name_plural;
    public $icon = 'bread';
    public $model;
    public $controller;
    public $policy;
    public $global_search_field;
    public $order_field;
    public $layouts = [];

    protected $model_class = null;

    public function __construct($json)
    {
        $this->layouts = collect();

        collect($json)->each(function ($value, $key) {
            if ($key == 'layouts') {
                foreach ($value as $layout) {
                    $layout = new Layout($layout);
                    $this->layouts->push($layout);
                }
            } else {
                $this->{$key} = $value;
            }
        });
    }

    public function getModel()
    {
        if (!$this->model_class) {
            $this->model_class = app($this->model);
        }

        return $this->model_class;
    }

    public function usesTranslatableTrait()
    {
        return in_array(Translatable::class, class_uses($this->getModel()));
    }

    public function usesSoftDeletes()
    {
        return in_array(SoftDeletes::class, class_uses($this->getModel()));
    }

    public function jsonSerialize()
    {
        return [
            'table'               => $this->table,
            'slug'                => $this->slug,
            'name_singular'       => $this->name_singular,
            'name_plural'         => $this->name_plural,
            'icon'                => $this->icon,
            'model'               => $this->model,
            'controller'          => $this->controller,
            'policy'              => $this->policy,
            'global_search_field' => $this->global_search_field,
            'order_field'         => $this->order_field,
            'layouts'             => $this->layouts,
        ];
    }
}
