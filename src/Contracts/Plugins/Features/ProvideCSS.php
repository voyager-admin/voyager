<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

interface ProvideCSS
{
    public function provideCSS(): string;
}