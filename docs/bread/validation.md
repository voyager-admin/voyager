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

## Validating array elements

#### Validating 1-dimensional arrays

Given then following array:

```php
[
    'name'  => 'admin',
    'email' => 'foo@bar.baz',
]
```

you can validate any fields by using `.name:required` or `.email:email` respectively.

#### Multi-dimensional array

Given the following array of users:

```php
[
    [
        'name'  => 'Admin',
        'email' => 'foo@bar.baz',
    ],
    [
        'name'  => 'User',
        'email' => 'e@ma.il',
    ],
]
```

You can validate any fields in **all** elements (users in this case) by using `*.name:required` and `*.email:email`