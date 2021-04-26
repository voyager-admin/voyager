
import { usePage } from '@inertiajs/inertia-vue3';

let localization = null;

export default {
    
    install(app) {
        app.config.globalProperties.__ = function (key, replace = {}) {
            return this.trans(key, replace);
        };
        app.config.globalProperties.trans = function (key, replace = {}) {
            if (localization === null) {
                localization = JSON.parse(usePage().props.value.localization);
            }
            let translation = key.split('.').reduce((t, i) => t[i] || null, localization);

            if (!translation) {
                return key;
            }

            for (var placeholder in replace) {
                translation = translation.replace(new RegExp(':'+placeholder, 'g'), replace[placeholder]);
            }

            return translation;
        };
        app.config.globalProperties.trans_choice = function (key, count = 1, replace = {}) {
            if (key === null) {
                return key;
            }
            if (localization === null) {
                localization = usePage().props.value.localization;
            }
            let translation = key.split('.').reduce((t, i) => t[i] || key, localization).split('|');

            translation = count > 1 ? translation[1] : translation[0];

            translation = translation.replace(`:num`, count);

            for (var placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
        };
        app.config.globalProperties.get_translatable_object = function (input) {
            if (this.isString(input) || this.isNumber(input) || this.isBoolean(input)) {
                try {
                    input = JSON.parse(input);
                } catch { }
                if (!this.isObject(input)) {
                    var value = input;
                    input = {};
                    input[usePage().props.value.initial_locale] = value;
                }
            } else if (!this.isObject(input)) {
                input = {};
            }

            return input;
        };
        app.config.globalProperties.translate = function (input, once = false, default_value = '') {
            if (!this.isObject(input)) {
                input = this.get_translatable_object(input);
            }
            if (this.isObject(input)) {
                return input[once ? usePage().props.value.initial_locale : usePage().props.value.locale] || default_value;
            }

            return input;
        };
        app.config.globalProperties.nextLocale = function () {
            var index = usePage().props.value.locales.indexOf(usePage().props.value.locale);
            if (index >= usePage().props.value.locales.length - 1) {
                usePage().props.value.locale = usePage().props.value.locales[0];
            } else {
                usePage().props.value.locale = usePage().props.value.locales[index + 1];
            }
        };
        app.config.globalProperties.previousLocale = function () {
            var index = usePage().props.value.locales.indexOf(usePage().props.value.locale);
            if (index <= 0) {
                usePage().props.value.locale = usePage().props.value.locales[usePage().props.value.locales.length - 1];
            } else {
                usePage().props.value.locale = usePage().props.value.locales[index - 1];
            }
        };
    }
};