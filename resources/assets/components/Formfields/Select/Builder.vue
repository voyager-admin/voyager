<template>
    <div v-if="show == 'list-options' || show == 'view-options'">
        <label for="multiple" class="label">{{ __('voyager::generic.multiple') }}</label>
        <input type="checkbox" id="multiple" class="input" v-model="options.multiple">

        <key-value-form v-model="options.options"></key-value-form>
    </div>
    <div v-else-if="show == 'view'">
        <select class="input w-full" :multiple="options.multiple || false">
            <option v-for="option in options.options" :value="option.key" :key="option.key">
                {{ translate(option.value) }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>