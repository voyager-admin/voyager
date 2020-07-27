<template>
<div class="notifications sm:p-6 sm:items-start sm:justify-end" v-on:animationend="timeout($event)" v-on:animationcancel="timeout($event)">
    <div>
        <slide-x-right-transition group :duration="{enter: 500, leave: 0}">
            <div
                v-for="notification in $notify.notifications"
                :key="notification._uuid"
                class="notification"
                :class="[`border-${notification._color}-500`]"
                @mouseover="stopTimeout(notification)"
                @mouseleave="startTimeout(notification)"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="w-6" v-if="notification._icon">
                            <icon :icon="notification._icon" :class="`text-${notification._color}-500`" :size="6" type="solid"></icon>
                        </div>
                        <div class="w-0 flex-1" :class="notification._icon ? 'ml-3' : ''">
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
                            <div class="mt-4 flex" v-if="notification._buttons && notification._buttons.length >= 1">
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
                    <div class="indeterminate">
                        <div class="before rounded" :class="`bg-${notification._color}-500`"></div>
                        <div class="after rounded" :class="`bg-${notification._color}-500`"></div>
                    </div>
                </div>
                <div
                    v-else-if="Number.isInteger(notification._timeout)"
                    class="w-full h-1.5 overflow-hidden"
                >
                    <div
                        class="h-full origin-left scale-x rounded"
                        :class="`bg-${notification._color}-500`"
                        :style="getProgressStyle(notification)"
                        :data-uuid="notification._uuid"
                    ></div>
                </div>
            </div>
        </slide-x-right-transition>
    </div>
</div>
</template>
<script>
export default {
    methods: {
        close: function (notification, key = false, message = null) {
            this.$notify.removeNotification(notification, key, message);
        },
        clickButton: function (notification, button) {
            if (notification._prompt) {
                this.close(notification, button.key, notification._prompt_value);
            } else {
                this.close(notification, button.key);
            }
            
        },
        getProgressStyle: function (notification) {
            return {
                animationDuration: notification._timeout + 'ms',
                animationPlayState: notification._timeout_running ? 'running' : 'paused',
            };
        },
        stopTimeout: function (notification) {
            if (notification._timeout !== null) {
                notification._timeout_running = false;
            }
        },
        startTimeout: function (notification) {
            if (notification._timeout !== null) {
                notification._timeout_running = true;
            }
        },
        timeout: function (e) {
            if (e.animationName.startsWith('scale-x')) {
                var uuid = e.target.dataset.uuid;
                var notification = this.$notify.notifications.where('_uuid', uuid).first();
                if (notification._timeout !== null) {
                    this.close(notification);
                }
            }
        },
    },
};
</script>

<style lang="scss" scoped>

</style>