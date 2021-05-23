<template>
    <div v-if="action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.min') }}</label>
        <input type="number" class="input w-full" v-model.number="options.min">

        <label class="label mt-4">{{ __('voyager::generic.max') }}</label>
        <input type="number" class="input w-full" v-model.number="options.max">

        <label class="label mt-4">{{ __('voyager::generic.step') }}</label>
        <input type="number" class="input w-full" v-model.number="options.step">

        <label class="label mt-4">{{ __('voyager::formfields.slider.range') }}</label>
        <input type="checkbox" class="input w-full" v-model="options.range">

        <label class="label mt-4">{{ __('voyager::formfields.slider.distance') }}</label>
        <input type="number" class="input w-full" v-model.number="options.distance" :disabled="!options.range">

        <label class="label mt-4">{{ __('voyager::formfields.slider.show_inputs') }}</label>
        <input type="checkbox" class="input w-full" v-model="options.inputs">

        <label class="label mt-4">{{ __('voyager::generic.color') }}</label>
        <color-picker v-model="options.color"></color-picker>
    </div>
    <div v-else-if="action == 'view'">
        <slider
            class="my-2"
            :inputs="options.inputs"
            :range="options.range"
            :min="options.min"
            :max="options.max"
            :step="options.step"
            :distance="options.distance"
            :color="options.color"
            v-model:lower="lowerModel"
            v-model:upper="upperModel"
        />
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultListOptions() {
            return {};
        },
        defaultViewOptions() {
            return {
                min: 1,
                max: 100,
                step: 1,
                range: true,
                distance: 0,
                inputs: true,
                color: 'accent',
            };
        },
    },
    data() {
        return {
            lowerModel: this.options.min,
            upperModel: this.options.max,
        };
    },
    mounted() {
        this.$watch(() => this.options.min, (min) => {
            this.lowerModel = min;
        });
        this.$watch(() => this.options.max, (max) => {
            this.upperModel = max;
        });
    }
}
</script>