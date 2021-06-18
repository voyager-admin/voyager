<template>
    <div v-if="action == 'list-options'">
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.tags.display_amount') }}</label>
            <input type="number" :min="0" :max="10000" class="input w-full" v-model.number="options.display_amount">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::generic.color') }}</label>
            <color-picker v-model="options.color" />
        </div>
    </div>
    <div v-else-if="action == 'view-options'">
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::generic.min') }}</label>
            <input type="number" :min="0" :max="1000" class="input w-full" v-model.number="options.min">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::generic.max') }}</label>
            <input type="number" :min="1" :max="10000" class="input w-full" v-model.number="options.max">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.tags.allow_reordering') }}</label>
            <input type="checkbox" class="input" v-model="options.reordering">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.tags.allow_duplicates') }}</label>
            <input type="checkbox" class="input" v-model="options.duplicates">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::formfields.tags.allow_empty') }}</label>
            <input type="checkbox" class="input" v-model="options.empty">
        </div>
        <div class="input-group mt-2">
            <label class="label">{{ __('voyager::generic.color') }}</label>
            <color-picker v-model="options.color" />
        </div>
    </div>
    <div v-else-if="action == 'view'">
        <tag-input
            v-model="dummyModel"
            :min="options.min"
            :max="options.max"
            :reordering="options.reordering"
            :duplicates="options.duplicates"
            :empty="options.empty"
            :badgeColor="options.color"
            :class="options.classes"
        />
    </div>
</template>

<script>
import formfieldBuilder from '@mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultListOptions() {
            return {
                display_amount: 0,
                color: 'accent',
            };
        },
        defaultViewOptions() {
            return {
                min: 0,
                max: 0,
                reordering: true,
                duplicates: false,
                empty: false,
                color: 'accent',
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