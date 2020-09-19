<?php

namespace Voyager\Admin\Contracts\Formfields;

interface Formfield
{
    /**
     * Get the name of the formfield.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the type of the formfield.
     *
     * @return string
     */
    public function type(): string;

    /**
     * Get the component name.
     *
     * @return string
     */
    public function getComponentName(): string;

    /**
     * Get the builder component name.
     *
     * @return string
     */
    public function getBuilderComponentName(): string;
}
