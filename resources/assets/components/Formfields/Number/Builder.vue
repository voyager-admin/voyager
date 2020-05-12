<template>
    <div>
        <div v-if="show == 'list-options'">
            <label class="label mt-4">Decimals</label>
            <input
                class="voyager-input w-full"
                type="number" placeholder="Decimals"
                v-model.number="options.decimals" />

            <label class="label mt-4">Decimal separator</label>
            <input
                class="voyager-input w-full"
                type="text" placeholder="Decimal separator"
                v-model="options.dec_point" />

            <label class="label mt-4">Thousands separator</label>
            <input
                class="voyager-input w-full"
                type="text" placeholder="Thousands separator"
                v-model="options.thousands_sep" />
        </div>
        <div v-else-if="show == 'view-options'">
            <label class="label mt-4">Placeholder</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Placeholder"
                v-bind:value="options.placeholder"
                v-on:input="options.placeholder = $event" /> 

            <label class="label mt-4">Minimum</label>
            <input
                class="voyager-input w-full"
                type="number" placeholder="Minimum"
                v-model.number="options.min" />

            <label class="label mt-4">Maximum</label>
            <input
                class="voyager-input w-full"
                type="number" placeholder="Maximum"
                v-model.number="options.max" />

            <label class="label mt-4">Step</label>
            <input
                class="voyager-input w-full"
                type="number" placeholder="Step"
                v-model.number="options.step" />
        </div>
        <div v-else-if="show == 'view'">
            <input
                type="text"
                class="voyager-input w-full"
                :min="options.min"
                :max="options.max"
                :step="options.step"
                :placeholder="translate(options.placeholder)">
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>