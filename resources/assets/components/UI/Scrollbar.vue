<template>
    <div @click="calculateSize" class="scrollbar" ref="scrollWrapper">
        <div
            :class="!dragging ? 'transition' : ''"
            ref="scrollArea"
            @wheel="scroll"
            @touchstart="startDrag"
            @touchmove="onDrag"
            @touchend="stopDrag"
            :style="{ marginTop: top * -1 +'px', marginLeft: left * -1 +'px' }">
            <slot />
            <vertical-scrollbar
                v-if="ready"
                :area="{ height: scrollAreaHeight }"
                :wrapper="{ height: scrollWrapperHeight }"
                :scrolling="vMovement"
                :dragging-from-parent="dragging"
                :on-change-position="handleChangePosition"
                :on-dragging="handleScrollbarDragging"
                :on-stop-drag="handleScrollbarStopDrag"
            ></vertical-scrollbar>
            <horizontal-scrollbar
                v-if="ready"
                :area="{ width: scrollAreaWidth }"
                :wrapper="{ width: scrollWrapperWidth }"
                :scrolling="hMovement"
                :dragging-from-parent="dragging"
                :on-change-position="handleChangePosition"
                :on-dragging="handleScrollbarDragging"
                :on-stop-drag="handleScrollbarStopDrag"
            ></horizontal-scrollbar>
        </div>
    </div>
</template>
<script>
import VerticalScrollbar from "./Scrollbar/Vertical.vue";
import HorizontalScrollbar from "./Scrollbar/Horizontal.vue";

export default {
    props: {
        speed: {
            type: Number,
            default: 50,
        },
        dontScroll: {
            type: Boolean,
            default: true,
        }
    },
    components: {
        VerticalScrollbar,
        HorizontalScrollbar
    },
    data: function () {
        return {
            ready: false,
            top: 0,
            left: 0,
            scrollAreaHeight: null,
            scrollAreaWidth: null,
            scrollWrapperHeight: null,
            scrollWrapperWidth: null,
            vMovement: 0,
            hMovement: 0,
            dragging: false,
            start: { y: 0, x: 0 },
            allowBodyScroll: false,
        };
    },
    methods: {
        scroll: function (e) {
            this.calculateSize(() => {
                let num = this.speed;
                let shifted = e.shiftKey;
                let scrollY = e.deltaY > 0 ? num : -num;
                let scrollX = e.deltaX > 0 ? num : -num;
                if (shifted && e.deltaX == 0) {
                    scrollX = e.deltaY > 0 ? num : -num;
                }
                let nextY = this.top + scrollY;
                let nextX = this.left + scrollX;
                let canScrollY = this.scrollAreaHeight > this.scrollWrapperHeight;
                let canScrollX = this.scrollAreaWidth > this.scrollWrapperWidth;

                if (canScrollY && !shifted) {
                    this.normalizeVertical(nextY);
                }

                if (shifted && canScrollX) {
                    this.normalizeHorizontal(nextX);
                }
            });
            if (!this.allowBodyScroll || this.dontScroll) {
                e.preventDefault();
                e.stopPropagation();
            }
        },
        startDrag: function (e) {
            this.touchEvent = e;
            const evt = e.changedTouches ? e.changedTouches[0] : e;
            this.calculateSize(() => {
                this.dragging = true;
                this.start = {
                    y: evt.pageY,
                    x: evt.pageX
                };
            });
        },
        onDrag: function (e) {
            if (this.dragging) {
                e.preventDefault();
                e.stopPropagation();
                if (this.touchEvent) {
                    this.touchEvent.preventDefault();
                    this.touchEvent.stopPropagation();
                }
                let evt = e.changedTouches ? e.changedTouches[0] : e;
                let yMovement = this.start.y - evt.clientY;
                let xMovement = this.start.x - evt.clientX;
                this.start = {
                    y: evt.clientY,
                    x: evt.clientX
                };
                let nextY = this.top + yMovement;
                let nextX = this.left + xMovement;
                this.normalizeVertical(nextY);
                this.normalizeHorizontal(nextX);
            }
        },
        stopDrag: function () {
            this.dragging = false;
            this.touchEvent = false;
        },
        scrollToY: function (y) {
            this.normalizeVertical(y);
        },
        scrollToX: function (x) {
            this.normalizeHorizontal(x);
        },
        normalizeVertical: function (next) {
            const elementSize = this.getSize();
            const lowerEnd = elementSize.scrollAreaHeight - elementSize.scrollWrapperHeight;
            // Max Scroll Down
            const maxBottom = next > lowerEnd;
            if (maxBottom) {
                next = lowerEnd;
            }
            // Max Scroll Up
            const maxTop = next < 0;
            if (maxTop) {
                next = 0;
            }
            // Update the Vertical Value if it's needed
            const shouldScroll = this.top !== next;
            this.allowBodyScroll = !shouldScroll;
            if (shouldScroll) {
                this.top = next;
                this.vMovement = (next / elementSize.scrollAreaHeight) * 100;
            }
        },
        normalizeHorizontal: function (next) {
            const elementSize = this.getSize();
            const rightEnd = elementSize.scrollAreaWidth - this.scrollWrapperWidth;
            const maxRight = next > rightEnd;
            if (maxRight) {
                next = rightEnd;
            }
            const maxLeft = next < 0;
            if (next < 0) {
                next = 0;
            }
            const shouldScroll = this.left !== next;
            this.allowBodyScroll = !shouldScroll;
            if (shouldScroll) {
                this.left = next;
                this.hMovement = (next / elementSize.scrollAreaWidth) * 100;
            }
        },
        handleChangePosition: function (movement, orientation) {
            this.calculateSize(() => {
                let next = movement / 100;
                if (orientation == 'vertical') {
                    this.normalizeVertical(next * this.scrollAreaHeight);
                }
                if (orientation == 'horizontal') {
                    this.normalizeHorizontal(next * this.scrollAreaWidth);
                }
            });
        },
        handleScrollbarDragging: function() {
            this.dragging = true;
        },
        handleScrollbarStopDrag: function () {
            this.dragging = false;
        },
        getSize: function () {
            let $scrollArea = this.$refs.scrollArea;
            let elementSize = {
                scrollAreaHeight: this.$el.children[0].scrollHeight,
                scrollAreaWidth: this.$el.children[0].scrollWidth,
                scrollWrapperHeight: this.$el.clientHeight,
                scrollWrapperWidth: this.$el.clientWidth
            };

            return elementSize;
        },
        calculateSize: function (cb) {
            if (typeof cb !== "function") {
                cb = null;
            }
            let elementSize = this.getSize();
            if (
                elementSize.scrollWrapperHeight !== this.scrollWrapperHeight ||
                elementSize.scrollWrapperWidth !== this.scrollWrapperWidth ||
                elementSize.scrollAreaHeight !== this.scrollAreaHeight ||
                elementSize.scrollAreaWidth !== this.scrollAreaWidth
            ) {
                // Scroll Area Height and Width
                this.scrollAreaHeight = elementSize.scrollAreaHeight;
                this.scrollAreaWidth = elementSize.scrollAreaWidth;
                // Scroll Wrapper Height and Width
                this.scrollWrapperHeight = elementSize.scrollWrapperHeight;
                this.scrollWrapperWidth = elementSize.scrollWrapperWidth;
                this.ready = true;

                return cb ? cb() : false;
            }

            return cb ? cb() : false;
        }
    },
    mounted: function () {
        this.calculateSize();
        window.addEventListener('resize', this.calculateSize);
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.calculateSize);
    }
};
</script>

<style lang="scss">
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";

.mode-dark .scrollbar {
    .track {
        @apply border-gray-500;
        &.vertical, &.horizontal {
            @include bg-color(scrollbar-bg-color-dark, 'colors.gray.600');

            .handle {
                @include bg-color(scrollbar-handle-bg-color-dark, 'colors.gray.750');
                @include border-color(scrollbar-handle-border-color-dark, 'colors.gray.500');
            }
        }
    }
}

.scrollbar {
    @apply overflow-hidden relative;
    margin: 0 auto;

    &:hover {
        .track {
            &.vertical, &.horizontal {
                @apply opacity-100 transition-opacity duration-500 ease-in-out;
            }
        }
    }

    .track {
        @apply border;
        @include border-color(scrollbar-border-color, 'colors.gray.300');
        &.vertical, &.horizontal {
            @include bg-color(scrollbar-bg-color, 'colors.gray.200');
            @apply opacity-25 absolute rounded-md transition-opacity duration-500 ease-in-out;

            .handle {
                @apply relative cursor-default rounded-md border;
                @include bg-color(scrollbar-handle-bg-color, 'colors.gray.350');
                @include border-color(scrollbar-handle-border-color, 'colors.gray.300');
            }
        }

        &.vertical {
            @apply w-3 h-full top-0 right-0;

            .handle {
                @apply w-3;
                left: -1px;
            }
        }

        &.horizontal {
            @apply h-3 w-full bottom-0 right-0;

            .handle {
                @apply h-3;
                top: -1px;
            }
        }
    }

    .transition {
        @apply transition-all duration-500 ease-in-out;
    }
}
</style>