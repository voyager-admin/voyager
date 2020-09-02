let components = [
    'Icon',
    'LocalePicker',
    'MenuWrapper',
    'MenuItem',
    'Search',
    'Tooltips',
    'UserDropdown',
];

components.forEach(function (component) {
    var name = component.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                        .map(x => x.toLowerCase())
                        .join('-');
    Vue.component(name, require('../components/Layout/'+component).default);
});