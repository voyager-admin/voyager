/*require('./notify');
require('./bread');
require('./formfields');
require('./layout');
require('./ui');

Vue.component('settings-manager', require('../components/Settings/Manager').default);
Vue.component('plugins-manager', require('../components/Plugins/Manager').default);
Vue.component('login', require('../components/Auth/Login').default);
*/

require('./helper/array');

import { createApp } from 'vue';

// Vendor
// https://github.com/component/debounce
window.debounce = require('debounce');

// https://github.com/simov/slugify ~7kb
window.slugify = require('slugify');

// https://github.com/katlasik/mime-matcher
import MimeMatcher from 'mime-matcher';

// Multi language
import Multilanguage from './multilanguage';

// Directives
import ClickOutside from './directives/click-outside';
import Focus from './directives/focus';
import ScrollTo from './directives/scroll-to';
import Tooltip from './directives/tooltip';

// Helper mixins
import MiscMixin from './mixins/misc';
import StringMixin from './mixins/strings';
import TypeMixin from './mixins/types';
import UrlMixin from './mixins/url';

import Layout from './layout';
import UI from './ui';
import Bread from './bread';
import Formfields from './formfields';

import { Notification, Notify } from './notify';
import Store from './store';

// Components
import SettingsManager from '../components/Settings/Manager';
import PluginsManager from '../components/Plugins/Manager';
import Login from '../components/Auth/Login';

window.createAndMountVoyager = function (data = {}, el = '#voyager') {
    const voyager = createApp({
        name: 'Voyager',
        props: ['routes', 'localization', 'locales', 'locale', 'initial_locale', 'breads', 'formfields', 'debug', 'jsonOutput', 'menuItems'],
        created: function () {
            var vm = this;

            for (const key in data) {
                if (this.$store.hasOwnProperty(key)) {
                    this.$store[key] = data[key];
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                vm.$store.pageLoading = false;
            });
        },
    }, data);

    voyager.config.globalProperties.debounce = debounce;
    voyager.config.globalProperties.slugify = window.slugify;
    voyager.config.globalProperties.MimeMatcher = MimeMatcher;

    voyager.config.globalProperties.Status = Object.freeze({
        Pending  : 1,
        Uploading: 2,
        Finished : 3,
        Failed   : 4,
    });

    voyager.use(Multilanguage, {
        localization: data.localization,
        initial_locale: data.initial_locale,
        locale: data.locale,
        locales: data.locales,
    });

    voyager.directive('click-outside', ClickOutside);
    voyager.directive('focus', Focus);
    voyager.directive('scroll-to', ScrollTo);
    voyager.directive('tooltip', Tooltip);

    voyager.mixin(MiscMixin);
    voyager.mixin(StringMixin);
    voyager.mixin(TypeMixin);
    voyager.mixin(UrlMixin);

    voyager.config.globalProperties.$store = Store;

    voyager.config.errorHandler = (error, vm, info) => {
        Store.handleError(error, vm, info);
    };
    voyager.config.warnHandler = (warning, vm, trace) => {
        Store.handleWarning(warning, vm, trace);
    };

    Layout(voyager);
    UI(voyager);
    Bread(voyager);
    Formfields(voyager);
    voyager.config.globalProperties.$notify = Notify;
    voyager.config.globalProperties.$notification = Notification;

    voyager.component('settings-manager', SettingsManager);
    voyager.component('plugins-manager', PluginsManager);
    voyager.component('login', Login);

    voyager.mount(el);
};