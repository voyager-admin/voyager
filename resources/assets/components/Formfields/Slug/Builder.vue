<template>
    <div>
        <div v-if="show == 'list-options'">
            <label for="length" class="label">{{ __('voyager::generic.display_length') }}</label>
            <plus-minus-input id="length" class="input w-full" v-model.number="options.display_length" /> 
        </div>
        <div v-else-if="show == 'view-options'">
            <label class="label mt-4">{{ __('voyager::generic.placeholder') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::generic.placeholder')"
                v-bind:value="options.placeholder"
                v-on:input="options.placeholder = $event" /> 

            <label class="label mt-4">{{ __('voyager::generic.default_value') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::generic.default_value')"
                v-bind:value="options.default_value"
                v-on:input="options.default_value = $event" /> 

            <label class="label mt-4">{{ __('voyager::formfields.slug.replacement') }}</label>
            <input type="text" class="input w-full" v-model="options.replacement" />

            <label class="label mt-4">{{ __('voyager::formfields.slug.lower') }}</label>
            <input type="checkbox" class="input" v-model="options.lower" />

            <label class="label mt-4">{{ __('voyager::formfields.slug.strict') }}</label>
            <input type="checkbox" class="input" v-model="options.strict" />
        </div>
        <div v-else-if="show == 'view'">
            <input
                type="text"
                class="input w-full"
                v-bind:value="translate(options.default_value)"
                :placeholder="translate(options.placeholder)">
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>