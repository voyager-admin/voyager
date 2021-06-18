<template>
    <div v-if="action == 'view-options'">
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::generic.max') }}</label>
            <input type="number" class="input w-full" v-model.number="options.max" />
        </div>
        <div class="input-group mt-2">
            <key-value-form v-model="options.meta" :valueText="__('voyager::generic.title')" />
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.media_picker.select_text') }}</label>
            <language-input
                class="input w-full"
                type="text"
                :placeholder="__('voyager::formfields.media_picker.select_text')"
                v-model="options.select_text"
            />
        </div>
    </div>
    <div v-if="action == 'list-options'">
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.media_picker.show_icons') }}</label>
            <input type="checkbox" class="input" v-model="options.icons">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.media_picker.display_items') }}</label>
            <input type="number" :max="50" class="input w-full" v-model.number="options.display" />
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.media_picker.shuffle_items') }}</label>
            <input type="checkbox" class="input" v-model="options.shuffle">
        </div>
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultListOptions() {
            return {
                icons: true,
                display: 3,
                shuffle: false
            };
        },
        defaultViewOptions() {
            return {
                max: 0,
                meta: [],
                select_text: '',
            };
        },
    }
}
</script>