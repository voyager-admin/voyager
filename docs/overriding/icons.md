# Overriding Icons

To override an icon you simply have to register a component with the name of the icon you want to override, suffixed with `Icon`.  
For example, if you want to override the icon `academic-cap`, register a component named `AcademicCapIcon`:  

```javascript
import { defineAsyncComponent } from 'vue';

voyager.component('AcademicCapIcon', defineAsyncComponent(() => import('path/to/your/icon')));
```

Or, to define a simple non-async component:

```javascript
import { defineComponent, h } from 'vue';

voyager.component('AcademicCapIcon', defineComponent({
    render() {
        return h('svg', { class: '...' });
    }
}));
```