<template>
    <div>
        <input
            type="text"
            class="voyager-input w-full"
            v-bind:value="readableValue"
            @click="dropdownOpen = !dropdownOpen">
    </div>
</template>
<script>
var dayjs = require('dayjs');

export default {
    props: {
        value: {
            default: undefined
        },
        format: {
            type: String,
            default: 'DD.MM.YYYY',
        },
        type: {
            type: String,
            default: 'date',
            validator: function (value) {
                return ['date', 'time', 'datetime'].indexOf(value) !== -1;
            }
        },
        now: {
            type: Boolean,
            default: true,
        },
        defaultText: {
            type: String,
            default: 'Pick a date',
        }
    },
    computed: {
        readableValue: function () {
            if (this.value === undefined && !this.now) {
                return this.defaultText;
            }

            return this.date.format(this.format);
        }
    },
    data: function () {
        return {
            date: dayjs(this.value),
            dropdownOpen: false,
        };
    },
    watch: {
        value: function (val) {
            this.date = dayjs(val);
        }
    }
};
</script>

<style lang="scss" scoped>

</style>