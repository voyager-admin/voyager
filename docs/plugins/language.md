# Language

If you want to access your localization strings from inside your Vue components, simply tell Voyager they exist:

```php
<?php

namespace Me\MyPlugin;

use Illuminate\Support\ServiceProvider;
use Voyager\Admin\Facades\Voyager as Voyager;
use Voyager\Admin\Manager\Plugins as PluginManager;

class MyPluginServiceProvider extends ServiceProvider
{
    public function boot(PluginManager $pluginmanager)
    {
        $pluginmanager->addPlugin(\Me\MyPlugin\MyPlugin::class);

        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'my-plugin');
        Voyager::addTranslations('my-plugin', 'generic');
    }
}
```

You can now access the strings like `__('my-plugin::generic.my-string')`