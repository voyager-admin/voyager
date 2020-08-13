<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

interface ProvideJS
{
    public function provideJS(): string;
}