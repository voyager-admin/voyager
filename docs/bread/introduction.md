# BREAD Builder

...

## Storage path

By default, BREADs are stored under `storage/voyager/breads`.  
You can change this behavious by calling `setPath` on the BREAD manager in your Service Provider:

```php
use Voyager\Admin\Manager\Breads as BreadManager;

public function boot(BreadManager $breadmanager)
{
    $breadmanager->setPath('your/new/path');
}
```

## Backup

Voyager II allows you to create backups which you can restore at any time.  
To create a backup, simply click `Backup` when browsing your BREADs or when editing a BREAD.

![](../.gitbook/assets/bread-builder/backup.png) 

{% hint style="info" %}
When creating a backup while editing a BREAD, only the actually stored BREAD is backed-up.  
It will **NOT** contain the changes you already made.
{% endhint %}

Backing-up a BREAD creates an additional file named `[table].backup.[date]@[time].json`.  
For example `users.backup.2020-06-22@23-28-10.json`


## Rolling back a backup

To roll back a backup, simply click the `Rollback` button and select the backup you want to rollback.

![](../.gitbook/assets/bread-builder/rollback.png) 

{% hint style="info" %}
When rolling back a BREAD the current version will be backed-up as well allowing you to easily switch between two version.
{% endhint %}

## Layout mapping

In this section you can map your layouts to the different BREA(D) actions.  
Simply select the layout you want to use for browsing, reading, editing and adding.  
Some plugins might override this section, allowing you to select layouts for various conditions.

![](../.gitbook/assets/bread-builder/layout-mapping.png) 