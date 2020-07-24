# Media Manager



## Selecting files

You can select multiple files by pressing `Ctrl` while clicking your desired files.

## Mime types

You can set which mime-types are shown in the media-manager by adding mimes to the `media.mime-types` setting.  
Read more about this [here](./bread/formfields/media-picker.md#mime-types).

## Image optimization

By default all images are optimized through [spatie/laravel-image-optimization](https://github.com/spatie/laravel-image-optimizer).  
You can turn this off in the settings.  
Please follow the [official documentation](https://github.com/spatie/image-optimizer#optimization-tools) on how to install various optimization plugins.

## Thumbnails

You can generate thumbnails by adding settings to a group called "Thumbnails".  
Add a formfield `Dynamic select` and enter `voyager.get-thumbnail-options` as the route in the options.  
The name of the setting will be the suffix of your filename.  
For example, creating a setting called `small` will generate a thumbnail named `myfile_small.jpg`.


Save your settings and refresh the page.  
You will now be able to select the method (fit, crop or resize) and enter the required parameter.

## Watermark

You can automatically add a watermark to each uploaded image.  
To do this, go to `Settings` and select the `Watermark` tab.  
There you can pick an image, set the size, position and opacity of the watermark.  
If you don't want to add watermarks anymore, simply remove the watermark image from the media-picker.