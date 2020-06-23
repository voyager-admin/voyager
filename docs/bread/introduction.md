# BREAD Builder

The BREAD builder is the heart of Voyager.  
...


## Backup
Voyager II allows you to create backups which you can restore at any time.  
To create a backup, simply click `Backup` when browsing your BREADs or when editing a BREAD.

{% hint style="info" %}
When creating a backup when editing a BREAD, only the actually stored BREAD is backed-up, **NOT** the changes you already made.
{% endhint %}

Backing-up a BREAD creates an additional file named `[table].backup.[date].[time].json`. 
For example `users.backup.2020-06-22@23-28-10.json`


## Rollback a backup
To rollback to a certain backup, select the backup you want to restore to from the `Rollback` dropdown.  
Your current BREAD is backed-up aswell so you can swap between two versions.