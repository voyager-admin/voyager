<template>
    <div class="overflow-hidden" :class="`h-${size} w-${size}`"></div>
</template>

<script>
export default {
    props: {
        size: {
            type: Number,
            default: 5,
        },
    }
}
</script>