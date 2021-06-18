<template>
<div
    class="notifications sm:p-6 sm:items-start sm:justify-end"
    v-on:animationend="timeout($event)"
    v-on:animationcancel="timeout($event)"
    :class="position"
>
    <transition-group :duration="{enter: 500, leave: 0}" name="notifications" tag="div">
        <div
            v-for="notification in notifications"
            :key="notification._uuid"
            class="notification"
            :class="[`border-${notification._color}-500`]"
            uses="border-red-500 border-orange-500 border-yellow-500 border-green-500 border-teal-500 border-blue-500 border-indigo-500 border-purple-500 border-pink-500 border-gray-500"
            @mouseover="stopTimeout(notification)"
            @mouseleave="startTimeout(notification)"
        >
            <div class="p-4">
                <div class="flex space-x-3 items-start">
                    <div class="w-6" v-if="notification._icon">
                        <icon
                            :icon="notification._icon"
                            :class="`text-${notification._color}-500`"
                            uses="text-red-500 text-orange-500 text-yellow-500 text-green-500 text-teal-500 text-blue-500 text-indigo-500 text-purple-500 text-pink-500 text-gray-500"
                            :size="6"
                        />
                    </div>
                    <div class="w-0 flex-1" >
                        <span v-if="notification._title">
                            <p class="title">{{ notification._title }}</p>
                            <p class="message mt-1">{{ notification._message }}</p>
                        </span>
                        <p class="title" v-else v-html="notification._message"></p>
                        <div class="mt-4 flex" v-if="notification._prompt">
                            <input
                                type="text"
                                class="input small w-full"
                                v-model="notification._prompt_value"
                                v-on:keyup="stopTimeout(notification)"
                                v-on:keypress.enter="close(notification, true, notification._prompt_value)"
                                v-focus
                            />
                        </div>
                        <div class="mt-4 flex space-x-1" v-if="notification._buttons && notification._buttons.length >= 1">
                            <span class="inline-flex" v-for="(button, key) in notification._buttons" :key="'button-'+key">
                                <button type="button" class="button" :class="button.color" @click="clickButton(notification, button)">
                                    <span>{{ button.value.startsWith('voyager::') ? __(button.value) : button.value }}</span>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="close(notification)" class="inline-flex text-gray-400 focus:outline-none">
                            <icon icon="x" />
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full h-1.5 overflow-hidden" v-if="notification._indeterminate === true">
                <div class="indeterminate" uses="bg-red-500 bg-orange-500 bg-yellow-500 bg-green-500 bg-teal-500 bg-blue-500 bg-indigo-500 bg-purple-500 bg-pink-500 bg-gray-500">
                    <div class="before rounded" :class="`bg-${notification._color}-500`"></div>
                    <div class="after rounded" :class="`bg-${notification._color}-500`"></div>
                </div>
            </div>
            <div
                v-else-if="Number.isInteger(notification._timeout)"
                class="w-full h-1.5 overflow-hidden"
            >
                <div
                    class="h-full origin-left animate-scale-x rounded"
                    :class="`bg-${notification._color}-500`"
                    :style="getProgressStyle(notification)"
                    :data-uuid="notification._uuid"
                ></div>
            </div>
        </div>
    </transition-group>
</div>
</template>
<script>
import { Notify as notify } from '@/notify';
import focus from '@directives/focus';

export default {
    directives: {focus: focus},
    props: {
        position: {
            type: String,
            default: 'top-right',
            validator: (value) => ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'].includes(value)
        }
    },
    data() {
        return {
            notifications: notify.notifications
        }
    },
    methods: {
        close(notification, key = false, message = null) {
            notify.removeNotification(notification, key, message);
        },
        clickButton(notification, button) {
            if (notification._prompt) {
                this.close(notification, button.key, notification._prompt_value);
            } else {
                this.close(notification, button.key);
            }
            
        },
        getProgressStyle(notification) {
            return {
                animationDuration: notification._timeout + 'ms',
                animationPlayState: notification._timeout_running ? 'running' : 'paused',
            };
        },
        stopTimeout(notification) {
            if (notification._timeout !== null) {
                notification._timeout_running = false;
            }
        },
        startTimeout(notification) {
            if (notification._timeout !== null) {
                notification._timeout_running = true;
            }
        },
        timeout(e) {
            if (e.animationName.startsWith('scale-x')) {
                var uuid = e.target.dataset.uuid;
                var notification = notify.notifications.where('_uuid', uuid).first();
                if (notification._timeout !== null) {
                    this.close(notification);
                }
            }
        },
    },
};
</script>

<style lang="scss">
@import "@sassmixins/bg-color";
@import "@sassmixins/border-color";
@import "@sassmixins/text-color";

.dark .notifications {
    .notification {
        @include bg-color(notification-bg-color-dark, 'colors.gray.800');

        p.title {
            @include text-color(notification-title-color-dark, 'colors.gray.100');
        }

        p.message {
            @include text-color(notification-message-color-dark, 'colors.gray.400');
        }
    }
}

.notifications {
    @apply fixed inset-0 flex px-4 py-6 pointer-events-none;

    &.top-left {
        @apply justify-start;
    }

    &.top-center {
        @apply justify-center;
    }

    &.bottom-left {
        @apply items-end justify-start;
    }

    &.bottom-center {
        @apply items-end justify-center;
    }

    &.bottom-right {
        @apply items-end;
    }

    > div {
        @apply max-w-sm w-full rounded-lg pointer-events-auto;

        @media(min-width: theme('screens.md')) {
            @apply max-w-sm;
        }
    }

    .notification {
        @apply transition-all duration-500 ease-in-out;
        @include bg-color(notification-bg-color, 'colors.white');
        @apply rounded-lg mb-2 border overflow-hidden shadow-lg w-96;

        p.title {
            @include text-color(notification-title-color, 'colors.gray.900');
            @apply text-sm leading-5 font-medium break-words;
        }

        p.message {
            @include text-color(notification-message-color, 'colors.gray.500');
            @apply text-sm leading-5 break-words;
        }
    }
}

.notifications-enter-from {
    @apply opacity-0;
    transform: translateY(30px);
}
.notifications-leave-to {
    @apply opacity-0;
    transform: translateY(-30px);
}
.notifications-leave-active {
    @apply absolute;
}
</style>

<style lang="scss">

</style>