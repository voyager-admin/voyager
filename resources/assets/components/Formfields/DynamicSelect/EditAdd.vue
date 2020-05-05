<template>
    <div>
        <label class="label" v-if="translate(options.label, true) !== ''">{{ translate(options.label, true) }}</label>
        <div class="flex">
            <div class="flex-1 mx-2" v-for="(select, i) in selects" :key="'select-'+i">
                <select class="voyager-input w-full" v-model="selected[i]">
                    <option v-for="(option, b) in select" :key="'option-'+b" :value="b">
                        {{ option }}
                    </option>
                </select>
            </div>
        </div>
        <p class="description" v-if="translate(options.description, true) !== ''">
            {{ translate(options.description, true) }}
        </p>
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

            if (this.selects.length == 1) {
                this.$emit('input', this.selected[0]);
            } else {
                this.$emit('input', this.selected);
            }
        }
    },
    methods: {
        loadOptions: function () {
            var vm = this;
            if (vm.options.route_name == '') {
                return;
            }
            axios.post(vm.route(vm.options.route_name), {
                selected: vm.selected,
            })
            .then(function (response) {
                vm.selects = response.data;
            });
        }
    },
    mounted: function () {
        this.loadOptions();
        this.selected.push(this.value);
    }
};
</script>