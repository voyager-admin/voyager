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
    watch: {
        modelValue: {
            immediate: true,
            handler: function (val) {
                this.translations = this.get_translatable_object(this.modelValue);
            }
        }
    }
};
</script>