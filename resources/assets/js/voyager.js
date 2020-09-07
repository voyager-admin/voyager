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

// Helper mixins
import MiscMixin from './mixins/misc';
import StringMixin from './mixins/strings';
import TypeMixin from './mixins/types';
import UrlMixin from './mixins/url';

import Layout from './layout';
import UI from './ui';
import Bread from './bread';
import Formfields from './formfields';
import Transitions from './transitions';

import { Notification, Notify } from './notify';
import Eventbus from './eventbus';
import Store from './store';

// Components
import SettingsManager from '../components/Settings/Manager';
import PluginsManager from '../components/Plugins/Manager';
import Login from '../components/Auth/Login';

let voyager;

window.createVoyager = function (data = {}) {
    voyager = createApp({
        name: 'Voyager',
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
    });

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
    });

    voyager.directive('click-outside', ClickOutside);
    voyager.directive('focus', Focus);
    voyager.directive('scroll-to', ScrollTo);

    voyager.mixin(MiscMixin);
    voyager.mixin(StringMixin);
    voyager.mixin(TypeMixin);
    voyager.mixin(UrlMixin);

    voyager.config.globalProperties.$store = Store;
    voyager.config.globalProperties.$eventbus = Eventbus;
    window.$eventbus = Eventbus;

    //voyager.config.errorHandler = Store.handleError;
    //voyager.config.warnHandler = Store.handleWarning;

    voyager.config.globalProperties.$ui = {
        name: 'Voyager',
        colors: [
            'accent',
            'red',
            'orange',
            'yellow',
            'green',
            'teal',
            'blue',
            'indigo',
            'purple',
            'pink',
            'gray',
        ],
        lorem: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam quo totam eius aperiam dolorum.',
        tags: ['Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipisicing', 'elit'],
    };

    Layout(voyager);
    UI(voyager);
    Bread(voyager);
    Formfields(voyager);
    Transitions(voyager);
    voyager.config.globalProperties.$notify = Notify;
    voyager.config.globalProperties.$notification = Notification;

    voyager.component('settings-manager', SettingsManager);
    voyager.component('plugins-manager', PluginsManager);
    voyager.component('login', Login);

    window.voyager = voyager;
};

window.mountVoyager = function (el = '#voyager') {
    voyager.mount(el);
}