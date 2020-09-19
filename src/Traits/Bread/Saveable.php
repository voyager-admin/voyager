<?php

namespace Voyager\Admin\Traits\Bread;

use Voyager\Admin\Contracts\Formfields\Features;

trait Saveable
{
    public function updateStoreData($formfields, $data, $model, $update = true)
    {
        $formfields->each(function ($formfield) use ($data, &$model, $update) {
            $value = $data[$formfield->column->column] ?? '';

            if ($formfield->translatable ?? false) {
                $translations = [];
                $old = $model->{$formfield->column->column};
                if (!is_object($old)) {
                    $old = @json_decode($old);
                }
                foreach ($value as $locale => $translated) {
                    if ($update) {
                        $old = (isset($old->{$locale}) ? $old->{$locale} : '');
                        $translations[$locale] = $formfield instanceof Features\ManipulateData\Update ? $formfield->update($model, $translated, $old) : $translated;
                    } else {
                        $translations[$locale] = $formfield instanceof Features\ManipulateData\Store ? $formfield->store($translated) : $translated;
                    }
                }
                $value = json_encode($translations);
            } else {
                if ($update) {
                    $value = $formfield instanceof Features\ManipulateData\Update ? $formfield->update($model, $value, $model->{$formfield->column->column}) : $value;
                } else {
                    $value = $formfield instanceof Features\ManipulateData\Store ? $formfield->store($value) : $value;
                }
            }

            if ($formfield->column->type == 'column') {
                $model->{$formfield->column->column} = $value;
            } elseif ($formfield->column->type == 'computed') {
                if (method_exists($model, 'set'.Str::camel($formfield->column->column).'Attribute')) {
                    $model->{$formfield->column->column} = $value;
                }
            }
        });

        return $model;
    }
}
