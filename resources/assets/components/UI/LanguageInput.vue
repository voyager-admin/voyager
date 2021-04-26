<template>
    <input v-model="currentText">
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';

export default {
    emits: ['update:modelValue'],
    props: ['modelValue'],
    data() {
        return {
            translations: {}
        };
    },
    computed: {
        currentText: {
            get() {
                return this.translations[this.locale];
            },
            set(value) {
                this.translations[this.locale] = value;
                this.$emit('update:modelValue', this.translations);
            }
        },
        locale() {
            return usePage().props.value.locale;
        }
    },
    created() {
        this.$watch(() => this.modelValue, (value) => {
            this.translations = this.get_translatable_object(value);
        }, { immediate: true });
    },
};
</script>