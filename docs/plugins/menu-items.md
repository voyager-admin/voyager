# Menu Items

You can inject menu items to the menu by simply adding a method `registerMenuItems` to your plugin like this:

```php
<?php

namespace Me\MyPlugin;

use Voyager\Admin\Classes\MenuItem;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Manager\Menu as MenuManager;

class MyPlugin implements GenericPlugin
{
    public function registerMenuItems(MenuManager $menumanager) {
        $menumanager->addItems(
            (new MenuItem('My Title', 'icon'))->route('my-route')
        );
    }
}
```

You can also add a divider before or after your item like this:

```php
$menumanager->addItems(
    (new MenuItem())->divider(),
    (new MenuItem('My Title', 'icon'))->route('my-route')
);
```

## Available methods

| **Method**  | **Description**                                                  | **Arguments**                                                                |
|-------------|------------------------------------------------------------------|------------------------------------------------------------------------------|
| __construct | Creates a new Menu item                                          | string title: The title string icon: The name of an icon                     |
| route       | A route to be used                                               | string route: The route key array params: The parameters passed to the route |
| url         | A URL to be used                                                 | string url: The URL                                                          |
| permission  | Display/Hide the item based on a permission                      | string permission: The key of a permission array args: Additional arguments  |
| divider     | Acts as a divider between items                                  | -                                                                            |
| exact       | Apply the active class only when the current URL matches exactly | -                                                                            |
| addChildren | Add children to the item                                         | MenuItem item: One or many children                                          |