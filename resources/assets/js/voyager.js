import '@helper/array';

import '../sass/voyager.scss';

// External libraries
import * as Vue from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import slugify from 'slugify';

window.slugify = slugify;
window.Vue = Vue;

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

// Components
import * as FormfieldComponents from '@/formfields';
import * as TransitionComponents from '@/transitions';
import * as UIComponents from '@/ui';

// Global components
import LocalePicker from '@components/Layout/LocalePicker.vue';
import Icon from '@/icon'

let components = {
    ...FormfieldComponents,
    ...TransitionComponents,
    ...UIComponents,
    LocalePicker,
    Icon
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

window.createVoyager = (data = {}, el = 'voyager') => {
    createInertiaApp({
        resolve: name => {
            let component = require(`@components/Generic.vue`).default;
            try {
                component = require(`@components/${name}.vue`).default;
            } catch (e) {}

            component.layout = component.layout || Voyager;

            return component;
        },
        setup({ el, app, props, plugin }) {
            voyager = Vue.createApp({
                render: () => Vue.h(app, props)
            }).use(plugin);

            prepareVoyager(data);
            voyager.mount(el);
        },
        id: el
    });
};