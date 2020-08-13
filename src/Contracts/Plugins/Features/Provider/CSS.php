<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

interface CSS
{
    public function provideCSS(): string;
}