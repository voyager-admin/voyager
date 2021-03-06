import { reactive } from 'vue';

export default reactive({
    sidebarOpen: true,
    darkmode: 'system',
    systemDarkmode: false,

    breads: [],
    formfields: [],

    localization: [],
    locales: [],
    locale: '',
    initialLocale: '',

    ui: [],

    pageLoading: true,
    sidebarOpen: true,
    title: '',
    titleSuffix: '',
    rtl: false,
    csrfToken: '',
    currentUrl: '',

    version: '',

    notificationPosition: 'top-right',

    jsonOutput: false,
    devServer: {
        url: null,
        available: false,
        wanted: false,
    },

    sidebar: {
        items: [],
        title: '',
        iconSize: 6,
    },

    user: {
        name: '',
        avatar: '',
    }
});