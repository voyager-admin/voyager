<template>
    <card :title="title" :title-size="titleSize">
        <template #actions>
            <div class="inline-flex items-center space-x-6">
                <div>
                    <slot name="actions"></slot>
                </div>
                <icon icon="chevron-up" v-if="isOpen" :size="6" class="cursor-pointer" @click="toggle" />
                <icon icon="chevron-down" v-else :size="6" class="cursor-pointer" @click="toggle" />
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
    created() {
        this.$watch(() => this.closed, (closed) => {
            this.isOpen = !closed;
        },{ immediate: true });
    }
};
</script>