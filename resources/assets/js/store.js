import { reactive } from 'vue';

import { Notification } from './notify'

export default reactive({
    pageLoading: true,
    sidebarOpen: true,
    darkmode: false,
    rtl: false,
    formfields: [],
    breads: [],
    locale: null,
    locales: [],
    initial_locale: null,
    routes: [],
    json_output: true,
    csrf_token: null,
    current_url: '',
    searchPlaceholder: '',
    user: {
        name: '',
        avatar: '',
    },
    sidebar: {
        title: '',
        items: [],
    },
    page: {
        component: null,
        title: '',
        parameters: [],
    },
    toggleDirection: function () {
        this.rtl = !this.rtl;
        if (this.rtl) {
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            document.querySelector('html').setAttribute('dir', 'ltr');
        }
    },
    toggleDarkMode: function () {
        this.darkmode = !this.darkmode;
        if (this.darkmode) {
            document.querySelector('html').classList.add('dark');
        } else {
            document.querySelector('html').classList.remove('dark');
        }
        $eventbus.emit('darkmode', this.darkmode);
    },
    toggleSidebar: function () {
        this.sidebarOpen = !this.sidebarOpen;
        $eventbus.emit('sidebar-open', this.sidebarOpen);
    },
    openSidebar: function () {
        this.sidebarOpen = true;
        $eventbus.emit('sidebar-open', true);
    },
    closeSidebar: function () {
        this.sidebarOpen = false;
        $eventbus.emit('sidebar-open', false);
    },
    getFormfieldByType: function (type) {
        var formfield = this.formfields.where('type', type).first();
        if (!formfield) {
            console.error('Formfield with type "'+type+'" does not exist!');
        }

        return formfield || {};
    },
    getBreadByTable: function (table) {
        return this.breads.where('table', table).first();
    },
    nextLocale: function () {
        var index = this.locales.indexOf(this.locale);
        if (index >= this.locales.length - 1) {
            this.locale = this.locales[0];
        } else {
            this.locale = this.locales[index + 1];
        }
    },
    previousLocale: function () {
        var index = this.locales.indexOf(this.locale);
        if (index <= 0) {
            this.locale = this.locales[this.locales.length - 1];
        } else {
            this.locale = this.locales[index - 1];
        }
    },
    handleAjaxError: function (response) {
        var notification = new Notification(response.data.message).color('red').timeout();
        if (response.data.hasOwnProperty('exception')) {
            notification = notification.title(response.data.exception);
        }

        return notification.show();
    },
    handleError: function (error, vm, info) {
        return new Notification(error.message).color('red').timeout().show();
    },
    handleWarning: function (warning, vm, trace) {
        return new Notification(warning).color('yellow').timeout().show();
    }
});