<template>
    <slot v-if="action == 'query'"></slot>
    <template v-else-if="action == 'browse'">
        <p v-if="options.display_length > 0">
            {{ String(modelValue).slice(0, options.display_length) }}
        </p>
        <p v-else>
            {{ modelValue }}
        </p>
    </template>
    <template v-else-if="action == 'edit' || action == 'add'">
        <input
            v-if="(options.rows || 1) == 1"
            type="text"
            class="input w-full"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="translate(options.placeholder, true)"
            :inputmode="options.inputmode || 'text'">
        <textarea
            v-else
            class="input w-full"
            :rows="options.rows"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="translate(options.placeholder)"
            :inputmode="options.inputmode || 'text'"></textarea>
    </template>
    <span v-else>
        {{ modelValue }}
    </span>
</template>

<script>
import formfield from '@mixins/formfield';

export default {
    mixins: [formfield],
}
</script>