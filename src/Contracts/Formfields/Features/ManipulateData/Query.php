<?php

namespace Voyager\Admin\Contracts\Formfields\Features\ManipulateData;

interface Query
{
    public function query($query, $filter, $locale = null, $global = false);
}