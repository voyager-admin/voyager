<template>
    <div v-if="action == 'query'">
        
    </div>
    <div v-else-if="action == 'browse'">
        
    </div>
    <div v-else-if="action == 'edit' || action == 'add'" class="w-full" :class="options.inline ? 'flex space-x-4' : 'space-y-4'">
        <div v-for="(input, num) in inputs" class="w-full" :class="options.outline ? 'input-group' : null">
            <label v-if="input.title" class="label">{{ input.title }}</label>
            <template v-if="input.type == 'select'">
                <select class="input mt-2 w-full" @change="setSelectValue(input, $event)" :multiple="input.multiple" :id="`input-${column.column}-${num}`">
                    <option
                        v-for="(title, value) in input.options"
                        :value="value"
                        :selected="optionSelected(input, value)"
                    >
                        {{ title }}
                    </option>
                </select>
            </template>
            <template v-else-if="input.type == 'text'">
                <input
                    type="text"
                    class="input mt-2 w-full"
                    v-bind:value="getValue(input)"
                    @input="setValue(input, $event.target.value)"
                    :placeholder="input.placeholder || ''"
                >
            </template>
            <template v-else-if="input.type == 'number'">
                <input
                    type="number"
                    class="input mt-2 w-full"
                    v-bind:value="getValue(input)"
                    @input="setValue(input, $event.target.value)"
                    :placeholder="input.placeholder || ''"
                    :min="input.min"
                    :max="input.max"
                >
            </template>
            <template v-else-if="input.type == 'checkbox' || input.type == 'radio'">
                <template v-for="(title, value) in input.options">
                    <div class="flex space-x-1.5 items-center">
                        <input
                            :type="input.type"
                            class="input w-full"
                            :value="value"
                            @change="setSelectValue(input, $event)"
                            :id="`${column.column}-${value}`"
                            :name="`${column.column}-${input.key}`"
                            :checked="optionSelected(input, value)"
                        >
                        <label :for="`${column.column}-${value}`" class="label">{{ title }}</label>
                    </div>
                </template>
            </template>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import formfield from '../../../js/mixins/formfield';
import { debounce } from 'debounce';

export default {
    mixins: [formfield],
    data() {
        return {
            inputs: [],
            should: 0,
            has: 0,
            load: null,
        };
    },
    methods: {
        optionSelected(input, value) {
            let current = this.getValue(input);
            if (Array.isArray(current)) {
                return current.includes(value);
            }

            return current == value;
        },
        getValue(input) {
            if (input.key === null) {
                return this.modelValue || input.value || null;
            }

            if (this.modelValue) {
                return this.modelValue[input.key] || input.value || null;
            }

            return input.value || null;
        },
        setSelectValue(input, select) {
            let value = null;
            if (input.multiple) {
                value = [];

                if (input.type == 'select') {
                    Array.from(select.target.options).forEach((option, key) => {
                        if (option.selected) {
                            if (option.value === '') {
                                value.push(null);
                            } else {
                                value.push(option.value);
                            }
                        }
                    });
                } else if (input.type == 'checkbox') {
                    value = this.getValue(input);
                    if (!Array.isArray(value)) {
                        value = [];
                    }
                    if (select.target.checked) {
                        value.push(select.target.value);
                    } else {
                        value = value.filter((v) => v !== select.target.value);
                    }
                }
            } else {
                value = select.target.value;
                if (value === '') {
                    value = null;
                }
            }

            this.setValue(input, value);
        },
        setValue(input, value) {
            if (this.isNumeric(value)) {
                value = parseInt(value);
            }
            if (input.key === null) {
                this.$emit('update:modelValue', value);
            } else {
                let current = this.modelValue;
                if (!current || typeof current !== 'object' || current.constructor !== Object) {
                    current = {};
                }
                current[input.key] = value;
                this.$emit('update:modelValue', current);
            }
        },
        isNumeric(input) {
            return !isNaN(parseFloat(input)) && isFinite(input);
        }
    },
    created() {
        const loadInputs = debounce(() => {
            if (!this.options.route_name) {
                return;
            }
            try {
                this.route(this.options.route_name);
            } catch (e) {
                new this.$notification(this.__('voyager::formfields.dynamic_select.route_warning', { route: this.options.route_name })).color('red').timeout().show();
                return;
            }
            axios.post(this.route(this.options.route_name), { ...this.modelValue, bread_action: this.action })
            .then((response) => {
                this.inputs = response.data;
                // Add default value if the property does not exist
                response.data.forEach((input) => {
                    if (input.key) {
                        var current = this.modelValue;
                        if (!current || typeof current !== 'object' || current.constructor !== Object) {
                            current = {};
                        }
                        if (!current.hasOwnProperty(input.key)) {
                            current[input.key] = input.value;

                            this.$emit('update:modelValue', current);
                        }
                        
                    } else if (this.modelValue === null && input.value) {
                        this.$emit('update:modelValue', input.value);
                    }
                });
            }).catch((response) => {});
        }, this.options.debounce || 200, false);

        this.$watch(() => this.modelValue, () => {
            loadInputs();
        }, { immediate: true, deep: true });

        this.$watch(() => this.options.route_name, (route, old) => {
            if (route !== old) {
                loadInputs();
            }
        });
    }
}
</script>