<?php

namespace Voyager\Admin\Formfields;

use DB;
use Voyager\Admin\Contracts\Formfields\Formfield;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Add;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Query;

class Number implements Formfield, Add, Query
{
    public function type(): string
    {
        return 'number';
    }

    public function name(): string
    {
        return __('voyager::formfields.number.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-number';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-number-builder';
    }

    public function add()
    {
        return $this->options->default_value ?? 0;
    }

    public function query($query, $filter, $locale = null, $global = false)
    {
        $from = $filter['from'] ?? ($global ? $filter : 0);
        $to = $filter['to'] ?? null;
        $column = $this->column->column;

        return $query->{$global ? 'orWhere' : 'where'}(function ($q) use ($from, $to, $locale, $column) {
            if (is_null($locale)) {
                $q = $q->where(DB::raw('lower('.$column.')'), '>=', $from);
                if ($to) {
                    return $q->where(DB::raw('lower('.$column.')'), '<=', $to);
                }

                return $q;
            }

            $q = $q->where(DB::raw('lower('.$column.'->"$.'.$locale.'")'), '>=', $from);
            if ($to) {
                return $q->where(DB::raw('lower('.$column.'->"$.'.$locale.'")'), '<=', $to);
            }

            return $q;
        });
    }
}
