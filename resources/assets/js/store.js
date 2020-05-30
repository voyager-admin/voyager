export default {
    install (Vue) {
        Vue.prototype.$store = new Vue({
            data: {
                routes: [],
                formfields: [],
                breads: [],
                csrf_token: document.head.querySelector('meta[name="csrf-token"]').content,
                debug: false,
                darkmode: false,
                rtl: false,
                sidebarOpen: true,
                pageLoading: true,
                ui: {
                    name: 'Voyager',
                    colors: [
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
                    ],
                    lorem: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam quo totam eius aperiam dolorum.',
                    tags: ['Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipisicing', 'elit'],
                    select_options: [
                        { key: 12, value: 'Lorem first', icon: 'arrow-circle-right' },
                        { key: 1, value: 'ipsum', icon: 'arrow-circle-right' },
                        { key: 2, value: 'dolor', icon: 'arrow-circle-right' },
                        { key: 3, value: 'sit', icon: 'arrow-circle-right' },
                        { key: 4, value: 'amet', icon: 'arrow-circle-right' },
                        { key: 5, value: 'consectetur', icon: 'arrow-circle-right' },
                        { key: 6, value: 'adipisicing', icon: 'arrow-circle-right' },
                        { key: 7, value: 'elit last', icon: 'arrow-circle-right' },
                    ],
                    selected_option: null,
                    selected_options: [],
                },
            },
            methods: {
                toggleDirection () {
                    this.rtl = !this.rtl;
                    if (this.rtl) {
                        document.querySelector('html').setAttribute('dir', 'rtl');
                    } else {
                        document.querySelector('html').setAttribute('dir', 'ltr');
                    }
                },
                toggleDarkMode () {
                    this.darkmode = !this.darkmode;
                    if (this.darkmode) {
                        document.querySelector('html').classList.add('mode-dark');
                    } else {
                        document.querySelector('html').classList.remove('mode-dark');
                    }
                },
                toggleSidebar () {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                openSidebar () {
                    this.sidebarOpen = true;
                },
                closeSidebar () {
                    this.sidebarOpen = false;
                },
                getFormfieldByType (type) {
                    return this.formfields.filter(function (formfield) {
                        return formfield.type == type;
                    })[0];
                },
                getBreadByTable (table) {
                    return this.breads.filter(function (bread) {
                        return bread.table == table;
                    })[0];
                },
            },
            watch: {
                sidebarOpen: function (open) {
                    this.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
                },
                darkmode: function (darkmode) {
                    this.setCookie('dark-mode', (darkmode ? 'true' : 'false'), 360);
                }
            },
            created: function () {
                var vm = this;
                // Toggle darkmode when cookie is set
                var dark_mode = vm.getCookie('dark-mode');
                if (dark_mode == 'true') {
                    vm.toggleDarkMode();
                }

                // Toggle sidebar when cookie is set
                var sidebar_open = vm.getCookie('sidebar-open');
                if (sidebar_open == 'false') {
                    vm.toggleSidebar();
                }

                // Hide loader when page is loaded
                document.addEventListener("DOMContentLoaded", function(event) {
                    vm.pageLoading = false;
                });
            }
        });
    }
};