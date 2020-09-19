<template>
    <div v-if="action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.max') }}</label>
        <input type="number" class="input w-full" v-model.number="options.max" />
        <!-- Meta -->
        <key-value-form v-model="options.meta" :valueText="__('voyager::generic.title')" />
        <!-- Mimes -->

        <label class="label mt-4">{{ __('voyager::formfields.media_picker.select_text') }}</label>
        <language-input
            class="input w-full"
            type="text"
            :placeholder="__('voyager::formfields.media_picker.select_text')"
            v-model="options.select_text"
        /> 
    </div>
    <div v-if="action == 'list-options'">
        <label class="label mt-4">{{ __('voyager::formfields.media_picker.show_icons') }}</label>
        <input type="checkbox" class="input" v-model="options.icons">

        <label class="label mt-4">{{ __('voyager::formfields.media_picker.display_items') }}</label>
        <input type="number" :max="50" class="input w-full" v-model.number="options.display" />

        <label class="label mt-4">{{ __('voyager::formfields.media_picker.shuffle_items') }}</label>
        <input type="checkbox" class="input" v-model="options.shuffle">
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultListOptions: function () {
            return {
                icons: true,
                display: 3,
                shuffle: false
            };
        },
        defaultViewOptions: function () {
            return {
                max: 0,
                meta: [],
                select_text: '',
            };
        },
    }
}
</script>