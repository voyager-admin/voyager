<template>
    <div @keydown.esc="close" v-click-outside="close">
        <slide-left-transition>
            <div v-if="isOpen" class="dark slidein text-white w-full" :class="'lg:'+width">
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
        </slide-left-transition>
        <div ref="opener" @click="toggle">
            <slot name="opener"></slot>
        </div>
    </div>
</template>
<script>
import closable from '../../js/mixins/closable';
import clickOutside from '../../js/directives/click-outside';

export default {
    mixins: [closable],
    directives: {clickOutside: clickOutside},
    props: {
        opened: {
            type: Boolean,
            default: false
        },
        width: {
            type: String,
            default: 'w-1/3',
        },
        title: {
            type: String,
        }
    },
    data() {
        return {
            uuid: this.uuid()
        };
    },
    created() {
        this.$eventbus.on('close-slide-ins', (uuid) => {
            if (uuid !== this.uuid) {
                this.close();
            }
        });
        this.$watch(() => this.opened, (opened) => {
            this.isOpen = opened;
        }, { immediate: true });
        this.$watch(() => this.opened, (open) => {
            if (open) {
                this.$eventbus.emit('close-slide-ins', this.uuid);
            }
        }, { immediate: true });
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