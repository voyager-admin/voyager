<template>
    <div>
        <input
            v-if="options.rows == 1"
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
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['options', 'modelValue', 'show'],
};
</script>