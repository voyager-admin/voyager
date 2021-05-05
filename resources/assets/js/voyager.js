require('./helper/array');

require('../sass/voyager.scss');

// External libraries
import { createApp } from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue3';
import slugify from 'slugify';

window.slugify = slugify;
window.Vue = require('vue');

import Voyager from 'components/Voyager';

// Multilanguage
import Multilanguage from './multilanguage';

// Global (helper) functions
import Global from './global';

// Global helper mixins
import MiscMixin from 'mixins/misc';
import StringMixin from 'mixins/strings';
import TypeMixin from 'mixins/types';
import UrlMixin from 'mixins/url';

// Components
import * as FormfieldComponents from './formfields';
import * as TransitionComponents from './transitions';
import * as UIComponents from './ui';

// Global components
import LocalePicker from '../components/Layout/LocalePicker.vue';
import Icon from './icon'

let components = {
    ...FormfieldComponents,
    ...TransitionComponents,
    ...UIComponents,
    LocalePicker,
    Icon
};

// Core modules
import { Notification } from './notify';
import Eventbus from './eventbus';
import Store from './store';

let voyager;

function resolveInertiaComponent(name) {
    try {
        require(`../components/${name}`);

        return import(`../components/${name}`);
    } catch (e) {
        return import(`../components/Generic`);
    }
}

window.createVoyager = (data = {}, el = 'voyager') => {
    voyager = createApp(App, {
        initialPage: JSON.parse(document.getElementById(el).dataset.page),
        resolveComponent: (name) => resolveInertiaComponent(name)
            .then(({ default: page }) => {
                if (page.layout === undefined) {
                    page.layout = Voyager;
                }

                return page;
            }),
    }).use(plugin);

    voyager.addToUI = function (title, component) {
        Store.ui.push({ title, component });
    };

    for (let key of Object.keys(data)) {
        Store[key] = data[key];
    }

    voyager.config.globalProperties.Status = Object.freeze({
        Pending  : 1,
        Uploading: 2,
        Finished : 3,
        Failed   : 4,
    });
    window.Status = voyager.config.globalProperties.Status;
    
    voyager.use(Multilanguage);

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

    for (var key in components) {
        voyager.component(StringMixin.methods.kebabCase(key), components[key]);
    }

    window.voyager = voyager;
};

window.mountVoyager = (el = 'voyager') => {
    voyager.mount('#' + el);
};