<template>
    <div :class="!isLogin ? 'h-screen flex overflow-hidden' : null ">
        <div>
            <fade-transition tag="div" :duration="250">
                <div class="absolute w-full h-1.5 overflow-hidden" style="z-index: 9999;" v-if="$store.pageLoading">
                    <div class="indeterminate">
                        <div class="before rounded" :class="`bg-blue-500`"></div>
                        <div class="after rounded" :class="`bg-blue-500`"></div>
                    </div>
                </div>
            </fade-transition>
        </div>
        <template v-if="!isLogin">
            <sidebar />
            <div class="flex flex-col w-0 flex-1 overflow-hidden">
                <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none" id="content">
                    <div id="top"></div>
                    <navbar></navbar>
                    <div id="tooltips" class="h-0 w-0"></div>
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
import Sidebar from './Sidebar';
import Navbar from './Navbar';
import Notifications from './UI/Notifications';
import $store from '../js/store';
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

        Inertia.on('finish', () => {
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

        axios.interceptors.response.use((response) => {
            return response;
        }, (error) => {
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