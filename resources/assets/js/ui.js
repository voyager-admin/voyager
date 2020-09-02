/**********************************************************************
 *  
 *  Global UI Functionality for Voyager
 * 
 *  This is custom js functionality for the Voyager UI may include
 *  functionality for dropdowns, modals, textbox, etc...
 * 
 **********************************************************************/


let components = [
    'Alert',
    'Badge',
    'Card',
    'Collapsible',
    'ColorPicker',
    'DateTime',
    'Dropdown',
    'IconPicker',
    'LanguageInput',
    'MediaManager',
    'Modal',
    'Notifications',
    'Pagination',
    'PlusMinusInput',
    'SlideIn',
    'Tabs',
    'TagInput',
];

components.forEach(function (component) {
    var name = component.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                        .map(x => x.toLowerCase())
                        .join('-');
    Vue.component(name, require('../components/UI/'+component).default);
});