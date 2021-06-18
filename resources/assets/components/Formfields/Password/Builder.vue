<template>
    <div v-if="action == 'view-options'">
        <div class="input-group mt-2">
            <label class="label mt-4">{{ __('voyager::generic.placeholder') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::generic.placeholder')"
                v-model="options.placeholder"
            />
        </div>
    </div>
    <div v-else-if="action == 'view'">
        <input
            type="password"
            class="input w-full"
            :placeholder="translate(options.placeholder)"
        >
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                placeholder: '',
            };
        },
    }
}
</script>