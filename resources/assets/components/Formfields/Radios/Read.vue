<template>
    <div>
        {{ optionValue }}
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    computed: {
        optionValue: function () {
            var vm = this;
            var option = vm.options.options.where('key', vm.data);

            if (option.length > 0) {
                return vm.translate(option[0].value);
            }

            return '';
        },
    },
};
</script>