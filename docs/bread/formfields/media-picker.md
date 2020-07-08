# Media Picker

The media-picker provides an easy way to use the [media manager](../../media-manager.md) as a powerful formfield for your BREADs.

## Picking files

There are two ways to pick files.  
1. Double click the file you want to pick
2. Select multiple files and hit the button "Select X files"

{% hint style="info" %}
Files that are already picked will be removed when picking them again
{% endhint %}

## Options

### Max

Defines the maximum amount of files that can be picked.  
0 means infinity.  
When using 1 as the maximum, the currently picked file will automatically be replaced when picking another.

## Using media files in your model

Voyager provides a simple way to retreive files in your model.  
To use it, include the trait:

```php
<?php

namespace App;

use Voyager\Admin\Traits\HasMedia;

class MyModel
{
    use HasMedia;

    // ...
}
```

You can now call `media(field)` where `field` is the column you use in the formfield.  
`media()` will **always** return a [collection](https://laravel.com/docs/collections) containing your files.  

The following properties exist on each item in the collection:  
- URL
- Size
- Relative path
- ...

### Meta properties

Meta properties are automatically translated and injected into the items.  
If you defined a meta prop `title` you can access it like `$model->media('field')->first()->title`