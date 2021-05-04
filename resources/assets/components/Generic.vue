<template>
    <component :is="component" />
</template>
<script>
export default {
    computed: {
        component() {
            return this.$page.component;
        }
    }
}
</script>