let bread_components = [
    'Bread/Actions',
    'Bread/Browse',
    'Bread/EditAdd',
    'Bread/Read',
];

bread_components.forEach(function (component) {
    var name = component.substring(component.lastIndexOf('/') + 1);
    var name = name.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                    .map(x => x.toLowerCase())
                    .join('-');
    Vue.component('bread-'+name, require('../components/'+component).default);
});

Vue.component('bread-builder-browse', require('../components/Builder/Browse').default);
Vue.component('bread-builder-edit-add', require('../components/Builder/EditAdd').default);
Vue.component('bread-builder-view', require('../components/Builder/View').default);
Vue.component('bread-builder-list', require('../components/Builder/List').default);
Vue.component('bread-builder-validation', require('../components/Builder/ValidationForm').default);