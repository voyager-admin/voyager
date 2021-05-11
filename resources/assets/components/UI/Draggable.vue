<template>
    <component :is="as" ref="container">
        <slot />
    </component>
</template>

<script>
import { Sortable } from '@shopify/draggable';

export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: Array,
            default: () => []
        },
        as: {
            type: String,
            default: 'tbody',
        },
        draggable: {
            type: String,
            default: '.dd-source'
        },
        handle: {
            type: String,
            default: null,
        }
    },
    data() {
        return {
            instance: null,
        }
    },
    methods: {
        createDraggable(value) {
            // Set keys to each draggable child
            let found = false;
            this.$el.querySelectorAll(this.draggable).forEach((child, key) => {
                child.setAttribute('key', key);
            });

            if (found) {
                this.$emit('update:modelValue', value);
            }

            if (this.instance) {
                this.instance.destroy();
            }

            this.instance = new Sortable(this.$el, {
                draggable: this.draggable,
                handle: this.handle,
                mirror: {
                    appendTo: this.$el,
                    constrainDimensions: true,
                },
            });

            this.instance.on('drag:stopped', () => this.getNewModelValue());
        },
        getNewModelValue() {
            let newModel = [];
            this.$el.querySelectorAll(this.draggable).forEach((child, key) => {
                newModel.push(this.modelValue[child.getAttribute('key')]);
            });

            this.$emit('update:modelValue', newModel);
        }
    },
    mounted() {
        this.$watch(() => this.modelValue, (v) => {
            this.$nextTick(() => {
                this.createDraggable(v);
            });
        }, { immediate: true, deep: true });
    }
};
</script>