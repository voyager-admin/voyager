# Preferences

A plugin can store and access preferences which are stored on the filesystem.  
To write a preferences, simply call

```php
// Not translatable
$this->preferences->set('my_key', 'My value');

// Translatable
$this->preferences->set('my_key', 'My value', 'en');
$this->preferences->set('my_key', 'Mein Wert', 'de');
```

inside your plugin class.  
To get a preference, call:

```php
$this->preferences->get('my_key', 'Default value');
```

If your preference is translatable, it will be automatically translated to the current locale.  
If you don't want your value to be translated, simply pass `false` as the third parameter or the locale to translate it to another locale.

## Clearing preferences

You can delete one preference by calling `$this->preferences->remove('my_key');` or clear all preferences by calling `$this->preferences->removeAll();`