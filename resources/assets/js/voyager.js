require('./helper/array');

require('../sass/voyager.scss');

import { createApp } from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue3';

import slugify from 'slugify';
window.slugify = slugify;

window.Vue = require('vue');

// Popper
import {
    popperGenerator as PopperGenerator,
    defaultModifiers as PopperDefaultModifiers,
} from '@popperjs/core/lib/popper-lite';
import PopperFlip from '@popperjs/core/lib/modifiers/flip';
import PopperPreventOverflow from '@popperjs/core/lib/modifiers/preventOverflow';
import PopperArrow from '@popperjs/core/lib/modifiers/arrow';

import Voyager from 'components/Voyager';

// Multi language
import Multilanguage from './multilanguage';

// Global (helper) functions
import Global from './global';

// Global helper mixins
import MiscMixin from 'mixins/misc';
import StringMixin from 'mixins/strings';
import TypeMixin from 'mixins/types';
import UrlMixin from 'mixins/url';

// Components
import * as BreadComponents from './bread';
import * as FormfieldComponents from './formfields';
import * as LayoutComponents from './layout';
import * as TransitionComponents from './transitions';
import * as UIComponents from './ui';

import icon from './icon'

let components = {
    ...BreadComponents,
    ...FormfieldComponents,
    ...LayoutComponents,
    ...TransitionComponents,
    ...UIComponents,
};

// Core modules
import { Notification } from './notify';
import Eventbus from './eventbus';
import Store from './store';

let voyager;

window.createVoyager = () => {
    voyager = createApp(App, {
        initialPage: JSON.parse(document.getElementById('app').dataset.page),
        resolveComponent: name => import(`../components/${name}`)
            .then(({ default: page }) => {
                if (page.layout === undefined) {
                    page.layout = Voyager;
                }
                return page;
            }),
    }).use(plugin);

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
    voyager.config.globalProperties.createPopper = PopperGenerator({
        defaultModifiers: [...PopperDefaultModifiers, PopperFlip, PopperPreventOverflow, PopperArrow],
    });

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

    voyager.component('icon', icon);

    window.voyager = voyager;
};

window.mountVoyager = (el = '#voyager') => {
    voyager.mount('#app');
}