<template>
    <card :title="title" :title-size="titleSize">
        <div slot="actions" class="inline-flex items-center">
            <slot name="actions"></slot>
            <icon :icon="isOpen ? 'chevron-up' : 'chevron-down'" :size="6" class="ltr:ml-6 rtl:mr-6 cursor-pointer" @click.native="toggle"></icon>
        </div>
        <collapse-transition>
            <div v-show="isOpen">
                <slot></slot>
            </div>
        </collapse-transition>
        <div slot="footer" class="footer" v-if="$slots.footer">
            <slot name="footer"></slot>
        </div>
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
    mounted: function () {
        this.isOpen = !this.closed;
    },
    watch: {
        closed: function (closed) {
            this.isOpen = !closed;
        }
    }
};
</script>