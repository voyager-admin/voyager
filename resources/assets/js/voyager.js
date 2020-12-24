require('./helper/array');

import { createApp } from 'vue';
import slugify from 'slugify';
window.slugify = slugify;

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

// Global helper mixins
import MiscMixin from 'mixins/misc';
import StringMixin from 'mixins/strings';
import TypeMixin from 'mixins/types';
import UrlMixin from 'mixins/url';

// Components
import * as BreadComponents from './bread';
import * as FormfieldComponents from './formfields';
import * as LayoutComponents from './layout';
import * as PageComponents from './pages';
import * as TransitionComponents from './transitions';
import * as UIComponents from './ui';

let components = {
    ...BreadComponents,
    ...FormfieldComponents,
    ...LayoutComponents,
    ...PageComponents,
    ...TransitionComponents,
    ...UIComponents,
};

// Core modules
import { Notification } from './notify';
import Eventbus from './eventbus';
import Store from './store';

let voyager;

window.createVoyager = function (data = {}, main = true) {
    voyager = createApp((main ? Voyager : components.Login), data);

    voyager.config.globalProperties.Status = Object.freeze({
        Pending  : 1,
        Uploading: 2,
        Finished : 3,
        Failed   : 4,
    });
    window.Status = voyager.config.globalProperties.Status;

    voyager.use(Multilanguage, {
        localization: data.localization,
    });

    voyager.mixin(MiscMixin);
    voyager.mixin(StringMixin);
    voyager.mixin(TypeMixin);
    voyager.mixin(UrlMixin);

    voyager.config.globalProperties.slugify = slugify;
    voyager.config.globalProperties.$store = Store;
    voyager.config.globalProperties.$eventbus = Eventbus;
    voyager.config.globalProperties.$notification = Notification;
    voyager.config.globalProperties.createPopper = PopperGenerator({
        defaultModifiers: [...PopperDefaultModifiers, PopperFlip, PopperPreventOverflow, PopperArrow],
    });

    window.$eventbus = Eventbus;

    //voyager.config.errorHandler = Store.handleError;
    //voyager.config.warnHandler = Store.handleWarning;

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

window.mountVoyager = function (el = '#voyager') {
    voyager.mount(el);
}