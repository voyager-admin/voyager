<template>
    <template v-if="editorExists">
        <json-code-editor v-model="modelValue" />
    </template>
    <textarea rows="10" v-model="json" class="input w-full" v-else></textarea>
</template>
<script>
import { resolveComponent } from 'vue';

export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: Object,
            required: true,
        }
    },
    computed: {
        json: {
            get() {
                return JSON.stringify(this.modelValue, null, 2);
            },
            set(value) {
                try {
                    let json = JSON.parse(value);
                    this.$emit('update:modelValue', json);
                } catch (e) { }
            }
        },
        editorExists() {
            return resolveComponent('json-code-editor') !== 'json-code-editor';
        }
    }
};
</script>