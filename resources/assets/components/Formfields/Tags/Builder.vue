<template>
    <div v-if="action == 'list-options'">
        <label class="label mt-4">{{ __('voyager::formfields.tags.display_amount') }}</label>
        <input type="number" :min="0" :max="10000" class="input w-full" v-model.number="options.display_amount">
    </div>
    <div v-else-if="action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.min') }}</label>
        <input type="number" :min="0" :max="1000" class="input w-full" v-model.number="options.min">

        <label class="label mt-4">{{ __('voyager::generic.max') }}</label>
        <input type="number" :min="1" :max="10000" class="input w-full" v-model.number="options.max">
        
        <label class="label mt-4">{{ __('voyager::formfields.tags.allow_reordering') }}</label>
        <input type="checkbox" class="input" v-model="options.reordering">

        <label class="label mt-4">{{ __('voyager::formfields.tags.allow_duplicates') }}</label>
        <input type="checkbox" class="input" v-model="options.duplicates">

        <label class="label mt-4">{{ __('voyager::formfields.tags.allow_empty') }}</label>
        <input type="checkbox" class="input" v-model="options.empty">

        <!-- TODO: Add badgeColor -->
    </div>
    <div v-else-if="action == 'view'">
        <tag-input
            v-model="dummyModel"
            :min="options.min"
            :max="options.max"
            :reordering="options.reordering"
            :duplicates="options.duplicates"
            :empty="options.empty"
        />
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultListOptions() {
            return {
                display_amount: 0,
            };
        },
        defaultViewOptions() {
            return {
                min: 0,
                max: 0,
                reordering: true,
                duplicates: false,
                empty: false,
            };
        },
    },
    data() {
        return {
            dummyModel: [],
        };
    }
}
</script>