<template>
    <slot v-if="action == 'query'"></slot>
    <div v-else-if="action == 'browse'">
        <span v-if="Array.isArray(modelValue) && modelValue.length == 2">
            {{ __('voyager::formfields.slider.from_to', { lower: modelValue[0], upper: modelValue[1] }) }}
        </span>
        <span v-else>
            {{ modelValue }}
        </span>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <slider
            class="my-2"
            :inputs="options.inputs"
            :range="options.range"
            :min="options.min"
            :max="options.max"
            :step="options.step"
            :distance="options.distance"
            :color="options.color"
            v-model:lower="lowerModel"
            v-model:upper="upperModel"
        />
    </div>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        lowerModel: {
            get() {
                let model = this.prepareModel();
                if (this.options.range) {
                    return model[0];
                }
                
                return model;
            },
            set(value) {
                if (this.options.range) {
                    this.$emit('update:modelValue', [value, this.modelValue[1]]);
                } else {
                    this.$emit('update:modelValue', value);
                }
            }
        },
        upperModel: {
            get() {
                let model = this.prepareModel();
                if (this.options.range) {
                    return model[1];
                }

                return this.options.max;
            },
            set(value) {
                if (this.options.range) {
                    this.$emit('update:modelValue', [this.modelValue[0], value]);
                }
            }
        }
    },
    methods: {
        prepareModel() {
            if (this.options.range) {
                if (!Array.isArray(this.modelValue) || this.modelValue.length !== 2) {
                    this.$emit('update:modelValue', [this.options.min, this.options.max]);
                    return [this.options.min, this.options.max];
                }
            } else if (Array.isArray(this.modelValue) && this.modelValue.length > 0) {
                this.$emit('update:modelValue', this.modelValue[0]);
                return this.modelValue[0];
            } else if (typeof this.modelValue === 'string' || this.modelValue instanceof String) {
                this.$emit('update:modelValue', this.options.min);
                return this.options.min;
            }

            return this.modelValue;
        }
    }
}
</script>