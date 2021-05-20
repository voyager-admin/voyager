This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Test everything on mobile (especially media manager)
- Modal should show scrollbar inside container, not outside
- Measure bread loading times and cache them if necessary
- Media manager: While uploading files, they are shown in every folder
- Media manager: When navigating through folders, it is not possible to use browser-back
- Media manager: X and Y offset for watermarks should be percentage(?)
- Media manager: Lightbox should scale images when they are too big
- Media manager: Allow downloading folder(s)
- Media manager: "Select files" button should say "Upload files"
- Overhaul dynamic select
- Make a command to "remove" translations (transform a value from JSON to normal string)
- Check ALL axios calls (response => response.response)
- Exception handler should only be replaced when logged in (?)
- Show a dark background when using darkmode to prevent white flashing
- Share a lot less stuff with Inertia. Instead, simply pass it to `createVoyager`
- Remove plugins that are no longer installed from `plugins.json`
- Refreshing the page and going back fails (Inertia problem?)
- Test (download) actions
- Catch some common errors (Not a JSON column, column can not be null, Column does not exist, ...)
- Revamp what happens when bread stored or updated. Clear? Edit again?
- Let formfields be overriden by a `component` option
- When validation rules are changed afterwards in settings, validation may fail and thus, settings will not save (including the new validation rules)
- Repeater: Allow `key` to be null and the resulting `modelValue` to be an array instead of an object.

## Formfields
- **Relationship**
- **Color Picker**
- **Date/Time Picker**
- **Masked input**

## Formfields testing

- [X] Checkbox
- [ ] Dynamic Input
- [ ] MediaPicker
- [X] Number
- [X] Password
- [X] Radio
- [X] Select
- [X] SimpleArray
- [X] Slug
- [X] Tags (Add reordering)
- [X] Text

## Documentation
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Ordering only works when there are actual values in the field. Those have to be set by a mutator (or similar)