<template>
    <div v-if="action == 'query'">
        
    </div>
    <div v-else-if="action == 'browse'">
        
    </div>
    <div v-else-if="action == 'edit' || action == 'add'" class="w-full flex space-x-4">
        <div v-for="(select, i) in selects" :key="'select-'+i" class="flex-1">
            <label class="label" v-if="isObject(select) && select.hasOwnProperty('label')">{{ select.label }}</label>
             <div v-if="isObject(select) && select.hasOwnProperty('type')">
                <input type="number" class="input w-full small" v-if="select.type == 'number'" :value="getValue(i)" @input="setValue(i, $event.target.value)" />
                <input type="text" class="input w-full small" v-if="select.type == 'text'" :value="getValue(i)" @input="setValue(i, $event.target.value)" />
                <input type="checkbox" class="input" v-if="select.type == 'checkbox'" :value="getValue(i)" @input="setValue(i, $event.target.checked)" />
            </div>
            <select class="input w-full small" :value="getValue(i)" @change="setValue(i, $event.target.value)" v-else-if="isArray(select) || isObject(select)">
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
import { isReactive, toRaw } from 'vue';
import axios from 'axios';
import formfield from '../../../js/mixins/formfield';
import debounce from 'debounce';

export default {
    mixins: [formfield],
    data() {
        return {
            selects: [],
        };
    },
    methods: {
        loadOptions: debounce(function () {
            if (!this.options.route_name) {
                return;
            }
            var selected = this.modelValue || {};
            if (isReactive(selected)) {
                selected = toRaw(selected);
            }
            axios.post(this.route(this.options.route_name), selected)
            .then((response) => {
                this.selects = response.data;
            }).catch((response) => {});
        }, 250),
        getValue(i) {
            if (!this.isObject(this.modelValue)) {
                this.$emit('update:modelValue', {});
            }

            return (this.modelValue || {})[i] || '';
        },
        setValue(i, value) {
            var selected = this.modelValue;
            selected[i] = value;
            this.$emit('update:modelValue', selected);

            this.loadOptions();
        }
    },
    created() {
        this.loadOptions();
    }
}
</script>