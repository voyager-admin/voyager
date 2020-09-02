// https://github.com/vuejs/vue
window.Vue = require('vue');

// https://github.com/component/debounce
window.debounce = require('debounce');
Vue.prototype.debounce = debounce;

// https://github.com/simov/slugify ~7kb
window.slugify = require('slugify');
Vue.prototype.slugify = window.slugify;

// https://github.com/BinarCode/vue2-transitions ~14kb
import Transitions from 'vue2-transitions';
Vue.use(Transitions);

// https://github.com/katlasik/mime-matcher
import MimeMatcher from 'mime-matcher';
Vue.prototype.MimeMatcher = MimeMatcher;