let formfields = [
    'Checkboxes',
    'DynamicSelect',
    'MediaPicker',
    'Number',
    'Password',
    'Radios',
    'Relationship',
    'Select',
    'SimpleArray',
    'Slug',
    'Tags',
    'Text',
];

export default (voyager) => {
    formfields.forEach(function (formfield) {
        var name = formfield.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                            .map(x => x.toLowerCase())
                            .join('-');
        voyager.component('formfield-'+name+'-browse', require('../components/Formfields/'+formfield+'/Browse').default);
        voyager.component('formfield-'+name+'-read', require('../components/Formfields/'+formfield+'/Read').default);
        voyager.component('formfield-'+name+'-edit-add', require('../components/Formfields/'+formfield+'/EditAdd').default);
        voyager.component('formfield-'+name+'-builder', require('../components/Formfields/'+formfield+'/Builder').default);
    });

    voyager.component('key-value-form', require('../components/Formfields/KeyValueForm').default);
    voyager.component('array-form', require('../components/Formfields/ArrayForm').default);
};