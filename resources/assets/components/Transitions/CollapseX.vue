<template>
    <transition
        :class="class"
        @before-enter="beforeEnter"
        @after-enter="afterEnter"
        @enter="enter"
        @before-leave="beforeLeave"
        @leave="leave"
        @after-leave="afterLeave"
        move-class="collapse-move"
    >
        <slot></slot>
    </transition>
</template>

<script>
import baseTransition from './baseTransition';

export default {
    mixins: [baseTransition],
    methods: {
        transitionStyle: function (duration = 300) {
            var duration = duration / 1000;
            return `${duration}s width ease-in-out`;
        },
        beforeEnter: function (el) {
            el.style.transition = this.transitionStyle(this.enterDuration);
            el.style.width = '0';
            this.setStyles(el);
        },
        enter: function (el) {
            if (el.scrollWidth !== 0) {
                el.style.width = el.scrollWidth + 'px';
            } else {
                el.style.width = '';
            }
            el.style.overflow = 'hidden';
        },
        afterEnter: function (el) {
            el.style.transition = '';
            el.style.width = '';
        },
        beforeLeave: function (el) {
            el.style.width = el.scrollWidth + 'px';
            el.style.overflow = 'hidden';
            this.setStyles(el);
        },
        leave: function (el) {
            if (el.scrollWidth !== 0) {
                el.style.transition = this.transitionStyle(this.leaveDuration);
                el.style.width = 0;
            }
            this.setAbsolutePositioning(el);
        },
        afterLeave: function (el) {
            el.style.transition = ''
            el.style.width = '';
        }
    }
}
</script>

<style lang="scss" scoped>
.collapse-move {
    transition: transform .3s ease;
}
</style>