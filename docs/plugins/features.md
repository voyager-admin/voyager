# Features

```php
namespace My\Formfield;

use Voyager\Admin\Classes\Formfield;

class MyFormfield extends Formfield
{
    // The name of your formfield in a readable form. Can be a translation object
    public function name(): string {
        return 'My Formfield';
        // Or
        return __('mynamespace::name');
    }

    // The name in a slugged form, used for identification. Can not be translated!
    public function type(): string {
        return 'my-formfield';
    }

    // The name of the component
    public function getComponentName(): string {
        return 'my-formfield';
    }

    // The name of the builder component
    public function getBuilderComponentName(): string {
        return 'my-formfield-builder';
    }

    public $notTranslatable;        // Formfield can not be translated
    public $notAsSetting;           // Formfield can not be used as a setting
    public $notInLists;             // Can not be used in Lists
    public $notInViews;             // Can not be used in Views
    public $browseArray;            // Get the data as an array when browsing
    public $noColumns;              // Don't allow normal columns as the field
    public $noComputedProps;        // Don't allow accessors as the field
    public $noRelationships;        // Don't allow relationship objects as the field
    public $noRelationshipProps;    // Don't allow relationship columns as the field
    public $noRelationshipPivots;   // Don't allow relationship pivot columns as the field
}
```