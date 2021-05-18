<template>
    <template v-if="editorExists">
        
    </template>
    <template v-else>
        <textarea rows="10" v-model="json" class="input w-full"></textarea>
    </template>
</template>
<script>
import { resolveComponent } from 'vue';

export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: Object,
            required: true,
        },
        indentation: {
            type: Number,
            default: 4
        }
    },
    computed: {
        json: {
            get() {
                return JSON.stringify(this.modelValue, null, this.indentation);
            },
            set(value) {
                try {
                    let json = JSON.parse(value);
                    this.$emit('update:modelValue', json);
                } catch (e) { }
            }
        },
        editorExists() {
            //return resolveComponent('json-code-editor') !== 'json-code-editor';
            return false;
        }
    }
};
</script>