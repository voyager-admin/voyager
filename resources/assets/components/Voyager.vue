<template>
    <div :class="!isLogin ? 'h-screen flex overflow-hidden' : null ">
        <div>
            <div>
                <div class="absolute w-full h-1.5 overflow-hidden" style="z-index: 9999;" v-if="$store.pageLoading">
                    <div class="indeterminate">
                        <div class="before rounded" :class="`bg-blue-500`"></div>
                        <div class="after rounded" :class="`bg-blue-500`"></div>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="!isLogin">
            <sidebar />
            <div class="flex flex-col w-0 flex-1 overflow-hidden">
                <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none" id="content">
                    <div id="top"></div>
                    <navbar></navbar>
                    <div class="mx-auto sm:px-3 md:px-4">
                        <slot />
                    </div>
                </main>
            </div>
            
        </template>
        <template v-else>
            <slot />
        </template>
        <notifications :position="$store.notificationPosition" />
    </div>
</template>

<script>
import Sidebar from '@components/Sidebar.vue';
import Navbar from '@components/Navbar.vue';
import Notifications from '@components/UI/Notifications.vue';
import { watch } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

export default {
    components: {
        Sidebar,
        Navbar,
        Notifications
    },
    created() {
        watch(() => this.$store.title, (title) => {
            document.title = title + ' - ' + this.$store.titleSuffix;
        }, { immediate: true });

        Inertia.on('navigate', (event) => {
            this.$store.pageLoading = false;
            let url = String(window.location);
            if (!url.endsWith('/')) {
                url = url + '/';
            }
            this.$store.currentUrl = url;
        });

        Inertia.on('start', (event) => {
            this.$store.pageLoading = true;
        });

        Inertia.on('finish', (e) => {
            this.$store.pageLoading = false;
        });

        document.addEventListener('DOMContentLoaded', () => {
            this.$store.pageLoading = false;
        });

        // Toggle sidebar when cookie is set
        var sidebar_open = this.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            this.closeSidebar();
        }
        $eventbus.on('sidebar-open', (open) => {
            this.setCookie('sidebar-open', open);
        });

        // Show dev server warning if not available
        if (this.$store.devServer.wanted && !this.$store.devServer.available) {
            new this.$notification(this.__('voyager::generic.dev_server_unavailable', { url: 'http://localhost:8081' }))
                    .color('yellow')
                    .timeout(5000)
                    .confirm()
                    .addButton({ key: true, value: this.__('voyager::generic.disable'), color: 'accent'})
                    .show()
                    .then((result) => {
                        if (result === true) {
                            axios.post(this.route('voyager.disable-dev-server'))
                            .then(() => {
                                new this.$notification(this.__('voyager::generic.dev_server_disabled')).show();
                            })
                            .catch(response => {})
                            .then(() => {});
                        }
                    });
        }

        axios.interceptors.request.use((config) => {
            this.$store.pageLoading = true;
            return config;
        }, (error) => {
            return Promise.reject(error);
        });

        axios.interceptors.response.use((response) => {
            this.$store.pageLoading = false;
            return response;
        }, (error) => {
            this.$store.pageLoading = false;
            let response = error;
            if (response.response.status !== 422) {
                if (response.hasOwnProperty('response')) {
                    response = response.response;
                }
                if (response.hasOwnProperty('data')) {
                    response = response.data;
                }

                var notification = new this.$notification(response.message).color('red').timeout();
                if (response.hasOwnProperty('stack')) {
                    notification = notification.message(response.stack);
                    notification = notification.title(response.message);
                }
        
                notification.show();
            }

            throw error;
        });
    },
    computed: {
        isLogin() {
            return usePage().component.value == 'Login';
        }
    },
}
</script>