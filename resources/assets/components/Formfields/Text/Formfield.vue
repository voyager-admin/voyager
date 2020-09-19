<template>
    <slot v-if="action == 'query'"></slot>
    <div v-else-if="action == 'browse'">
        <span v-if="options.display_length > 0">
            {{ String(modelValue).slice(0, options.display_length) }}
        </span>
        <span v-else>
            {{ modelValue }}
        </span>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <input
            v-if="(options.rows || 1) == 1"
            type="text"
            class="input w-full"
            :value="modelValue || translate(options.default_value, true)"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="translate(options.placeholder, true)"
            :inputmode="options.inputmode || 'text'">
        <textarea
            v-else
            class="input w-full"
            :rows="options.rows"
            :value="modelValue || translate(options.default_value)"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="translate(options.placeholder)"
            :inputmode="options.inputmode || 'text'"></textarea>
    </div>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield]
}
</script>