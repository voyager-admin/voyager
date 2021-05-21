
import { usePage } from '@inertiajs/inertia-vue3';
import $store from './store';

let localization = null;

export default {
    
    install(app) {
        app.config.globalProperties.__ = function (key, replace = {}) {
            return this.trans(key, replace);
        };
        app.config.globalProperties.trans = function (key, replace = {}) {
            let translation = key.split('.').reduce((t, i) => t[i] || null, $store.localization);

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
            let translation = key.split('.').reduce((t, i) => t[i] || key, $store.localization).split('|');

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
                    input[this.$store.initialLocale] = value;
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
                return input[once ? this.$store.initialLocale : this.$store.locale] || default_value;
            }

            return input;
        };
        app.config.globalProperties.nextLocale = function () {
            var index = this.$store.locales.indexOf(this.$store.locale);
            if (index >= this.$store.locales.length - 1) {
                this.$store.locale = this.$store.locales[0];
            } else {
                this.$store.locale = this.$store.locales[index + 1];
            }
        };
        app.config.globalProperties.previousLocale = function () {
            var index = this.$store.locales.indexOf(this.$store.locale);
            if (index <= 0) {
                this.$store.locale = this.$store.locales[this.$store.locales.length - 1];
            } else {
                this.$store.locale = this.$store.locales[index - 1];
            }
        };
    }
};