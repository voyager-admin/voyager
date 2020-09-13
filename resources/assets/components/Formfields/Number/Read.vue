<template>
    <div>
        {{ formattedNumber }}
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    computed: {
        formattedNumber: function () {
            var value = this.translate(this.value, !this.translatable);
            value = this.numberFormat(value, this.options.decimals, this.options.dec_point, this.options.thousands_sep);
            return value;
        }
    }
};
</script>