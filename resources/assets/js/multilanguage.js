Vue.prototype.$language = new Vue({
    data: {
        locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        initial_locale: document.getElementsByTagName('html')[0].getAttribute('lang'),
        locales: document.getElementsByTagName('html')[0].getAttribute('locales').split(','),
        localePicker: false,
        localization: [],
        index: 0,
    },
    watch: {
        locale: function (locale) {
            this.index = this.locales.indexOf(locale);
        }
    },
    methods: {
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
        }
    },
    created: function () {
        var vm = this;
        document.addEventListener('keydown', function (e) {
            if (event.ctrlKey) {
                if (e.keyCode == 38 || e.keyCode == 39) {
                    vm.nextLocale();
                } else if (e.keyCode == 37 || e.keyCode == 40) {
                    vm.previousLocale();
                }
            }
        });
    }
});

Vue.mixin({
    methods: {
        get_translatable_object: function (input) {
            if (this.isString(input) || this.isNumber(input) || this.isBoolean(input)) {
                try {
                    input = JSON.parse(input);
                } catch {
                    var value = input;
                    input = {};
                    input[this.$language.initial_locale] = value;
                }
            } else if (!this.isObject(input)) {
                input = {};
            }

            if (input && this.isObject(input)) {
                this.$language.locales.forEach(function (locale) {
                    if (!input.hasOwnProperty(locale)) {
                        Vue.set(input, locale, '');
                    }
                });
            }

            return input;
        },

        translate: function (input, once = false, default_value = '') {
            if (!this.isObject(input)) {
                input = this.get_translatable_object(input);
            }
            if (this.isObject(input)) {
                return input[once ? this.$language.initial_locale : this.$language.locale] || default_value;
            }

            return input;
        },

        trans: function (key, replace = {})
        {
            let translation = key.split('.').reduce((t, i) => t[i] || null, this.$language.localization);

            if (!translation) {
                if (this.$store.debug) {
                    console.log('Translation with key "'+key+'" does not exist.');
                }

                return key;
            }

            for (var placeholder in replace) {
                translation = translation.replace(new RegExp(':'+placeholder, 'g'), replace[placeholder]);
            }

            return translation;
        },

        __: function (key, replace = {})
        {
            return this.trans(key, replace);
        },

        trans_choice: function (key, count = 1, replace = {})
        {
            if (key === null) {
                return key;
            }
            let translation = key.split('.').reduce((t, i) => t[i] || key, this.$language.localization).split('|');

            translation = count > 1 ? translation[1] : translation[0];

            translation = translation.replace(`:num`, count);

            for (var placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
        },
    }
});