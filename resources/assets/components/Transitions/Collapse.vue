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
        transitionStyle(duration = 300) {
            return `${(duration / 1000)}s height ease-in-out`;
        },
        beforeEnter(el) {
            el.style.transition = this.transitionStyle(this.enterDuration);
            el.style.height = '0';
            this.setStyles(el);
        },
        enter(el) {
            if (el.scrollHeight !== 0) {
                el.style.height = el.scrollHeight + 'px';
            } else {
                el.style.height = '';
            }
            el.style.overflow = 'hidden';
        },
        afterEnter(el) {
            el.style.transition = '';
            el.style.height = '';
        },
        beforeLeave(el) {
            el.style.height = el.scrollHeight + 'px';
            el.style.overflow = 'hidden';
            this.setStyles(el);
        },
        leave(el) {
            if (el.scrollHeight !== 0) {
                el.style.transition = this.transitionStyle(this.leaveDuration);
                el.style.height = 0;
            }
            this.setAbsolutePositioning(el);
        },
        afterLeave(el) {
            el.style.transition = '';
            el.style.height = '';
        }
    }
}
</script>

<style lang="scss" scoped>
.collapse-move {
    transition: transform .3s ease-in-out;
}
</style>