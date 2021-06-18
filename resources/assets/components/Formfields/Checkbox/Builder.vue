<template>
    <div v-if="action == 'list-options' || action == 'view-options'">
        <h5 class="w-full mt-2">{{ __('voyager::generic.options') }}</h5>
        <key-value-form v-model="options.options" />
        <div v-if="action == 'view-options'" class="input-group mt-2">
            <label for="inline" class="label">{{ __('voyager::generic.inline') }}</label>
            <input
                type="checkbox"
                id="inline"
                class="input"
                v-model="options.inline"
            /> 
        </div>
    </div>
    <div v-else-if="action == 'view'" class="w-full mb-4" :class="options.inline ? 'space-x-1.5' : null">
        <template v-for="(option, i) in (options.options || [])" :key="i">
            <div class="inline-flex items-center space-x-1.5" :class="options.inline ? null : 'w-full'">
                <input type="checkbox" class="input" :value="option.key" />
                <label class="label">{{ translate(option.value) }}</label>
            </div>
        </template>
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                options: [],
                inline: false,
            };
        },
        defaultListOptions() {
            return {
                options: [],
            };
        },
    }
}
</script>