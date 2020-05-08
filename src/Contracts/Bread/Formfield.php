<?php

namespace Voyager\Admin\Contracts\Bread;

interface Formfield
{
    /**
     * Get the name of the formfield.
     *
     * @return string|array
     */
    public function name();

    /**
     * Get the type of the formfield.
     *
     * @return string
     */
    public function type(): string;

    /**
     * Get the (custom) options for a list.
     *
     * @return array
     */
    public function listOptions(): array;

    /**
     * Get the (custom) options for a view.
     *
     * @return array
     */
    public function viewOptions(): array;

    /**
     * Get the data for browsing.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function browse($input);

    /**
     * Get the data for reading.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function read($input);

    /**
     * Get the data for editing.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function edit($input);

    /**
     * Get the data for updating (after editing).
     *
     * @param mixed $input
     * @param mixed $old
     *
     * @return mixed
     */
    public function update($model, $input, $old);

    /**
     * Get the data for adding (eg. default values).
     *
     * @return mixed
     */
    public function add();

    /**
     * Get the data for storing (after adding).
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function store($input);

    /**
     * Called when data was stored (after adding). Useful for relationships
     *
     * @param mixed $model
     * @param array $data
     *
     */
    public function stored($model, $data);

    /**
     * Gets if the formfield can be translated.
     *
     * @return bool
     */
    public function canBeTranslated();

    /**
     * If this formfield can be used as a setting.
     *
     * @return bool
     */
    public function canBeUsedAsSetting();

    /**
     * If this formfield can be used in a list.
     *
     * @return bool
     */
    public function canBeUsedInList();

    /**
     * If this formfield can be used in a view.
     *
     * @return bool
     */
    public function canBeUsedInView();

    /**
     * If array data should be passed to this formfield when browsing
     * This is especially useful for media-pickers and other formfields that don't just show text.
     *
     * @return bool
     */
    public function browseDataAsArray();

    /**
     * If formfield accepts normal table columns.
     *
     * @return bool
     */
    public function allowColumns();

    /**
     * If formfield accepts computed properties.
     *
     * @return bool
     */
    public function allowComputed();

    /**
     * If formfield accepts relationships (method name)
     * This is only useful for relationship-formfields.
     *
     * @return bool
     */
    public function allowRelationships();

    /**
     * If formfield accepts relationship-columns.
     *
     * @return bool
     */
    public function allowRelationshipColumns();

    /**
     * If formfield accepts relationship-pivot columns.
     *
     * @return bool
     */
    public function allowRelationshipPivots();
}
