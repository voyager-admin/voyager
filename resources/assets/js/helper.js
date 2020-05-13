import './helper/cookies';
import './helper/math';
import './helper/misc';
import './helper/strings';
import './helper/types';
import './helper/url';

Vue.directive('focus', {
    inserted: function (el) {
        el.focus();
    }
});

Vue.prototype.$eventHub = new Vue({});