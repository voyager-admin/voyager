// https://github.com/vuejs/vue
window.Vue = require('vue');

// https://github.com/component/debounce
window.debounce = require('debounce');
Vue.prototype.debounce = debounce;

// https://github.com/simov/slugify
window.slugify = require('slugify');
Vue.prototype.slugify = window.slugify;

// https://github.com/BinarCode/vue2-transitions
import Transitions from 'vue2-transitions';
Vue.use(Transitions);

// https://github.com/Jexordexan/vue-slicksort
import { HandleDirective } from 'vue-slicksort';
Vue.directive('sort-handle', HandleDirective);

// https://github.com/katlasik/mime-matcher
import MimeMatcher from 'mime-matcher';
Vue.prototype.MimeMatcher = MimeMatcher;

// https://github.com/Akryum/v-tooltip
import { VTooltip } from 'v-tooltip';
VTooltip.options.defaultPlacement = 'bottom';
Vue.directive('tooltip', VTooltip);

// https://github.com/ndelvalle/v-click-outside
import vClickOutside from 'v-click-outside';
Vue.use(vClickOutside);