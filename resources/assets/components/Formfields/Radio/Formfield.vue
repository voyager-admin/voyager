<template>
    <div v-if="action == 'query'">
        <select class="input w-full" v-model="queryValue">
            <option :value="null" v-if="options.options.whereNull('key').length == 0">{{ __('voyager::generic.none') }}</option>
            <option v-for="(option, i) in options.options" :key="i" :value="option.key">
                {{ translate(option.value) }}
            </option>
        </select>
    </div>
    <template v-else-if="action == 'browse'">
        {{ getOptionByKey(modelValue) }}
    </template>
    <template v-else-if="action == 'edit' || action == 'add'" class="w-full" :class="options.inline ? 'space-x-1.5' : null">
        <template v-for="(option, i) in options.options" :key="i">
            <div class="inline-flex items-center space-x-1.5" :class="options.inline ? null : 'w-full'">
                <input type="radio" class="input" :value="option.key" :checked="option.key == modelValue" @change="$event.target.checked ? $emit('update:modelValue', option.key) : null" />
                <label class="label">{{ translate(option.value, true) }}</label>
            </div>
        </template>
    </template>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '@mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        queryValue: {
            get() {
                return this.modelValue || null;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
    },
    methods: {
        getOptionByKey(key) {
            var option = (this.options.options || []).where('key', key).first();
            if (option) {
                return this.translate(option.value, true);
            }

            return '';
        }
    },
}
</script>