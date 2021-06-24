<template>
    <div v-if="action == 'query'" class="w-full inline-flex space-x-0.5">
        <input
            class="input small"
            type="number"
            :min="options.min || 0"
            :max="options.max || Number.MAX_SAFE_INTEGER"
            :step="options.step || 1"
            :placeholder="placeholder"
            v-model.number="fromValue"
            @dblclick="fromValue = null"
            @keydown.esc="fromValue = null"
        />
        <input
            class="input small"
            type="number"
            :min="options.min || 0"
            :max="options.max || Number.MAX_SAFE_INTEGER"
            :step="options.step || 1"
            :placeholder="placeholder"
            v-model.number="toValue"
            @dblclick="toValue = null"
            @keydown.esc="toValue = null"
        />
    </div>
    <template v-else-if="action == 'edit' || action == 'add'">
        <input
            class="input w-full"
            type="number"
            :value="modelValue || 0"
            @input="$emit('update:modelValue', Number($event.target.value))"
            :min="options.min || 0"
            :max="options.max || Number.MAX_SAFE_INTEGER"
            :step="options.step || 1"
            :placeholder="translate(options.placeholder || '', true)"
        />
    </template>
    <span v-else>
        {{ formattedNumber }}
    </span>
</template>

<script>
import formfield from '@mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        formattedNumber() {
            return this.numberFormat(this.modelValue, this.options.decimals || 0, this.options.dec_point || '.', this.options.thousands_sep || ',');
        },
        fromValue: {
            get() {
                return (this.modelValue || {}).from || null;
            },
            set(val) {
                var value = this.modelValue || {
                    from: null,
                    to: null
                };
                value.from = val;
                value.to = (value.to === undefined ? null : value.to);

                this.$emit('update:modelValue', value);
            }
        },
        toValue: {
            get() {
                return (this.modelValue || {}).to || null;
            },
            set(val) {
                var value = this.modelValue || {
                    from: null,
                    to: null
                };
                value.from = (value.from === undefined ? null : value.from);
                value.to = val;

                this.$emit('update:modelValue', value);
            }
        },
    }
}
</script>