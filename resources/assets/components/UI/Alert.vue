<template>
    <div class="rounded-md p-2 border" :class="`border-${color}-500`">
        <div class="flex items-center">
            <div class="flex-shrink-0" v-if="icon">
                <icon :icon="icon" :size="5" :class="`text-${color}-500`" type="solid" />
            </div>
            <div :class="[icon ? 'ml-3' : '']">
                <h3 class="text-base leading-5 font-medium" :class="`text-${color}-500`" v-if="$slots.title">
                    <slot name="title"></slot>
                </h3>
                <div class="text-sm leading-5" :class="[`text-${color}-500`, {'mt-1': $slots.title}]">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    emits: ['close'],
    props: {
        color: {
            type: String,
            default: 'green'
        },
        icon: {
            type: String,
            default: 'information-circle'
        },
    }
};
</script>