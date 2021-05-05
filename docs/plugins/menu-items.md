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