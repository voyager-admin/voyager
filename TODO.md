This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Test everything on mobile (especially media manager and listbox/relationship select)
- Datepicker range needs to validate time when range is same-day
- Modal should show scrollbar inside container, not outside
- Add some sort of action buttons
- Make listbox search and pagination sticky
- Discuss who/what should decide which layout is used for an action
- Discuss how/why widgets are displayed (permission, order, ...)

## Formfields
- **Relationship** not yet (fully) working
- **Color Picker**
- **Media Picker**
- **Date/Time Picker**
- **Masked input**
- **Repeater** should "simply" use another view

## Nice to have
- Add dark boxshadow variant to tailwind (currently not possible but should be with official dark-mode support)

## Documentation
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Press escape or double-click (most) search inputs to clear them

## Tested

- Slug formfield, both translatable and non-translatable