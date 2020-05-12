<template>
    <div>
        <div v-if="show == 'view-options'">
            <label class="label mt-4">Minimum</label>
            <input type="number" min="1" class="voyager-input w-full" v-model="options.min">
            <label class="label mt-4">Maximum</label>
            <input type="number" min="0" class="voyager-input w-full" v-model="options.max">
            <label class="label mt-4">Color</label>
            <color-picker v-on:input="options.color = $event" v-bind:value="options.color" :describe="false"></color-picker>
            <label class="label mt-4">Allow reordering</label>
            <input type="checkbox" v-model="options.reorder" class="voyager-input">
        </div>
        <div v-if="show == 'list-options'">
            <label class="label mt-4">Amount of tags to display</label>
            <input type="number" min="0" class="voyager-input w-full" v-model="options.display">
            <label class="label mt-4">Color</label>
            <color-picker v-on:input="options.color = $event" v-bind:value="options.color" :describe="false"></color-picker>
            <label class="label mt-4">Allow reordering</label>
            <input type="checkbox" v-model="options.reorder" class="voyager-input">
        </div>
        <div v-else-if="show == 'view'">
            <tag-input
                :min="options.min"
                :max="options.max"
                :badge-color="options.color || 'blue'"
                :allow-reorder="options.reorder || true">
            </tag-input>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>