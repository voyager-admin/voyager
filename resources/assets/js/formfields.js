let formfields = [
    'Checkboxes',
    'DynamicSelect',
    'Number',
    'Password',
    'Radios',
    'Relationship',
    'Select',
    'Tags',
    'Text',
];

formfields.forEach(function (formfield) {
    var name = formfield.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                        .map(x => x.toLowerCase())
                        .join('-');
    Vue.component('formfield-'+name+'-browse', require('../components/Formfields/'+formfield+'/Browse').default);
    Vue.component('formfield-'+name+'-read', require('../components/Formfields/'+formfield+'/Read').default);
    Vue.component('formfield-'+name+'-edit-add', require('../components/Formfields/'+formfield+'/EditAdd').default);
    Vue.component('formfield-'+name+'-builder', require('../components/Formfields/'+formfield+'/Builder').default);
});

Vue.component('key-value-form', require('../components/Formfields/KeyValueForm').default);