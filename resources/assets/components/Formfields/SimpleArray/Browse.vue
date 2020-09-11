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
        <div class="inline-flex" v-for="(option, i) in displaySelected" :key="i">
            {{ getValueByKey(option) }}
            <span v-if="displaySelected.length !== i+1">,&nbsp;</span>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    data: function () {
        return {
            selected: [],
        };
    },
    computed: {
        displaySelected: function () {
            return this.selected.splice(0, 3);
        }
    },
    methods: {
        getValueByKey: function (key) {
            var vm = this;
            var value = null;

            vm.options.options.forEach(function (option) {
                if (option.key == key) {
                    value = vm.translate(option.value);
                }
            });

            return value || key;
        },
    },
    created: function () {
        this.$watch(
            () => this.value,
            function (value) {
                this.selected = value || [];
            }
        );
    },
};
</script>