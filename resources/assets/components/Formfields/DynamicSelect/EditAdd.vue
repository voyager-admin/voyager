<template>
    <div>
        <div class="flex space-x-4">
            <div class="flex-1" v-for="(select, i) in selects" :key="'select-'+i">
                <select class="input w-full" v-model="selected[i]">
                    <option v-for="(option, b) in select" :key="'option-'+b" :value="b">
                        {{ option }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
    data: function () {
        return {
            selects: [],
            selected: [],
        };
    },
    watch: {
        selected: function (selected) {
            this.loadOptions();

            if (this.selects.length > 1) {
                this.$emit('input', this.selected);
            } else {
                this.$emit('input', this.selected[0]);
            }
        }
    },
    methods: {
        loadOptions: function () {
            var vm = this;

            if (this.value !== null && this.value !== '') {
                try {
                    var json = JSON.parse(this.value);
                    if (vm.isArray(json)) {
                        this.selected = json;
                    }
                } catch { }
            }

            if (vm.options.route_name == '') {
                return;
            }
            axios.post(vm.route(vm.options.route_name), {
                selected: vm.selected,
            })
            .then(function (response) {
                vm.selects = response.data;
                vm.selected.length = response.data.length;
            });
        }
    },
    mounted: function () {
        this.loadOptions();
        this.selected.push(this.value);
    }
};
</script>