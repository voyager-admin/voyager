<template>
    <div>
        <tag-input
            :min="options.min"
            :max="options.max"
            :badge-color="options.color || 'blue'"
            :allow-reorder="options.reorder || true"
            v-bind:value="value || []"
            @input="$emit('input', $event)">
        </tag-input>
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
};
</script>