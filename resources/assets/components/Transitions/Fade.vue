<template>
    <transition :class="class" v-on="hooks" enter-active-class="fade-in" leave-active-class="fade-out" move-class="fade-move">
        <slot></slot>
    </transition>
</template>

<script>
import baseTransition from './baseTransition';

export default {
    mixins: [baseTransition]
}
</script>

<style lang="scss" scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
.fade-in {
    animation-name: fadeIn;
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}
.fade-out {
    animation-name: fadeOut;
}

.fade-move {
    transition: transform .3s ease-out;
}
</style>