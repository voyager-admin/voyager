This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Media manager should not upload all files simultaneously
- Test all formfields, translatable and not translatable
- Implement a set-setting method (?)
- Let formfield push something into the cards actions-slot (Vue 3 Portals?) when editing/adding

## Formfields
- **Dynamic select** needs a "read" part
- **Relationship** not yet (fully) working
- **Color Picker**
- **Media Picker**
- **WYSIWYG Editor**
- **Maps** (external plugin)
- **Date/Time Picker**
- **Array**

## Nice to have
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields **This is actually important**
- Add dark boxshadow variant to tailwind (currently not possible but should be with official dark-mode support)

## Documentation
- Relationship methods NEED TO define the return-type. Otherwise they won't be recognized by the BREAD builder
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Browse filters can be cleared by double-clicking the input
- Browse searching on translatable formfields searches in the currently selected locale
- BREAD menu-badge only shows non-soft-deleted entries
- Translatable: Use `getTranslated($column, $locale, $fallback, $default)` to get a translated value (which is not the default locale)
- Translatable: Use `setTranslated($column, $value, $locale)` to set a translated value (which is not the default locale)
- Translatable: Use `Ctrl` + `up/right` to select the next locale, `Ctrl` + `down/left` to select the previous locale
- Translatable: Use `dontTranslate()` to prevent things getting translated, `translate()` to reactivate translating again
- Settings: Use `Voyager::settings()` with key `null` to get all settings, key `something` to get a whole group (first) or a setting with that name and no group, or key `group.name` to get a settings with that group and key.
- Backing-up a BREAD always uses the current stored state. So when backing-up after changing something (without saving first), the changes will NOT be included in the backup
- Tag input: Press backspace twice to remove latest tag

## Things to note
- When using variables in CSS: ALWAYS use the provided mixins. This allows us to easily extract used variables by a script