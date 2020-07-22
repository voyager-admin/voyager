# Plugins
 
Plugins provide additional functionality for Voyager.  
They are ordinary composer packages and will be installed through the console.  

## Managing plugins

### Installation

To install a plugin, simply click `Search Plugins` and find the plugin you want to install.  
Once found, double click the textbox next to it to copy the according command to your clipboard.  
Next, open your console and run the command.  
After that, your plugin will appear on your plugins page as disabled. Click `Enable` to enable it.  
After that your plugin is active.  
There might be additional steps to install a plugin. Please revise the instructions by clicking the `Instructions` button or by visiting the plugin page.

## Plugin Types

Plugins can be of the following types, giving them special powers

### Authentication Plugins

These plugin integrate different ways to log-in to Voyager.  
For example through OAuth or social-media.  
They can also provide two-factor-authorization.

### Authorization Plugins

Authorization plugins authorize actions and users.  
Those plugins can integrate various third-party packages like `spatie/permissions`.

### Formfield Plugins

Provides additional formfields for the BREAD builder like WYSIWYG editors and other non-standard inputs.

### Generic Plugins

A generic plugin does not fit any of the other types.

### Theme Plugins

Theme plugins allow you to customize the look and feel of Voyager.  
After a theme was installed you can preview it by clicking `Preview.  
You won't see the theme anymore after reloading your page.

![](./.gitbook/assets/plugins/theme-preview.png) 

### Widget Plugins

Widget plugins simply provide a widget which is shown on your dashboard.  
For example stats about users registered or Google Analytics.

![](./.gitbook/assets/plugins/widget.png) 

## Developing plugins

We created templates for all types of plugins on Github to get you started easily:
- [Authentication](#)
- [Authorization](#)
- [Formfield](#)
- [Generic](#)
- [Theme](https://github.com/voyager-admin/theme-boilerplate)
- [Widget](https://github.com/voyager-admin/widget-boilerplate)

{% hint style="info" %}
One package can provide multiple plugins.  
For example, a plugin could provide multiple widgets or even different types of plugins like a theme and widgets.  
All plugins can be enabled/disabled independently.
{% endhint %}

### Providing menu items

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

### Providing settings

A plugin can provide multiple settings. To do so implement a method `registerSettings` like this:

```php
public function registerSettings()
{
    return [
        [
            'type'          => 'text',
            'group'         => 'My group',
            'name'          => 'My setting',
            'key'           => 'my_setting',
            'value'         => 'Value',
            'translatable'  => false,
            'info'          => 'This is a setting provided by a plugin',
            'options'       => [],
            'validation'    => [],
        ],
        [
            'type'          => 'text',
            'group'         => 'My group',
            'name'          => 'My second setting',
            'key'           => 'my_second_setting',
            'value'         => 'Value',
            'translatable'  => false,
            'info'          => 'This is another setting provided by a plugin',
            'options'       => [],
            'validation'    => [],
        ]
    ];
}
```

Make sure to always return an array containing settings (as an array).  
The best way to generate this setting is to simply create it through the UI and then copy/paste it from your `settings.json` file.