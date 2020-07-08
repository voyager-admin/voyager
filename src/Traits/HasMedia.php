<?php

namespace Voyager\Admin\Traits;

use Voyager\Admin\Classes\Media as MediaClass;

trait HasMedia
{
    public function media($field)
    {
        if (isset($this->{$field})) {
            $media = $this->{$field};
            if (!is_array($media)) {
                $media = json_decode($media);
            }

            return collect($media)->transform(function ($file) {
                return new MediaClass($file);
            });
        }

        return collect();
    }
}