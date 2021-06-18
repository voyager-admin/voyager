<template>
    <template v-if="action == 'view-options'">
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.simple_array.item_description') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::formfields.simple_array.item_description')"
                v-model="options.item_description" /> 
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.simple_array.allow_reordering') }}</label>
            <input type="checkbox" class="input" v-model="options.reordering">
        </div>
    </template>
</template>

<script>
import formfieldBuilder from '@mixins/formfield-builder';

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