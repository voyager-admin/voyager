<template>
    <div v-if="show == 'list-options'">
        <label class="label mt-4">{{ __('voyager::generic.display') }}</label>
        <plus-minus-input class="input w-full" v-model.number="options.display" />
    </div>
    <div v-else-if="show == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.max') }}</label>
        <plus-minus-input class="input w-full" v-model.number="options.max" />

        <label class="label mt-4">{{ __('voyager::formfields.simple_array.item_text') }}</label>
        <language-input class="input w-full" v-model="options.item_text" />
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>