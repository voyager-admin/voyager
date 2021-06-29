__webpack_public_path__ = document.querySelector('meta[name="asset-url"]').content;

import '@helper/array';

import '../sass/voyager.scss';

// External libraries
import * as Vue from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import slugify from 'slugify';
import axios from 'axios';

import scrollTo from '@directives/scroll-to';

window.slugify = slugify;
window.Vue = Vue;
window.axios = axios;
window.scrollTo = scrollTo;

import Voyager from '@components/Voyager.vue';

// Multilanguage
import Multilanguage from '@/multilanguage';

// Global (helper) functions
import Global from '@/global';

// Global helper mixins
import MiscMixin from '@mixins/misc';
import StringMixin from '@mixins/strings';
import TypeMixin from '@mixins/types';
import UrlMixin from '@mixins/url';
import FormfieldMixin from '@mixins/formfield';
import FormfieldBuilderMixin from '@mixins/formfield-builder';

// Directives
import TooltipDirective from '@directives/tooltip';

// Global components
import LocalePicker from '@components/Layout/LocalePicker.vue';
import FormfieldComponents from '@/formfields';

let components = {
    LocalePicker,
    ...FormfieldComponents
};

// Core modules
import { Notification } from '@/notify';
import Eventbus from '@/eventbus';
import Store from '@/store';

let voyager;

function prepareVoyager(data) {
    for (let key of Object.keys(data)) {
        Store[key] = data[key];
    }

    voyager.addToUI = function (title, component) {
        Store.ui.push({ title, component });
    };

    voyager.componentExists = function (component) {
        return Object.keys(this._context.components).includes(component);
    };

    voyager.formfieldMixin = FormfieldMixin;
    voyager.formfieldBuilderMixin = FormfieldBuilderMixin;

    voyager.config.globalProperties.Status = Object.freeze({
        Pending  : 1,
        Uploading: 2,
        Finished : 3,
        Failed   : 4,
    });
    window.Status = voyager.config.globalProperties.Status;
    
    voyager.use(Multilanguage);

    voyager.directive('tooltip', TooltipDirective);

    voyager.mixin(MiscMixin);
    voyager.mixin(StringMixin);
    voyager.mixin(TypeMixin);
    voyager.mixin(UrlMixin);

    voyager.config.globalProperties.slugify = slugify;
    voyager.config.globalProperties.$store = Store;
    voyager.use(Global);
    voyager.config.globalProperties.$eventbus = Eventbus;
    voyager.config.globalProperties.$notification = Notification;

    window.$eventbus = Eventbus;

    voyager.config.globalProperties.colors = [
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
    ];

    if (data.hasOwnProperty('messages')) {
        data.messages.forEach((message) => {
            new Notification(message.message).color(message.color || 'yellow').timeout(message.timeout).show();
        });
    }

    // Register UI components
    let ui = require.context('@components/UI', true, /\.vue$/i)
    ui.keys().forEach((path) => {
        let name = path.replace('./', '').replace('.vue', '');
        voyager.component(StringMixin.methods.kebabCase(name), ui(path).default);
    });
    
    // Register transition components
    let transitions = require.context('@components/Transitions', true, /\.vue$/i)
    transitions.keys().forEach((path) => {
        let name = path.replace('./', '').replace('.vue', '');
        voyager.component(StringMixin.methods.kebabCase(name)+'-transition', transitions(path).default);
    });

    for (var key in components) {
        voyager.component(StringMixin.methods.kebabCase(key), components[key]);
    }

    window.voyager = voyager;
    voyager.config.globalProperties.$voyager = voyager;

    if (module.hot) {
        const first = window.location.pathname;
        module.hot.accept();
        module.hot.dispose(() => {
            if (first !== window.location.pathname) {
                window.location.reload();
            }
        });
    }
}

let mountTo;

window.createVoyager = (data = {}, el = 'voyager') => {
    createInertiaApp({
        resolve: name => {
            // This is necessary so webpack doesn't load ALL components (by using require(`@components/${name}`))
            let component = require(`@components/Generic.vue`).default;

            if (name == 'Dashboard') {
                component = require(`@components/Dashboard.vue`).default;
            } else if (name == 'Error') {
                component = require(`@components/Error.vue`).default;
            } else if (name == 'Login') {
                component = require(`@components/Login.vue`).default;
            } else if (name == 'Media') {
                component = require(`@components/Media.vue`).default;
            } else if (name == 'Plugins') {
                component = require(`@components/Plugins.vue`).default;
            } else if (name == 'Settings') {
                component = require(`@components/Settings.vue`).default;
            } else if (name == 'UI') {
                component = require(`@components/UI.vue`).default;
            } else if (name == 'Bread/Browse') {
                component = require(`@components/Bread/Browse.vue`).default;
            } else if (name == 'Bread/EditAdd') {
                component = require(`@components/Bread/EditAdd.vue`).default;
            } else if (name == 'Bread/Read') {
                component = require(`@components/Bread/Read.vue`).default;
            } else if (name == 'Builder/Browse') {
                component = require(`@components/Builder/Browse.vue`).default;
            } else if (name == 'Builder/EditAdd') {
                component = require(`@components/Builder/EditAdd.vue`).default;
            }

            component.layout = component.layout || Voyager;

            return component;
        },
        setup({ el, app, props, plugin }) {
            voyager = Vue.createApp({
                render: () => Vue.h(app, props)
            }).use(plugin);

            prepareVoyager(data);
            mountTo = el;
        },
        id: el
    });
};

window.mountVoyager = () => {
    voyager.mount(mountTo);
};