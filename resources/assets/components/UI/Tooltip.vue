<template>
    <fade-transition :duration="150" persisted>
        <div class="tooltip" ref="tooltip" v-show="display" :style="style" v-if="value !== null">
            <div class="arrow" :class="placement"></div>
            <div class="inner" v-html="value"></div>
        </div>
    </fade-transition>
    <span ref="slot" @mouseenter="display = true; setStyles();" @mouseleave="display = false">
        <slot></slot>
    </span>
</template>

<script>
import { nextTick } from 'vue';
export default {
    props: {
        placement: {
            type: String,
            default: 'bottom',
            validator: function (value) {
                return ['top', 'right', 'bottom', 'left'].includes(value);
            }
        },
        value: {
            default: null,
        }
    },
    data: function () {
        return {
            display: false,
            style: {}
        };
    },
    methods: {
        setStyles: function () {
            nextTick(() => {
                if (this.$refs.slot && this.$refs.tooltip) {
                    var slot = this.$refs.slot.getClientRects()[0];
                    var tooltip = this.$refs.tooltip.getClientRects()[0];
                    var content = document.getElementById('content');

                    var left = 0;
                    var top = 0;

                    if (this.placement == 'top') {
                        var center = slot.left + (slot.width / 2);
                        left = center - (tooltip.width / 2);
                        top += (slot.top - tooltip.height - 5);
                    } else if (this.placement == 'right') {
                        var center = slot.top + (slot.height / 2);
                        left =  slot.right;
                        top += center - (tooltip.height / 2);
                    } else if (this.placement == 'left') {
                        var center = slot.top + (slot.height / 2);
                        left = slot.left - tooltip.width;
                        top += center - (tooltip.height / 2);
                    } else {
                        var center = slot.left + (slot.width / 2);
                        left = center - ((tooltip.width - 5) / 2),
                        top += (slot.top + slot.height + 5);
                    }

                    this.style = {
                        top: (top + content.scrollTop) + 'px',
                        left: (left - content.getBoundingClientRect().left) + 'px',
                    };
                }
            });
        },
    },
}
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/text-color";

.tooltip {
    @apply absolute z-50 transition-opacity duration-500;

    .inner {
        @include bg-color(tooltip-bg-color, 'colors.gray.900');
        @include text-color(tooltip-text-color, 'colors.white');
        @apply py-1.5 px-2.5 rounded-md text-sm;
    }

    .arrow {
        @apply absolute;
        @include border-color(tooltip-arrow-color, 'colors.gray.900');
        &.top {
            border-width: 5px 5px 0 5px;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            border-bottom-color: transparent !important;
            bottom: -5px;
            left: calc(50% - 5px);
            margin-top: 0;
            margin-bottom: 0;
            width: 5px;
        }

        &.bottom {
            border-width: 0 5px 5px 5px;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            border-top-color: transparent !important;
            top: -5px;
            left: calc(50% - 5px);
            margin-top: 0;
            margin-bottom: 0;
        }

        &.left {
            border-width: 5px 0 5px 5px;
            border-top-color: transparent !important;
            border-right-color: transparent !important;
            border-bottom-color: transparent !important;
            right: -5px;
            top: calc(50% - 5px);
            margin-left: 0;
            margin-right: 0;
        }

        &.right {
            border-width: 5px 5px 5px 0;
            border-left-color: transparent !important;
            border-top-color: transparent !important;
            border-bottom-color: transparent !important;
            left: -5px;
            top: calc(50% - 5px);
            margin-left: 0;
            margin-right: 0;
        }
    }
}
</style>