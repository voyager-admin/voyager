<template>
    <component :is="component" v-bind="$page.props" />
</template>
<script>
export default {
    computed: {
        component() {
            if (this.$voyager.componentExists(this.$page.component)) {
                return this.$page.component;
            } else {
                // TODO: Translate
                console.error(`Component "${this.$page.component}" does not exist. This page will be empty!`);
            }
        }
    },
}
</script>