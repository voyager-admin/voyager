<template>
    <span
        class="badge"
        :class="[`button ${color}`, large ? 'large' : null]"
        v-on="$listeners">
        <slot></slot>
        <icon
            v-on:click.native="$emit('click-icon', $event)"
            class="ltr:ml-1 ltr:mr-0 rtl:mr-1 rtl:ml-0 cursor-pointer"
            :size="large ? 5 : 4"
            v-if="icon !== ''"
            :icon="icon">
        </icon>
    </span>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: 'accent',
        },
        icon: {
            type: String,
            default: '',
        },
        large: {
            type: Boolean,
            default: false,
        }
    },
};
</script>

<style lang="scss" scoped>
.badge {
    @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium leading-4 m-1 transition ease-in-out duration-150 select-none;

    &.large {
        @apply px-3 text-sm leading-5;
    }
}
</style>