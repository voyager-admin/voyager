<?php

namespace Voyager\Admin\Contracts\Plugins;

interface WidgetPlugin extends GenericPlugin
{
    public function getWidgetComponent(): string;

    public function getWidgetParameters(): array;

    public function getWidth(): int;

    public function getTitle(): ?string;

    public function getIcon(): ?string;
}
