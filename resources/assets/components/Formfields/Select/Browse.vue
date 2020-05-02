<template>
    <div>
        <div v-if="show == 'query'">
            <select class="voyager-input small w-full" @change="$emit('input', $event.target.value)">
                <option :value="null">{{ __('voyager::generic.none') }}</option>
                <option v-for="option in options.options" :value="option.key" :key="option.key">
                    {{ translate(option.value, true) }}
                </option>
            </select>
        </div>
        <div v-else>
            <div class="inline-flex" v-for="(option, i) in displaySelected" :key="i">
                {{ getValueByKey(option) }}
                <span v-if="displaySelected.length !== i+1">,&nbsp;</span>
            </div>
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
        parseInput: function () {
            if ((this.options.multiple || false)) {
                if (this.isString(this.value)) {
                    try {
                        this.selected = JSON.parse(this.value);
                    } catch (e) {
                        this.selected = [];
                    }
                } else {
                    this.selected = this.value;
                }
            } else if (this.value !== '') {
                this.selected = [this.value];
            }
        },
        getValueByKey: function (key) {
            var vm = this;
            var value = null;

            vm.options.options.forEach(function (option) {
                if (option.key == key) {
                    value = vm.translate(option.value, true);
                }
            });

            return value || key;
        },
    },
    watch: {
        value: function (value) {
            this.parseInput();
        }
    },
    mounted: function () {
        this.parseInput();
    }
};
</script>