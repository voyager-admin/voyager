<template>
    <collapse-transition>
        <div class="rounded-md p-4 border" :class="`border-${color}-500`" v-if="show">
            <div class="flex items-center">
                <div class="flex-shrink-0" v-if="icon">
                    <icon :icon="icon" :size="5" :class="`text-${color}-500`" type="solid" />
                </div>
                <div :class="[icon ? 'ml-3' : '']">
                    <h3 class="text-sm leading-5 font-medium" :class="`text-${color}-500`" v-if="$slots.title">
                        <slot name="title"></slot>
                    </h3>
                    <div class="text-sm leading-5" :class="[`text-${color}-600`, {'mt-2': $slots.title}]">
                        <slot></slot>
                    </div>
                </div>
                <div class="ml-auto pl-3" v-if="closebutton">
                    <div class="-mx-1 -my-1">
                        <slot name="actions">
                            <button class="button border-none shadow-none" :class="`text-${color}-500`" @click="$emit('close'); show = false">
                                <icon icon="x" />
                            </button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </collapse-transition>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: 'green'
        },
        icon: {
            default: 'information-circle'
        },
        closebutton: {
            type: Boolean,
            default: true
        },
    },
    data: function () {
        return {
            show: true,
        };
    },
    methods: {
        open: function () {
            this.show = true;
        },
        close: function () {
            this.show = false;
        },
        toggle: function () {
            this.show = !this.show;
        }
    }
};
</script>