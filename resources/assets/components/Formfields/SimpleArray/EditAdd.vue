<template>
    <div>
        <array-form v-bind:value="value || []" @input="$emit('input', $event)" title-text="" :item-text="translate(options.item_text, false)" />
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
};
</script>