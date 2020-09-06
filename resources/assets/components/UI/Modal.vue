<template>
<div>
    <div was="fade-transition">
        <div v-if="isOpen" class="modal inset-0 p-0 flex items-center justify-center z-40">
            <div v-if="isOpen" class="fixed inset-0 transition-opacity" @click="close()">
                <div class="absolute inset-0 bg-black opacity-75"></div>
            </div>

            <div v-if="isOpen" class="body" :class="size === 'full' ? 'w-full max-h-full p-10' : 'lg:w-3/4 xl:w-2/4 max-h-3/4'">
                <card :title="title" :icon="icon" style="margin: 0 !important">
                    <template v-slot:actions class="inline-flex">
                        <slot name="actions"></slot>
                        <button  @click="close()">
                            <icon icon="x"></icon>
                        </button>
                    </template>
                    <slot></slot>
                </card>
            </div>
        </div>
    </div>
    <div ref="opener">
        <slot name="opener"></slot>
    </div>
</div>
</template>
<script>
import closable from '../../js/mixins/closable';

export default {
    mixins: [closable],
    props: {
        title: {
            type: String,
            default: ''
        },
        icon: {
            type: String,
            default: null
        },
        iconSize: {
            type: Number,
            default: 6
        },
        size: {
            type: String,
            default: 'normal',
        }
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', function (e) {
            if (e.code === 'Escape') {
                vm.close();
            }
        });
        if (vm.$refs.opener && vm.$refs.opener.children.length > 0) {
            Array.from(vm.$refs.opener.getElementsByTagName('*')).forEach(function (el) {
                el.addEventListener('click', event => {
                    vm.open();
                });
            });
        }
    },
};
</script>

<style lang="scss" scoped>
.modal {
    @apply fixed w-full top-0 left-0 h-full z-40 text-white text-left overflow-y-hidden;

    .body {
        @apply z-50 rounded-lg overflow-y-auto;
    }
}
</style>