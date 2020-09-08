<template>
    <div class="dropdown" v-click-outside="close" @keydown.esc="close">
        <div @click="open">
            <slot name="opener"></slot>
        </div>
        <slide-up-transition>
            <div class="wrapper" :class="[`w-${width}`, pos]" v-if="isOpen" @click="dontCloseOnInsideClick ? null : close()">
                <div class="body">
                    <slot></slot>
                </div>
            </div>
        </slide-up-transition>
    </div>
</template>
<script>
import closable from '../../js/mixins/closable';

export default {
    mixins: [closable],
    props: {
        pos: {
            type: String,
            default: 'left',
            validator: function (value) {
                return ['left', 'right'].indexOf(value) !== -1;
            }
        },
        width: {
            type: [Number, String, null],
            default: 72,
        },
        dontCloseOnInsideClick: {
            type: Boolean,
            default: false,
        }
    }
};
</script>

<style lang="scss">
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";

.dark .dropdown {
    .wrapper {
        @include bg-color(dropdown-bg-color-dark, 'colors.gray.900');
        @include border-color(dropdown-border-color-dark, 'colors.gray.700');

        .body {
            .link {
                @apply truncate;
                @include border-color(dropdown-border-color-dark, 'colors.gray.700');
                @include text-color(dropdown-link-color-dark, 'colors.blue.600');

                &:hover {
                    @include bg-color(dropdown-link-hover-color-dark, 'colors.gray.800');
                }
            }

            .divider {
                @include border-color(dropdown-divider-border-color-dark, 'colors.gray.700');
            }
        }
    }
}

.dropdown {
    @apply relative inline-block text-left;

    .wrapper {
        @include bg-color(dropdown-bg-color, 'colors.white');
        @include border-color(dropdown-border-color, 'colors.gray.400');
        @apply absolute rounded-md shadow-lg border z-50;

        &.right {
            @apply origin-top-left left-0;
        }

        &.left {
            @apply origin-top-right right-0;
        }

        .body {
            @apply overflow-auto;

            .link {
                @include border-color(dropdown-border-color, 'colors.gray.400');
                @include text-color(dropdown-link-color, 'colors.blue.600');
                @apply block px-6 py-3 leading-tight cursor-pointer;

                &:hover {
                    @include bg-color(dropdown-link-hover-color, 'colors.gray.100');
                }
            }

            .divider {
                @apply border-t;
                @include border-color(dropdown-divider-border-color, 'colors.gray.300');
            }
        }
    }
}
</style>