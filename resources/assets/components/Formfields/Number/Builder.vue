<template>
    <div v-if="action == 'list-options'">
        <label class="label mt-4">{{ __('voyager::formfields.number.decimals') }}</label>
        <input
            type="number"
            class="input w-full"
            :placeholder="__('voyager::formfields.number.decimals')"
            v-model.number="options.decimals" />

        <label class="label mt-4">{{ __('voyager::formfields.number.decimal_separator') }}</label>
        <input
            class="input w-full"
            type="text" :placeholder="__('voyager::formfields.number.decimal_separator')"
            v-model="options.dec_point" />

        <label class="label mt-4">{{ __('voyager::formfields.number.thousands_separator') }}</label>
        <input
            class="input w-full"
            type="text" :placeholder="__('voyager::formfields.number.thousands_separator')"
            v-model="options.thousands_sep" />
    </div>
    <div v-else-if="action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.placeholder') }}</label>
        <language-input
            class="input w-full"
            type="text" :placeholder="__('voyager::generic.placeholder')"
            v-model="options.placeholder" /> 

        <label class="label mt-4">{{ __('voyager::generic.minimum') }}</label>
        <input
            type="number"
            class="input w-full"
            :placeholder="__('voyager::generic.minimum')"
            v-model.number="options.min" />

        <label class="label mt-4">{{ __('voyager::generic.maximum') }}</label>
        <input
            type="number"
            class="input w-full"
            :placeholder="__('voyager::generic.maximum')"
            v-model.number="options.max" />

        <label class="label mt-4">{{ __('voyager::generic.step') }}</label>
        <input
            type="number"
            class="input w-full"
            :placeholder="__('voyager::generic.placeholder')"
            v-model.number="options.step" />
        <label class="label mt-4">{{ __('voyager::formfields.number.decimals') }}</label>
        <input
            type="number"
            class="input w-full"
            :placeholder="__('voyager::formfields.number.decimals')"
            v-model.number="options.decimals" />

        <label class="label mt-4">{{ __('voyager::formfields.number.decimal_separator') }}</label>
        <input
            class="input w-full"
            type="text" :placeholder="__('voyager::formfields.number.decimal_separator')"
            v-model="options.dec_point" />

        <label class="label mt-4">{{ __('voyager::formfields.number.thousands_separator') }}</label>
        <input
            class="input w-full"
            type="text" :placeholder="__('voyager::formfields.number.thousands_separator')"
            v-model="options.thousands_sep" />
    </div>
    <div v-else-if="action == 'view'">
        <input
            type="text"
            class="input w-full"
            :min="options.min"
            :max="options.max"
            :step="options.step"
            :placeholder="translate(options.placeholder)"
        >
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                placeholder: '',
                min: 0,
                max: 0,
                step: 1,
                decimals: 0,
                dec_point: '.',
                thousands_sep: ',',
            };
        },
        defaultListOptions() {
            return {
                placeholder: '',
                decimals: 0,
                dec_point: '.',
                thousands_sep: ',',
            };
        },
    }
}
</script>