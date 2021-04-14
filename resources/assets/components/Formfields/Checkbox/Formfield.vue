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
        <badge v-for="(option, i) in (modelValue || [])" :key="i">
            {{ getOptionByKey(option) || option }}
        </badge>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'" class="w-full" :class="options.inline ? 'space-x-1.5' : null">
        <template v-for="(option, i) in options.options" :key="i">
            <div class="inline-flex items-center space-x-1.5" :class="options.inline ? null : 'w-full'">
                <input type="checkbox" class="input" :value="option.key" v-model="value" />
                <label class="label">{{ translate(option.value, true) }}</label>
            </div>
        </template>
    </div>
    <div v-else>
        <badge v-for="(option, i) in modelValue" :key="i">
            {{ getOptionByKey(option) }}
        </badge>
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        value: {
            get() {
                if (!this.isArray(this.modelValue)) {
                    this.$emit('update:modelValue', []);
                    return [];
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
        defaultViewOptions() {
            return {
                options: [],
                inline: false,
            };
        },
        defaultListOptions() {
            return {
                options: [],
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
    },
}
</script>