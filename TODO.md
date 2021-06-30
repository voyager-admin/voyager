This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Test everything on mobile (especially media manager)
- Modal should show scrollbar inside container, not outside
- Measure bread loading times and cache them if necessary
- Media manager: While uploading files, they are shown in every folder
- Media manager: When navigating through folders, it is not possible to use browser-back
- Media manager: X and Y offset for watermarks should be percentage(?)
- Make a command to "remove" translations (transform a value from JSON to normal string)
- Show a dark background when using darkmode to prevent white flashing
- Remove plugins that are no longer installed from `plugins.json`
- Test (download) actions
- Catch some common errors (Not a JSON column, column can not be null, Column does not exist, Prop is hidden, ...)
- Revamp what happens when bread stored or updated. Clear? Edit again?
- Link to related item when browsing
- When querying a xMany relationship, it should only display those
- Let dynamic input provide validation rules (?)
- Remove margin-bottom from card (This requires every usage to be aligned)

## Formfields testing

- [X] Checkbox
- [ ] Dynamic Input
- [ ] MediaPicker
- [X] Number
- [X] Password
- [X] Radio
- [X] Select
- [X] SimpleArray
- [X] Slider
- [X] Slug
- [X] Tags
- [X] Text

## Documentation
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Ordering only works when there are actual values in the field. Those have to be set by a mutator (or similar)

