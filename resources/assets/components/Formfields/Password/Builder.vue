<template>
    <div>
        <div v-if="show == 'view-options'">
            <label class="label mt-4">{{ __('voyager::generic.placeholder') }}</label>
            <language-input
                class="voyager-input w-full"
                type="text" :placeholder="__('voyager::generic.placeholder')"
                v-bind:value="options.placeholder"
                v-on:input="options.placeholder = $event" />
        </div>
        <div v-else-if="show == 'view'">
            <input
                type="password"
                class="voyager-input w-full"
                :placeholder="translate(options.placeholder)">
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>