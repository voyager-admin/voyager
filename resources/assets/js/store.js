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
                    fade: false,
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
                },
                handleAjaxError: function (response) {
                    var notification = new this.$notification(response.data.message).color('red').timeout();
                    if (response.data.hasOwnProperty('exception')) {
                        notification = notification.title(response.data.exception);
                    }

                    return notification.show();
                },
                getCookie: function(name) {
                    var name = name + '=';
                    var decodedCookie = decodeURIComponent(document.cookie);
                    var ca = decodedCookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return '';
                },
                setCookie: function (name, value, exdays) {
                    var d = new Date();
                    d.setTime(d.getTime() + (exdays*24*60*60*1000));
                    var expires = "expires="+ d.toUTCString();
                    document.cookie = name + "=" + value + ";" + expires + ";path=/";
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
                // Toggle darkmode when cookie is set or dark-mode is the prefered color scheme
                var dark_mode = vm.getCookie('dark-mode');
                if (window.matchMedia && dark_mode == '') {
                    var dark = window.matchMedia('(prefers-color-scheme: dark)');
                    if (dark.matches) {
                        vm.toggleDarkMode(true);
                    }
                    // TODO: Once Safari follow the specs, this should be .dark.addEventListener('change', () => {})
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
                document.addEventListener('DOMContentLoaded', function() {
                    vm.pageLoading = false;
                });
            }
        });
    }
};