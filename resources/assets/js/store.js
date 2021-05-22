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

    notificationPosition: 'top-right',

    jsonOutput: false,

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