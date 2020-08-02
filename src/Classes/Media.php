<?php

namespace Voyager\Admin\Classes;

use Illuminate\Support\Facades\Storage;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Media
{
    public function __construct($input)
    {
        foreach ($input as $key => $value) {
            if ($key == 'meta') {
                foreach ($value as $meta_key => $meta_value) {
                    $this->{$meta_key} = VoyagerFacade::translate($meta_value);
                }
            } elseif ($key == 'thumbnails') {
                $this->thumbnails = collect($value)->transform(function ($file) {
                    return new Media($file);
                });
            } else {
                $this->{$key} = $value;
            }
        }

        $this->url = Storage::disk($this->disk)->url($this->path.$this->name);
    }
}