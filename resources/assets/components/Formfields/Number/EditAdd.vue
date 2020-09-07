<template>
    <plus-minus-input
        class="input w-full"
        :model-value="value"
        @update:model-value="$emit('update:modelValue', $event)"
        :min="options.min"
        :max="options.max"
        :step="options.step"
        :placeholder="translate(options.placeholder, true)" />
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['options', 'value', 'show'],
};
</script>