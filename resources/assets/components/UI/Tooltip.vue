<template>
    <fade-transition :duration="250" @after-leave="destroyPopper" @enter="updatePopper">
        <div class="tooltip" ref="tooltip" v-if="display && value !== null">
            <div v-html="value"></div>
            <div class="arrow" data-popper-arrow></div>
        </div>
    </fade-transition>
    <div ref="slot" @mouseenter="display = true" @mouseleave="display = false" v-bind="$attrs">
        <slot></slot>
    </div>
</template>

<script>
import { nextTick } from 'vue';

export default {
    props: {
        placement: {
            type: String,
            default: 'bottom',
            validator: function (value) {
                return [
                    'auto', 'auto-start', 'auto-end', 'top', 'top-start',
                    'top-end', 'bottom', 'bottom-start', 'bottom-end',
                    'right', 'right-start', 'right-end', 'left', 'left-start', 'left-end'
                ].includes(value);
            }
        },
        value: {
            default: null,
        },
        absolute: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            display: false,
            popper: null,
        };
    },
    methods: {
        updatePopper: function () {
            if (this.popper) {
                this.popper.forceUpdate();
            }
        },
        destroyPopper: function () {
            if (this.popper) {
                this.popper.destroy();
                this.popper = null;
            }
        }
    },
    watch: {
        display: function (value) {
            if (value) {
                nextTick(() => {
                    this.popper = this.createPopper(
                        this.$refs.slot,
                        this.$refs.tooltip, {
                            placement: this.placement,
                            strategy: this.absolute ? 'absolute' : 'fixed',
                        }
                    );
                });
            }
        }
    },
}
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/text-color";

.tooltip {
    @include bg-color(tooltip-bg-color, 'colors.black');
    @include text-color(tooltip-text-color, 'colors.white');
    @apply rounded-lg z-50 px-2 py-1.5 shadow absolute;
    
    .arrow {
        @apply absolute h-0 w-0 border-solid;
        content: '';
        margin: 5px;
    }
    
    &[data-popper-placement^="bottom"] {
        margin-top: 5px !important;
        
        .arrow {
            @include border-color(tooltip-bg-color, 'colors.black');
            @apply mt-0 mb-0;
            border-width: 0 5px 5px 5px;
            border-top-color: transparent !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            top: -5px;
        }
    }
    
    &[data-popper-placement^="top"] {
        margin-bottom: 5px !important;
        
        .arrow {
            @include border-color(tooltip-bg-color, 'colors.black');
            @apply mt-0 mb-0;
            border-width: 5px 5px 0 5px;
            border-bottom-color: transparent !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            bottom: -5px;
        }
    }
    
    &[data-popper-placement^="right"] {
        margin-left: 5px !important;
        
        .arrow {
            @include border-color(tooltip-bg-color, 'colors.black');
            @apply ml-0 mr-0;
            border-width: 5px 5px 5px 0;
            border-top-color: transparent !important;
            border-left-color: transparent !important;
            border-bottom-color: transparent !important;
            left: -5px;
        }
    }
    
    &[data-popper-placement^="left"] {
        margin-right: 5px !important;
        
        .arrow {
            @include border-color(tooltip-bg-color, 'colors.black');
            @apply ml-0 mr-0;
            border-width: 5px 0 5px 5px;
            border-top-color: transparent !important;
            border-right-color: transparent !important;
            border-bottom-color: transparent !important;
            right: -5px;
        }
    }
}
</style>