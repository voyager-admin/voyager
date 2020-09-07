<template>
    <tag-input
        :min="options.min"
        :max="options.max"
        :badge-color="options.color || 'blue'"
        :allow-reorder="options.reorder || true"
        :modelValue="modelValue || []"
        @input="$emit('update:modelValue', $event)">
    </tag-input>
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['options', 'modelValue', 'show'],
};
</script>