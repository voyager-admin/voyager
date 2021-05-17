<template>
    <textarea rows="10" v-model="json" class="input w-full"></textarea>
</template>
<script>
export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: Object,
            required: true,
        }
    },
    computed: {
        json: {
            get() {
                return JSON.stringify(this.modelValue, null, 2);
            },
            set(value) {
                try {
                    let json = JSON.parse(value);
                    this.$emit('update:modelValue', json);
                } catch (e) { }
            }
        }
    }
};
</script>