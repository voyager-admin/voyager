<template>
    <div>
        <div class="flex space-x-4">
            <div class="flex-1" v-for="(select, i) in selects" :key="'select-'+i">
                <label class="label" v-if="isObject(select) && select.hasOwnProperty('label')">{{ select.label }}</label>

                <div v-if="isObject(select) && select.hasOwnProperty('type')">
                    <input type="number" class="input w-full small" v-if="select.type == 'number'" v-model.number="selected[i]" />
                    <input type="text" class="input w-full small" v-if="select.type == 'text'" v-model="selected[i]" />
                    <input type="checkbox" class="input" v-if="select.type == 'checkbox'" v-model="selected[i]" />
                </div>
                <select class="input w-full small" v-model="selected[i]" v-else-if="isArray(select) || isObject(select)">
                    <option v-for="(option, b) in select" :key="'option-'+b" :value="b" v-if="b !== 'label'">
                        {{ option }}
                    </option>
                </select>
                <span v-else>
                    {{ select }}
                </span>
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

            this.triggerChange();
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
                vm.selected.length = response.data.length;

                vm.triggerChange();
            });
        },
        triggerChange: function () {
            if (this.selects.length > 1) {
                this.$emit('input', this.selected);
            } else {
                this.$emit('input', this.selected[0]);
            }
        }
    },
    mounted: function () {
        if (this.isArray(this.value)) {
            this.selected = this.value;
        } else if (this.value !== null && this.value !== '') {
            try {
                var json = JSON.parse(this.value);
                if (this.isArray(json)) {
                    this.selected = json;
                }
            } catch {
                this.selected.push(this.value);
            }
        }
        this.loadOptions();
    }
};
</script>