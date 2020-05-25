require('./vendor');
require('./helper');
require('./notify');
require('./bread');
require('./multilanguage');
require('./formfields');
require('./layout');
require('./ui');

Vue.component('settings-manager', require('../components/Settings/Manager').default);
Vue.component('plugins-manager', require('../components/Plugins/Manager').default);
Vue.component('login', require('../components/Auth/Login').default);

import Store from './store';
Vue.use(Store);