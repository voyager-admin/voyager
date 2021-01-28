<template>
    <div class="w-full flex justify-center">
        <img :src="file.file.url" class="rounded object-contain max-w-full" />
    </div>
    <div class="w-full flex justify-center mt-2">
        <!-- Tools -->
    </div>
</template>

<script>
export default {
    props: {
        file: {
            type: Object,
            required: true,
        }
    }
}
</script>

<style lang="sass" scoped>

</style>