<template>
    <div v-if="action == 'view-options'">
        <label for="route" class="label">Route name</label>
        <input type="text" class="input w-full" id="route" v-model="options.route_name">

        <label for="debounce" class="label">Debounce time</label>
        <input type="number" class="input w-full" id="debounce" min="0" max="2500" v-model="options.debounce">

        <label for="inline" class="label">Inline</label>
        <input type="checkbox" class="input w-full" id="inline" v-model="options.inline">

        <label for="outline" class="label">Outline</label>
        <input type="checkbox" class="input w-full" id="outline" v-model="options.outline">
    </div>
    <template v-else-if="action == 'view'">
        <formfield-dynamic-select
            action="add"
            :options="options"
            :column="column"
            :modelValue="mockValue"
            @update:modelValue="mockValue = $event"
        />
    </template>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                route_name: '',
                debounce: 200,
                inline: true,
                outline: true,
            };
        },
    },
    data() {
        return {
            mockValue: null,
        };
    }
}
</script>