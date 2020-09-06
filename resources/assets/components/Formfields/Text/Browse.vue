<template>
    <slot v-if="show == 'query'"></slot>
    <div v-else>
        <span v-if="options.display_length > 0">
            {{ String(value).slice(0, options.display_length) }}
        </span>
        <span v-else>
            {{ value }}
        </span>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
};
</script>