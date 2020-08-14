export default {
    install (Vue) {
        Vue.prototype.$store = new Vue({
            data: {
                routes: [],
                formfields: [],
                breads: [],
                csrf_token: document.head.querySelector('meta[name="csrf-token"]').content,
                debug: false,
                json_output: true,
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
                        { key: 12, value: 'Lorem first' },
                        { key: 1, value: 'ipsum' },
                        { key: 2, value: 'dolor' },
                        { key: 3, value: 'sit' },
                        { key: 4, value: 'amet' },
                        { key: 5, value: 'consectetur' },
                        { key: 6, value: 'adipisicing' },
                        { key: 7, value: 'elit last' },
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
                        document.querySelector('html').classList.add('dark');
                    } else {
                        document.querySelector('html').classList.remove('dark');
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
                    return this.formfields.where('type', type).first();
                },
                getBreadByTable (table) {
                    return this.breads.where('table', table).first();
                }
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
                // Toggle darkmode when cookie is set or dark-mode is the prefered color scheme
                var dark_mode = vm.getCookie('dark-mode');
                if (window.matchMedia && dark_mode == '') {
                    var dark = window.matchMedia('(prefers-color-scheme: dark)');
                    if (dark.matches) {
                        vm.toggleDarkMode(true);
                    }
                    dark.addListener(function (e) {
                        vm.toggleDarkMode(e.matches);
                    });
                }
                if (dark_mode == 'true') {
                    vm.toggleDarkMode(true);
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