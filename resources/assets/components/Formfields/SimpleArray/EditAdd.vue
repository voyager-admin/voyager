<template>
    <div>
        <array-form
            :model-value="modelValue || []"
            @update:model-value="$emit('update:modelValue', $event)"
            title-text=""
            :item-text="translate(options.item_text, false)"
        />
    </div>
</template>

<script>
export default {
    props: ['options', 'modelValue'],
};
</script>