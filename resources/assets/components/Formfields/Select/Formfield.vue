<template>
    <div v-if="action == 'query'">
        <select class="input w-full" v-model="queryValue">
            <option :value="null" v-if="options.options.whereNull('key').length == 0">{{ __('voyager::generic.none') }}</option>
            <option v-for="(option, i) in options.options" :key="i" :value="option.key">
                {{ translate(option.value) }}
            </option>
        </select>
    </div>
    <div v-else-if="action == 'browse'">
        <span v-if="options.multiple && isArray(modelValue)">
            <span v-for="(option, i) in modelValue" :key="i">
                {{ getOptionByKey(option) }}
                <span v-if="i < modelValue.length - 1">, </span>
            </span>
        </span>
        <span v-else>
            {{ getOptionByKey(modelValue) }}
        </span>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <select class="input w-full" :multiple="options.multiple" v-model="value">
            <option v-for="(option, i) in options.options" :key="i" :value="option.key">
                {{ translate(option.value, true) }}
            </option>
        </select>
    </div>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        value: {
            get() {
                if ((this.options.multiple || false) && !this.isArray(this.modelValue)) {
                    this.$emit('update:modelValue', []);
                    return [];
                }

                if (this.modelValue === '') {
                    return null;
                }

                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        queryValue: {
            get() {
                return this.modelValue || null;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        defaultListOptions() {
            return {
                options: [],
                multiple: false,
            };
        },
        defaultViewOptions() {
            return {
                options: [],
                multiple: false,
            };
        },
    },
    methods: {
        getOptionByKey(key) {
            var option = (this.options.options || []).where('key', key).first();
            if (option) {
                return this.translate(option.value);
            }

            return '';
        }
    }
}
</script>