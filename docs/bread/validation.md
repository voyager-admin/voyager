# Validation

## Validating multi language inputs

You can validate either the current app locale OR all locales by setting the option to `Validate all locales` or `Validate current locale` in the layout options.  
With this you can use `Voyager::addLocale('locale')` to dynamically add languages for users.  
For example user A speaks english and german, you could run
```php
Voyager::addLocale('en');
Voyager::addLocale('de');
```
and select `Validate all locales` to force the user to enter the data in both his languages.