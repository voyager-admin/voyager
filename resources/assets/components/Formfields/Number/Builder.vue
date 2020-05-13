<template>
    <div>
        <div v-if="show == 'list-options'">
            <label class="label mt-4">{{ __('voyager::formfields.number.decimals') }}</label>
            <input
                class="voyager-input w-full"
                type="number" :placeholder="__('voyager::formfields.number.decimals')"
                v-model.number="options.decimals" />

            <label class="label mt-4">{{ __('voyager::formfields.number.decimal_separator') }}</label>
            <input
                class="voyager-input w-full"
                type="text" :placeholder="__('voyager::formfields.number.decimal_separator')"
                v-model="options.dec_point" />

            <label class="label mt-4">{{ __('voyager::formfields.number.thousands_separator') }}</label>
            <input
                class="voyager-input w-full"
                type="text" :placeholder="__('voyager::formfields.number.thousands_separator')"
                v-model="options.thousands_sep" />
        </div>
        <div v-else-if="show == 'view-options'">
            <label class="label mt-4">{{ __('voyager::generic.placeholder') }}</label>
            <language-input
                class="voyager-input w-full"
                type="text" :placeholder="__('voyager::generic.placeholder')"
                v-bind:value="options.placeholder"
                v-on:input="options.placeholder = $event" /> 

            <label class="label mt-4">{{ __('voyager::generic.minimum') }}</label>
            <input
                class="voyager-input w-full"
                type="number" :placeholder="__('voyager::generic.minimum')"
                v-model.number="options.min" />

            <label class="label mt-4">{{ __('voyager::generic.maximum') }}</label>
            <input
                class="voyager-input w-full"
                type="number" :placeholder="__('voyager::generic.maximum')"
                v-model.number="options.max" />

            <label class="label mt-4">{{ __('voyager::generic.step') }}</label>
            <input
                class="voyager-input w-full"
                type="number" :placeholder="__('voyager::generic.placeholder')"
                v-model.number="options.step" />
        </div>
        <div v-else-if="show == 'view'">
            <input
                type="text"
                class="voyager-input w-full"
                :min="options.min"
                :max="options.max"
                :step="options.step"
                :placeholder="translate(options.placeholder)">
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>