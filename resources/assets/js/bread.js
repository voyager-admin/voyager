let components = [
    'Bread/Actions',
    'Bread/Browse',
    'Bread/EditAdd',
    'Bread/Read',
];

export default (voyager) => {
    components.forEach(function (component) {
        var name = component.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                            .map(x => x.toLowerCase())
                            .join('-');
        voyager.component(name, require('../components/'+component).default);
    });

    voyager.component('bread-builder-browse', require('../components/Builder/Browse').default);
    voyager.component('bread-builder-edit-add', require('../components/Builder/EditAdd').default);
    voyager.component('bread-builder-view', require('../components/Builder/View').default);
    voyager.component('bread-builder-list', require('../components/Builder/List').default);
    voyager.component('bread-builder-validation', require('../components/Builder/ValidationForm').default);
};