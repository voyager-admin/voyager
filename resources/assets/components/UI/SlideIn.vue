<template>
    <slide-x-right-transition>
        <div v-if="isOpened" class="mode-dark slidein" :class="width" v-click-outside="close">
            <slot />
        </div>
    </slide-x-right-transition>
</template>
<script>
export default {
    props: {
        opened: {
            type: Boolean,
            default: false
        },
        width: {
            type: String,
            default: 'w-1/4',
        }
    },
    data: function () {
        return {
            isOpened: this.opened,
        };
    },
    methods: {
        open: function () {
            this.isOpened = true;
        },
        close: function () {
            this.isOpened = false;
        },
        toggle: function () {
            this.isOpened = !this.isOpened;
        }
    },
    watch: {
        opened: function (opened) {
            this.isOpened = opened;
        },
        isOpened: function (opened) {
            if (opened) {
                this.$emit('opened');
            } else {
                this.$emit('closed');
            }
        }
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', event => {
            if (event.keyCode === 27) {
                vm.close();
            }
        });
    },
};
</script>

<style lang="scss" scoped>
.slidein {
    @apply fixed top-0 left-auto right-0 h-full overflow-y-auto p-8 z-50 block;
    background-color: rgba(0, 0, 0, .7);
    z-index: 100;
}
</style>