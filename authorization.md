# Authorization

The following names/parameters are used to authorize users:

## BREAD Builder

- `browse`      => `Bread::class`    => Browse all BREADs
- `browse`      => `Bread::instance` => Browse a BREAD
- `create`      => `Bread::class, table-name`    => Create a BREAD
- `edit`        => `Bread::instance` => Edit/update a BREAD
- `delete`      => `Bread::instance` => Delete a BREAD
- `backup`      => `Bread::instance` => Backup a BREAD
- `restore`     => `Bread::instance` => Restore a backed-up BREAD

## BREAD

- `browse`      => `Model::class, Bread::instance`    => Browse a BREAD
- `browse`      => `Model::instance, Bread::instance` => Browse a BREAD item
- `create`      => `Model::class, Bread::instance`    => Create a BREAD item
- `edit`        => `Model::instance, Bread::instance` => Edit/update a BREAD
- `delete`      => `Model::instance, Bread::instance` => Delete a BREAD item
- `soft_delete` => `Model::instance, Bread::instance` => Soft-delete a BREAD item
- `restore`     => `Model::instance, Bread::instance` => Restore a soft-deleted BREAD item