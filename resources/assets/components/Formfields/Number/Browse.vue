<template>
    <div v-if="show == 'query'">
        <plus-minus-input class="input small w-full" v-model="value" />
    </div>
    <div v-else>
        {{ formattedNumber }}
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    computed: {
        formattedNumber: function () {
            return this.number_format(this.value, this.options.decimals, this.options.dec_point, this.options.thousands_sep);
        }
    }
};
</script>