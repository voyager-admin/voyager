require('./helper/array');

import { createApp } from 'vue';
import slugify from 'slugify';
window.slugify = slugify;

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
import * as LayoutComponents from './layout';
import * as PageComponents from './pages';
import * as TransitionComponents from './transitions';
import * as UIComponents from './ui';

import FormfieldComponents from './formfields';

let components = {
    ...BreadComponents,
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
    voyager = createApp((main ? Voyager : Login), data);

    voyager.config.globalProperties.slugify = slugify;

    voyager.config.globalProperties.Status = Object.freeze({
        Pending  : 1,
        Uploading: 2,
        Finished : 3,
        Failed   : 4,
    });

    voyager.use(Multilanguage, {
        localization: data.localization,
    });

    voyager.mixin(MiscMixin);
    voyager.mixin(StringMixin);
    voyager.mixin(TypeMixin);
    voyager.mixin(UrlMixin);

    FormfieldComponents(voyager);

    voyager.config.globalProperties.$store = Store;
    voyager.config.globalProperties.$eventbus = Eventbus;
    window.$eventbus = Eventbus;

    voyager.config.errorHandler = Store.handleError;
    voyager.config.warnHandler = Store.handleWarning;

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
    voyager.config.globalProperties.$notification = Notification;

    window.voyager = voyager;
};

window.mountVoyager = function (el = '#voyager') {
    voyager.mount(el);
}