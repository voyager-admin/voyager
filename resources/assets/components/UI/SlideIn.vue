<template>
    <slide-right-transition>
        <div v-if="isOpen" class="dark slidein text-white w-full" :class="'lg:'+width" v-click-outside="close">
            <div class="flex w-full mb-3">
                <div class="flex-grow">
                    <h4>{{ title }}</h4>
                </div>
                
                <div class="flex justify-end items-center">
                    <slot name="actions" />
                    <button class="ltr:ml-2 rtl:mr-2" @click="close">
                        <icon icon="x" />
                    </button>
                </div>
            </div>
            <slot />
        </div>
    </slide-right-transition>
</template>
<script>
import closable from '../../js/mixins/closable';

export default {
    mixins: [closable],
    props: {
        opened: {
            type: Boolean,
            default: false
        },
        width: {
            type: String,
            default: 'w-1/4',
        },
        title: {
            type: String,
        }
    },
    watch: {
        opened: function (opened) {
            this.isOpen = opened;
        },
    },
    mounted: function () {
        var vm = this;

        vm.isOpen = this.opened;
        document.body.addEventListener('keydown', function (e) {
            if (e.code === 'Escape') {
                vm.close();
            }
        });
    },
};
</script>

<style lang="scss" scoped>
.slidein {
    @apply fixed top-0 left-auto right-0 h-full overflow-y-auto p-8 z-50 block;
    background-color: rgba(0, 0, 0, .85);
    z-index: 100;
}
</style>