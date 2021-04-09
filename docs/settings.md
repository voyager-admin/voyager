# Settings

- Use `Voyager::settings()` with key `null` to get all settings, key `something` to get a whole group (first) or a setting with that name and no group, or key `group.name` to get a settings with that group and key.


## Writing settings from code

Use `SettingsManager::set('key', 'value')`

Use `SettingsManager::set('key', ['en' => 'English value', 'de' => 'Deutscher Wert'])` to set multiple locales or
`SettingsManager::set('key', 'English value', 'en')` to set a single locale


## Batch update

By default, when calling `SettingsManager::set(...)` the settings file will be stored on the disk.  
You can prevent this by passing an optional fourth parameter `save` as `false`.  
When you are done setting all your settings, you have to call `SettingsManager::save()`.