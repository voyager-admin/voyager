export default {
    install: function (app, config) {
        app.config.globalProperties.__ = function (key, replace = {}) {
            return this.trans(key, replace);
        };
        app.config.globalProperties.trans = function (key, replace = {}) {
            let translation = key.split('.').reduce((t, i) => t[i] || null, config.localization);

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
            let translation = key.split('.').reduce((t, i) => t[i] || key, config.localization).split('|');

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
                } catch {
                    var value = input;
                    input = {};
                    input[this.$store.initial_locale] = value;
                }
            } else if (!this.isObject(input)) {
                input = {};
            }

            if (input && this.isObject(input)) {
                this.$store.locales.forEach(function (locale) {
                    if (!input.hasOwnProperty(locale)) {
                        //Vue.set(input, locale, '');
                        input[locale] = '';
                    }
                });
            }

            return input;
        };
        app.config.globalProperties.translate = function (input, once = false, default_value = '') {
            if (!this.isObject(input)) {
                input = this.get_translatable_object(input);
            }
            if (this.isObject(input)) {
                return input[once ? this.$store.initial_locale : this.$store.locale] || default_value;
            }

            return input;
        };

        var vm = this;
        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey) {
                if (e.code == 'ArrowRight' || e.code == 'ArrowDown') {
                    //vm.nextLocale();
                    console.log('Next');
                } else if (e.code == 'ArrowLeft' || e.code == 'ArrowUp') {
                    //vm.previousLocale();
                    console.log('Next');
                }
            }
        });
    }
    /*
        nextLocale: function () {
            if (this.index == this.locales.length - 1) {
                this.index = 0;
            } else {
                this.index = this.index + 1;
            }

            this.locale = this.locales[this.index];
        },
        previousLocale: function () {
            if (this.index == 0) {
                this.index = this.locales.length - 1;
            } else {
                this.index = this.index - 1;
            }

            this.locale = this.locales[this.index];
        },
    },
    created: function () {
        var vm = this;
        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey) {
                if (e.code == 'ArrowRight' || e.code == 'ArrowDown') {
                    vm.nextLocale();
                } else if (e.code == 'ArrowLeft' || e.code == 'ArrowUp') {
                    vm.previousLocale();
                }
            }
        });
    }*/
};