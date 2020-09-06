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
    },
    toggleSidebar: function () {
        this.sidebarOpen = !this.sidebarOpen;
    },
    openSidebar: function () {
        this.sidebarOpen = true;
    },
    closeSidebar: function () {
        this.sidebarOpen = false;
    },
    getFormfieldByType: function (type) {
        return this.formfields.where('type', type).first();
    },
    getBreadByTable: function (table) {
        return this.breads.where('table', table).first();
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