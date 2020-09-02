require('./helper/array');
require('./helper/misc');
require('./helper/strings');
require('./helper/types');
require('./helper/url');

import ClickOutside from './directives/click-outside';
import Focus from './directives/focus';
import ScrollTo from './directives/scroll-to';
import Tooltip from './directives/tooltip';

Vue.directive('click-outside', ClickOutside);
Vue.directive('focus', Focus);
Vue.directive('scroll-to', ScrollTo);
Vue.directive('tooltip', Tooltip);