# Media Picker

The media-picker provides an easy way to the [media manager](../../media-manager.md)

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