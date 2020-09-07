<template>
    <div v-if="show == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.minimum') }}</label>
        <plus-minus-input :min="1" class="input w-full" v-model="options.min" />
        <label class="label mt-4">{{ __('voyager::generic.maximum') }}</label>
        <plus-minus-input class="input w-full" v-model="options.max" />
        <label class="label mt-4">{{ __('voyager::generic.color') }}</label>
        <color-picker v-on:input="options.color = $event" v-bind:value="options.color"></color-picker>
        <label class="label mt-4">{{ __('voyager::formfields.tags.allow_reordering') }}</label>
        <input type="checkbox" v-model="options.reorder" class="input">
    </div>
    <div v-if="show == 'list-options'">
        <label class="label mt-4">{{ __('voyager::formfields.tags.display_amount') }}</label>
        <plus-minus-input class="input w-full" v-model="options.display" />
        <label class="label mt-4">{{ __('voyager::generic.color') }}</label>
        <color-picker v-on:input="options.color = $event" v-bind:value="options.color"></color-picker>
        <label class="label mt-4">{{ __('voyager::formfields.tags.allow_reordering') }}</label>
        <input type="checkbox" v-model="options.reorder" class="input">
    </div>
    <div v-else-if="show == 'view'">
        <tag-input
            :min="options.min"
            :max="options.max"
            :badge-color="options.color || 'blue'"
            :allow-reorder="options.reorder || true">
        </tag-input>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>