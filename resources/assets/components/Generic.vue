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
                console.error(this.__('voyager::generic.component_does_not_exist', { component: this.$page.component, default: this.__('voyager::generic.none') }));
            }
        }
    },
}
</script>