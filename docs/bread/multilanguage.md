# Multilanguage

- Browse filters can be cleared by double-clicking the input
- Browse searching on translatable formfields searches in the currently selected locale
- Use `getTranslated($column, $locale, $fallback, $default)` to get a translated value (which is not the default locale)
- Use `setTranslated($column, $value, $locale)` to set a translated value (which is not the default locale)
- Use `Ctrl` + `up/right` to select the next locale, `Ctrl` + `down/left` to select the previous locale
- Use `dontTranslate()` to prevent things getting translated, `translate()` to reactivate translating