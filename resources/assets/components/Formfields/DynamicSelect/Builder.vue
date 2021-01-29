<template>
    <div v-if="action == 'view-options'">
        <label for="route" class="label">Route name</label>
        <input type="text" class="input w-full" id="route" v-model="options.route_name">
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    computed: {
        defaultViewOptions() {
            return {
                route_name: '',
            };
        },
    }
}
</script>