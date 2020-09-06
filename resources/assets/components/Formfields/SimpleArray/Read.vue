<template>
    <div>
        <badge v-for="(val, i) in selected" :key="i">{{ val }}</badge>
    </div>
</template>

<script>
export default {
    props: ['options', 'data', 'translatable'],
    methods: {
        getOptionByKey: function (key) {
            return this.options.options.where('key', key)[0] || null;
        }
    },
    computed: {
        selected: function () {
            var vm = this;

            var data = vm.data || [];
            if (!vm.isArray(data)) {
                data = [data];
            }

            return data.map(function (key) {
                var val = vm.getOptionByKey(key);
                if (vm.isObject(val)) {
                    return vm.translate(val.value, true);
                }
                return '';
            }).whereNot('value', '');
        },
    }
};
</script>