<template>
    <alert v-if="lastError !== null" color="yellow" class="mb-4">
        <p>{{ __('voyager::generic.json_invalid') }} <a href="#" @click.prevent="refresh">{{ __('voyager::generic.refresh') }}</a></p>
        <code>{{ lastError }}</code>
    </alert>
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
    data() {
        return {
            lastState: {},
            lastError: null,
        };
    },
    computed: {
        json: {
            get() {
                if (this.lastError !== null) {
                    return this.lastState;
                }

                return JSON.stringify(this.modelValue, null, this.indentation);
            },
            set(value) {
                try {
                    let json = JSON.parse(value);
                    this.$emit('update:modelValue', json);
                    this.lastError = null;
                } catch (e) {
                    this.lastState = value;
                    this.lastError = e;
                }
            }
        },
        editorExists() {
            //return resolveComponent('json-code-editor') !== 'json-code-editor';
            return false;
        }
    },
    methods: {
        refresh() {
            this.json = JSON.stringify(this.modelValue, null, this.indentation);
        }
    }
};
</script>