<template>
    <div v-if="show == 'query'">
        <select class="input small w-full" @change="$emit('input', $event.target.value)" v-bind:value="value">
            <option :value="null">{{ __('voyager::generic.none') }}</option>
            <option v-for="option in options.options" :value="option.key" :key="option.key">
                {{ translate(option.value) }}
            </option>
        </select>
    </div>
    <div v-else>
        {{ optionValue }}
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    computed: {
        optionValue: function () {
            var vm = this;
            var option = vm.options.options.where('key', vm.value);

            if (option.length > 0) {
                return vm.translate(option[0].value);
            }

            return '';
        },
    },
};
</script>