<template>
    <input
        type="password"
        class="input w-full"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="translate(options.placeholder, true)">
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['options', 'modelValue', 'show'],
};
</script>