<template>
    <div class="flex space-x-4">
        <div class="flex-1" v-for="(select, i) in selects" :key="'select-'+i">
            <label class="label" v-if="isObject(select) && select.hasOwnProperty('label')">{{ select.label }}</label>

            <div v-if="isObject(select) && select.hasOwnProperty('type')">
                <input type="number" class="input w-full small" v-if="select.type == 'number'" v-model.number="selected[i]" />
                <input type="text" class="input w-full small" v-if="select.type == 'text'" v-model="selected[i]" />
                <input type="checkbox" class="input" v-if="select.type == 'checkbox'" v-model="selected[i]" />
            </div>
            <select class="input w-full small" v-model="selected[i]" v-else-if="isArray(select) || isObject(select)">
                <template v-for="(option, b) in select" :key="'option-'+b">
                    <option :value="b" v-if="b !== 'label'">
                        {{ option }}
                    </option>
                </template>
            </select>
            <span v-else>
                {{ select }}
            </span>
        </div>
    </div>
</template>

<script>
import fetch from '../../../js/fetch';

export default {
    emits: ['update:modelValue'],
    props: ['options', 'modelValue', 'show'],
    data: function () {
        return {
            selects: [],
            selected: [],
        };
    },
    methods: {
        loadOptions: function () {
            var vm = this;

            if (vm.options.route_name == '') {
                return;
            }

            fetch.post(vm.route(vm.options.route_name), {
                selected: vm.selected,
            })
            .then(function (response) {
                vm.selects = response.data;
                // TODO: Never set length of an array in Vue as it can't detect this change
                vm.selected.length = response.data.length;

                vm.triggerChange();
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            });
        },
        triggerChange: function () {
            if (this.selects.length > 1) {
                this.$emit('update:modelValue', this.selected);
            } else {
                this.$emit('update:modelValue', this.selected[0]);
            }
        }
    },
    created: function () {
        this.$watch(
            () => this.selected,
            function (selected) {
                this.loadOptions();
                this.triggerChange();
            }
        );
    },
    mounted: function () {
        if (this.isArray(this.modelValue)) {
            this.selected = this.modelValue;
        } else if (this.modelValue !== null && this.modelValue !== '') {
            try {
                var json = JSON.parse(this.modelValue);
                if (this.isArray(json)) {
                    this.selected = json;
                }
            } catch {
                this.selected.push(this.modelValue);
            }
        }
        this.loadOptions();
    }
};
</script>