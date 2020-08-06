<?php

namespace Voyager\Admin\Classes;

class Action
{
    public $title;
    public $icon;
    public $color = 'accent';
    public $method = 'get';
    public $download = false;
    public $file_name = '';
    public $bulk = false;
    public $confirm;
    public $success;
    public $permission;
    public $route_callback;
    public $callback;
    public $display_deletable = null;
    public $reload_after = false;

    /**
     * Create a new action.
     *
     * @param string $title The title as a string or translation-key.
     * @param string $icon  The icon of the button. "null" for no icon.
     * @param string $color The color of the button. Defaults to "accent".
     * @return self
     */
    public function __construct($title, $icon = null, string $color = 'accent')
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->color = $color;

        return $this;
    }

    /**
     * Set the method that is used when calling/clicking the action.
     *
     * @param string $method The method. Either get, post, put, patch or delete.
     * @return self
     */
    public function method(string $method)
    {
        if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
            throw new \Exception('Method "'.$method.'" can not be used in an action!');
        }
        $this->method = $method;

        return $this;
    }

    /**
     * Makes the action a download action.
     *
     * @param string $file_name The name of the file. Ex: download.pdf
     * @return self
     */
    public function download(string $file_name)
    {
        $this->download = true;
        $this->file_name = $file_name;

        return $this;
    }

    /**
     * Resolve the route for a BREAD.
     *
     * @param callable|string $route A callback resolving the route or the route name as a string.
     * @return self
     */
    public function route($route)
    {
        $this->route_callback = $route;

        return $this;
    }

    /**
     * Confirm the execution of the action.
     *
     * @param string $message The message as a string or translation-key.
     * @param string $title   The title as a string or translation-key.
     * @param string $color   The color of the notification. Defaults to "accent".
     * @return self
     */
    public function confirm(string $message, string $title = null, string $color = 'accent')
    {
        $this->confirm = [
            'title'     => $title,
            'message'   => $message,
            'color'     => $color,
        ];

        return $this;
    }

    /**
     * Display a message after executing the action.
     *
     * @param string $message The message as a string or translation-key.
     * @param string $title   The title as a string or translation-key.
     * @param string $color   The color of the notification. Defaults to "accent".
     * @return self
     */
    public function success(string $message, string $title = null, string $color = 'accent')
    {
        $this->success = [
            'title'     => $title,
            'message'   => $message,
            'color'     => $color,
        ];

        return $this;
    }

    /**
     * Make the action a bulk-action.
     * 
     * @return self
     */
    public function bulk()
    {
        $this->bulk = true;

        return $this;
    }

    /**
     * Authorize the action based on a permission.
     *
     * @param string $ability   The ability.
     * @param array  $arguments The arguments.
     * @return self
     */
    public function permission(string $ability, array $arguments = [])
    {
        $this->permission = [
            'ability'     => $ability,
            'arguments'   => $arguments,
        ];

        return $this;
    }

    /**
     * Sets if this action should be displayed on a BREAD.
     *
     * @param callable $callback A callback function which gets the BREAD as an arguments.
     * @return self
     */
    public function displayOnBread(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * This action should be displayed on deleted entries. Will receive the amount of deleted entries when it's a bulk-action (hidden if 0).
     *
     * @return self
     */
    public function displayDeletable()
    {
        $this->display_deletable = true;

        return $this;
    }

    /**
     * This action should be displayed on deleted entries. Will receive the amount of deleted entries when it's a bulk-action (hidden if 0).
     *
     * @return self
     */
    public function displayRestorable()
    {
        $this->display_deletable = false;

        return $this;
    }

    /**
     * Makes BREAD browse reload once the action finished.
     *
     * @return self
     */
    public function reloadAfter() {
        $this->reload_after = true;

        return $this;
    }
}
