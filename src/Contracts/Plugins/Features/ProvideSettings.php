<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

interface ProvideSettings
{
    public function provideSettings(): array;
}