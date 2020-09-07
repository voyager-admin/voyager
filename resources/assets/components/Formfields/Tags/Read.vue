<template>
    <badge :color="options.color || 'blue'" v-for="(tag, i) in tags" :key="'tag-'+i">
        {{ tag }}
    </badge>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    data: function () {
        return {
            tags: [],
        };
    },
    mounted: function () {
        if (this.isArray(this.data)) {
            this.tags = this.data;
        }
    }
};
</script>