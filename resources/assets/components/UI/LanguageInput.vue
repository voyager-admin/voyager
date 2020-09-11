<template>
    <input v-model="currentText">
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['modelValue'],
    data: function () {
        return {
            translations: {}
        };
    },
    computed: {
        currentText: {
            get: function () {
                return this.translations[this.$store.locale];
            },
            set: function (value) {
                this.translations[this.$store.locale] = value;
                // TODO: Vue.set(this.translations, this.$store.locale, value);
                this.$emit('update:modelValue', this.translations);
            }
        }
    },
    created: function () {
        this.$watch(
            () => this.modelValue,
            function (value) {
                this.translations = this.get_translatable_object(value);
            },
            { immediate: true }
        );
    },
};
</script>