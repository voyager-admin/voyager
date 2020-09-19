<template>
    <div v-if="action == 'query'">
        <input
            class="input small w-full"
            :value="modelValue"
            type="number"
            :min="options.min || 0"
            :max="options.max || Number.MAX_SAFE_INTEGER"
            :step="options.step || 1"
            :placeholder="placeholder"
            @input="$emit('update:modelValue', $event.target.value)"
            @dblclick="$emit('update:modelValue', null)"
            @keydown.esc="$emit('update:modelValue', null)"
        />
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
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
    </div>
    <div v-else>
        {{ formattedNumber }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        formattedNumber: function () {
            return this.numberFormat(this.modelValue, this.options.decimals || 0, this.options.dec_point || '.', this.options.thousands_sep || ',');
        }
    }
}
</script>