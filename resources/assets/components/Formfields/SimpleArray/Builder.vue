<template>
    <template v-if="action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::formfields.simple_array.item_description') }}</label>
        <language-input
            class="input w-full"
            type="text" :placeholder="__('voyager::formfields.simple_array.item_description')"
            v-model="options.item_description" /> 

        <label class="label mt-4">{{ __('voyager::formfields.simple_array.allow_reordering') }}</label>
        <input type="checkbox" class="input" v-model="options.reordering">
    </template>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                item_description: null,
                reordering: true,
            }
        }
    }
}
</script>