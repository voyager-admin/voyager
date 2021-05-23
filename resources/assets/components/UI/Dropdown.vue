<template>
    <div class="dropdown" v-click-outside="close" @keydown.esc="close">
        <div @click="tryOpen" ref="opener">
            <slot name="opener"></slot>
        </div>
        <fade-transition @after-leave="destroyPopper">
            <div class="wrapper" v-if="isOpen" @click="dontCloseOnInsideClick ? null : close()" ref="dropdown">
                <div class="body">
                    <slot></slot>
                </div>
            </div>
        </fade-transition>
    </div>
</template>
<script>
import { nextTick } from 'vue';
import closable from '../../js/mixins/closable';
import clickOutside from '../../js/directives/click-outside';

import { createPopper } from '@popperjs/core/lib/popper-lite';
import { placements } from '@popperjs/core/lib/enums';

export default {
    mixins: [closable],
    directives: {clickOutside: clickOutside},
    props: {
        placement: {
            type: String,
            default: 'bottom-start',
            validator: (value) => {
                return placements.includes(value);
            }
        },
        dontCloseOnInsideClick: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            popper: null,
        };
    },
    methods: {
        destroyPopper() {
            if (this.popper) {
                this.popper.destroy();
                this.popper = null;
            }
        },
        tryOpen() {
            let disabled = this.$refs.opener.children[0].getAttribute('disabled');
            if (disabled === null || disabled === false) {
                this.open();
            }
        }
    },
    watch: {
        isOpen(open) {
            if (open) {
                nextTick(() => {
                    this.popper = createPopper(
                        this.$refs.opener,
                        this.$refs.dropdown,
                        {
                            placement: this.placement
                        }
                    );
                });
            }
        }
    },
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

                &.active {
                    @include bg-color(dropdown-bg-color-dark, 'colors.gray.800');
                }

                &:hover {
                    @include bg-color(dropdown-link-hover-color-dark, 'colors.gray.700');
                }
            }

            .divider {
                @include border-color(dropdown-divider-border-color-dark, 'colors.gray.700');
            }
        }
    }
}

.dropdown {
    @apply inline-block text-left w-auto;

    .wrapper {
        @include bg-color(dropdown-bg-color, 'colors.white');
        @include border-color(dropdown-border-color, 'colors.gray.400');
        @apply rounded-md shadow-lg border z-50;

        .body {
            @apply overflow-auto;

            .link {
                @include border-color(dropdown-border-color, 'colors.gray.400');
                @include text-color(dropdown-link-color, 'colors.blue.600');
                @apply block px-6 py-3 leading-tight cursor-pointer;

                &.active {
                    @include bg-color(dropdown-bg-color-dark, 'colors.gray.200');
                }

                &:hover {
                    @include bg-color(dropdown-link-hover-color, 'colors.gray.250');
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