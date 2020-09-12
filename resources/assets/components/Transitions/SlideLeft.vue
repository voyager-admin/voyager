<template>
    <transition v-if="!group" :class="class" v-on="hooks" enter-active-class="slide-in" leave-active-class="slide-out">
        <slot></slot>
    </transition>
    <transition-group v-else :tag="tag" :class="class" v-on="hooks" enter-active-class="slide-in" leave-active-class="slide-out">
        <slot></slot>
    </transition-group>
</template>

<script>
import baseTransition from './baseTransition';

export default {
    mixins: [baseTransition]
}
</script>

<style lang="scss" scoped>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(15px);
    }
    to {
        opacity: 1;
    }
}
.slide-in {
    animation-name: slideIn;
}

@keyframes slideOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
        transform: translateX(15px);
    }
}
.slide-out {
    animation-name: slideOut;
}
</style>