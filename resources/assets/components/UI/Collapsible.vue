<template>
    <card :title="title" :title-size="titleSize">
        <template #actions>
            <div class="inline-flex items-center">
                <slot name="actions"></slot>
                <icon :icon="isOpen ? 'chevron-up' : 'chevron-down'" :size="6" class="ltr:ml-6 rtl:mr-6 cursor-pointer" @click="toggle" />
            </div>
        </template>
        <collapse-transition>
            <div v-show="isOpen">
                <slot></slot>
            </div>
        </collapse-transition>
    </card>
</template>
<script>
import closable from '../../js/mixins/closable';

export default {
    mixins: [closable],
    props: {
        title: {
            type: String,
            default: '',
        },
        titleSize: {
            type: Number,
            default: 4,
        },
        closed: {
            type:Boolean,
            default: false,
        }
    },
    created: function () {
        this.$watch(
            () => this.closed,
            function (closed) {
                this.isOpen = !closed;
            },
            { immediate: true }
        );
    }
};
</script>