<template>
<card :title="title">
    <div slot="actions" class="inline-flex">
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
export default {
    props: {
        title: {
            type: String,
            default: '',
        },
        opened: {
            type:Boolean,
            default: true,
        }
    },
    data: function () {
        return {
            isOpen: this.opened,
        };
    },
    methods: {
        toggle: function () {
            this.isOpen = !this.isOpen;

            if (this.isOpen) {
                this.$emit('open');
            } else {
                this.$emit('close');
            }

            this.$emit('toggle');
        },
        close: function () {
            this.isOpen = false;
            this.$emit('close');
        },
        open: function () {
            this.isOpen = true;
            this.$emit('open');
        }
    }
};
</script>