<template>
    <component :is="tag" :class="class">
        <slot></slot>
    </component>
</template>

<script>
import baseTransition from './baseTransition';

export default {
    mixins: [baseTransition]
}
</script>

<style lang="scss" scoped>

</style>