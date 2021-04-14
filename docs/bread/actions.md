# Actions

Actions are the buttons you see when browsing a BREAD.  
There are two kinds of actions: bulk and single.  
By default all actions are single actions.

## Adding an Action

To add an action add the following to your service provider:

```php
<?php

use Voyager\Admin\Classes\Action;
use Voyager\Admin\Manager\Bread as BreadManager;

public function boot(BreadManager $breadmanager)
{
    $action = (new Action('My title', 'my-icon', 'color'))->route('my_route');
    $breadmanager->addAction($action);
}
```

For convenience you can pass as many actions to `addAction()` as you want.  
Color can be one of Tailwinds color palette (gray, red, green, blue, purple, ...) or `accent`  
The title can be a translation-key (for example `generic.action_title`) or a normal string.  
When used as a bulk-action your translation can also contain pluralizable strings.

## Method

By default the HTTP method used when clicking your action is `get`.  
You can set your method by calling `->method('post')` where the parameter is one of `get`, `post`, `put`, `patch` or `delete`.

## Bulk actions

To make your action a bulk-action, simply call `->bulk()`

## Downloads

If you want to generate a file and send it to the user after clicking your action, simply call `->download('my_file.txt')` with the name of the file.  
Make sure to return the file-contents as the response.

## Route

You can provide a route-name as string like `->route('my_route')` or a callback function which will receive the currently used BREAD:

```php
$action = (new Action('My title', 'my-icon', 'color'))
    ->route(function ($bread) {
        return 'voyager.'.$bread->slug.'.edit';
    });
```

## Permission

If you want your action to depend on a permission, use `->permission()`:

```php
$action = (new Action('My title', 'my-icon', 'color'))
    ->permission('delete', [/* additional arguments */]);
```

## Display on BREAD

You can provide a callback function to determine if your action should be displayed on the currently used BREAD.  
To do so, simply chain the method `->displayOnBread(function ($bread) { ... })` to your action and return a boolean value.

```php
$action = (new Action('My title', 'my-icon', 'color'))
    ->displayOnBread(function ($bread) {
        if ($myCondition) {
            return true; // Display this action
        }

        return false; // Don't display this action
    });
```

## Confirm

Sometimes you want the user to confirm the action.  
To do so, simply use `->confirm(...)`:

```php
$action = (new Action('My title', 'my-icon', 'color'))
    ->confirm('Are you sure you want to do this?', 'Are you sure', 'red');
```

The first parameter is the message, the second the title (can be null), the third again is a color from the Tailwind color-palette (or `accent`)  
Like for the action title you can provide your title and message as translation-keys.

## Success

You can display a success notification after your action was completed.  

```php
$action = (new Action('My title', 'my-icon', 'color'))
    ->success('The action finished successfully.', 'Done', 'green');
```

The parameter are the same as for `confirm()`.

## Reload after your action is done

If you want to reload the browse-results after your action was run, simply use `->reloadAfter()`

## Deleted and non-deleted items

When using soft-deletes it is useful to only display the action on soft-deleted or not soft-deleted entries.  
This can be done by calling either `->displayDeletable()` or `displayRestorable()` respectively.  

