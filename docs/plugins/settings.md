# Settings

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