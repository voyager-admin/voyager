<template>
    <span class="absolute" :class="show ? 'block' : 'hidden'" ref="tooltip">
        <span class="tooltip" :style="styles" >
            <div class="arrow" :class="pos"></div>
            <div class="inner" v-html="value"></div>
        </span>
    </span>
    <slot />
</template>

<script>
export default {
    props: {
        value: {
            required: true,
        },
        pos: {
            type: String,
            default: 'bottom'
        }
    },
    data: function () {
        return {
            show: false,
        };
    },
    computed: {
        styles: function () {
            var top = 0;
            var left = 0;
            var pos = this.pos;

            //var rect = this.$el.nextElementSibling.getBoundingClientRect();
            /*if (pos == 'top') {
                var center = rect.left + (rect.width / 2);
                top = rect.top - (tooltip.rect.height || 0) - 5;
                left = center - ((tooltip.rect.width || 0) / 2);
            } else if (pos == 'left') {
                var center = rect.top + (rect.height / 2);
                top = center - ((tooltip.rect.height || 0) / 2);
                left = rect.x - (tooltip.rect.width || 0) - 5;
            } else if (pos == 'right') {
                var center = rect.top + (rect.height / 2);
                top = center - ((tooltip.rect.height || 0) / 2);
                left = rect.right + 5;
            } else {
                var center = rect.left + (rect.width / 2);
                top = rect.bottom + 5;
                left = center - ((tooltip.rect.width || 0) / 2);
            }*/
            
            return {
                top: top + 'px',
                left: left + 'px',
            };
        }
    },
    mounted: function () {
        var vm = this;
        vm.$el.addEventListener('mouseenter', function () {
            vm.show = true;
        });
        vm.$el.addEventListener('mouseleave', function () {
            vm.show = false;
        });
    }
}
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/text-color";

.tooltip {
    @apply relative block z-50 transition-opacity duration-500;

    .inner {
        @include bg-color(tooltip-bg-color, 'colors.gray.900');
        @include text-color(tooltip-text-color, 'colors.white');
        @apply py-1 px-2 rounded-md text-sm;
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