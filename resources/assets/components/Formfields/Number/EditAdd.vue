<template>
    <div>
        <plus-minus-input
            class="input w-full"
            v-bind:value="value"
            @input="$emit('input', $event.target.value)"
            :min="options.min"
            :max="options.max"
            :step="options.step"
            :placeholder="translate(options.placeholder, true)" />
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
};
</script>