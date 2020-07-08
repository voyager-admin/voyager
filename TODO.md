This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Media manager should not upload all files simultaneously
- Media manager styling breaks on small screens
- Let formfield push something into the cards actions-slot (Vue 3 Portals?) when editing/adding
- Datepicker range needs to validate time when range is same-day
- Modal should show scrollbar inside container, not outside

## Formfields
- **Relationship** not yet (fully) working
- **Color Picker**
- **Media Picker**
- **Date/Time Picker**
- **Masked input**
- **Repeater**

## Nice to have
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields **This is actually important**
- Add dark boxshadow variant to tailwind (currently not possible but should be with official dark-mode support)

## Documentation
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing