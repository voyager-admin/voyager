# CSS

## Mixins

We use a variety of mixins for all kind of colors (border, text, background ...)  
Those mixins easily generate a CSS variable which can be overriden by theme plugins.

### Background color

...

### Text color

...

### Border color

...

As a rule of thumb: you should never directly include a color, for example `color: red;`.  
Instead use the appropriate mixin, give it a reasonable name and provide a default value.  
For example:  
```
@import "mixins/bg-color";
@import "mixins/text-color";

.body {
    @include bg-color(bg-color, 'colors.gray.500');
    @include text-color(text-color, 'colors.red.500');
}
```