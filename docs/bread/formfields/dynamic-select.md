# Dynamic select

The dynamic select is a simple yet powerful formfield allowing you to provide select(s) and number or text inputs based on the currently selected value(s).  
To retreive the possible options, Voyager will fetch a route you define in the formfield options.

### Knowing the BREAD action

The incoming request contains a parameter `bread_action` which can be `query`, 'browse`, `edit` or `add`.

### Examples

Here are some examples that show you what you can create with the dynamic select.

#### Simple select

This example simply shows a select with 3 options. In this case, the value stored in the database will only contain the key of the selected option

```php
[
    'public'    => 'Public disk',
    'local'     => 'Local disk',
    's3'        => 'Amazon S3 Storage',
];
```

**TODO: Add screenshot**

If you want to get a key-value pair for this select, you can do the following:

```php
[
    'disk' => [
        'public'    => 'Public disk',
        'local'     => 'Local disk',
        's3'        => 'Amazon S3 Storage',
    ]
];
```

Now the value stored in your database will be `{ "disk": "public" }` when selecting `Public disk` and so on.

#### Various input types

This example will display a select named `a_select`, a text input named `a_text` and a number input named `a_number`

```php
[
    "a_select" => [
        "key_1" => "Value 1",
        "key_2" => "Value 2",
        "key_3" => "Value 3",
    ],
    "a_text" => [
        "type"          => "text",
        "title"         => "A text input", // Optional
        "placeholder"   => "Please enter a text", // Optional
        "value"         => "Default text", // Optional. Use if you want to set a default value or override the entered value
    ],
    "a_number" => [
        "type"          => "number",
        "title"         => "A number input", // Optional
        "placeholder"   => "Please enter a number between 1 and 10", // Optional
        "min"           => 1, // Optional
        "max"           => 10, // Optional
        "value"         => 0, // Optional. Use if you want to set a default value or override the entered value
    ]
]
```

**TODO: Add screenshot**

#### Conditional inputs

When you want to display selects/inputs based on a condition, for example the previous value.  
This example shows a simple input which prompts you to enter your country, then the region in that country and finally the city:

```php
<?php

use Voyager\Admin\Classes\DynamicSelect;

Route::post('/get-country-inputs', function (Request $request) {
    $default_country = null; // Change this to any string for a default value.
    $country = $request->input('country', $default_country);
    $region = $request->input('region', null);
    $select = (new DynamicSelect())->addText('country', 'Enter your country', 'Country', $default_country);

    if ($country) {
        $select->addText('region', 'Enter the region in '.$country, 'Region in '.$country);

        if ($region) {
            $select->addText('city', "Enter the city in $country / $region", "City in $country / $region");
        }
    }

    return $select;
});
```

**TODO: Add screenshot**

While this example does not make much sense, you get the idea - you can create the inputs based on various conditions to your liking.