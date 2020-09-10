This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Test everything on mobile (especially media manager)
- Datepicker range needs to validate time when range is same-day
- Modal should show scrollbar inside container, not outside
- Measure bread loading times and cache them if necessary
- Replace BREAD rollback dropdown so tables can get `overflow-x-auto`
- Ordering settings does not really work
- Media manager: While uploading files, they are shown in every folder
- Tooltips don't update value when changed
- Ordering only works when there are actual values in the field. Those have to be set by a mutator (or similar)
- Replace all watchers with `$watch` in created-hook as Vue3 doesn't support nested watchers in options API

## Formfields
- **Relationship** support polymorphic relationships
- **Color Picker**
- **Date/Time Picker**
- **Masked input**
- **Repeater** should "simply" use another view

## Documentation
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Press escape or double-click (most) search inputs to clear them

## Tested

- Slug formfield, both translatable and non-translatable