# Repeater

- Browsing only represent a simple text representation as we don't know about the formfields used in views
- When using **exactly** one formfield without a key, value will be stored as `['foo', 'bar', 'baz']`, otherwise as `[{ key: 'foo' }, { key: 'bar' }]`