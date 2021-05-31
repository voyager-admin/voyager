<template>
    <div v-if="action == 'list-options'">
        <label for="length" class="label">{{ __('voyager::generic.display_length') }}</label>
        <input
            type="number"
            id="length"
            class="input w-full"
            v-model.number="options.display_length" /> 
    </div>
    <div v-else-if="action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.placeholder') }}</label>
        <language-input
            class="input w-full"
            type="text" :placeholder="__('voyager::generic.placeholder')"
            v-model="options.placeholder" />

        <label for="column" class="label">{{ __('voyager::generic.column') }}</label>
        <select class="w-full input" id="column" :value="options.column" v-model="options.column">
            <option v-for="(column, i) in columns" :key="i" :value="column">{{ column }}</option>
        </select>
        <label for="strict" class="label">Strict</label>
        <input
            type="checkbox"
            id="strict"
            class="input"
            v-model="options.strict"
        />
        <label for="lower" class="label">Lower</label>
        <input
            type="checkbox"
            id="lower"
            class="input"
            v-model="options.lower"
        />
        <label for="replacement" class="label">Replacement</label>
        <input
            type="text"
            id="replacement"
            class="input w-full"
            v-model="options.replacement"
        />
    </div>
    <div v-else-if="action == 'view'">
        <input
            type="text"
            class="input w-full"
            :class="options.classes"
            :placeholder="translate(options.placeholder)">
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultListOptions() {
            return {
                display_length: 150,
            };
        },
        defaultViewOptions() {
            return {
                placeholder: '',
                column: {},
                strict: true,
                lower: true,
                replacement: '-'
            };
        },
    }
}
</script>