<template>
    <div>
        {{ translate(data, !translatable) }}
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
};
</script>