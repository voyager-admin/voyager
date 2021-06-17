<template>
    <component :is="inline ? 'div' : 'dropdown'" dont-close-on-inside-click  :placement="placement">
        <div class="inline-flex space-x-1 pt-4 px-4">
            <date-time v-model="dateFrom" v-bind="$props" :date-possible-callback="isDateFromPossible" :range-value="dayjs(dateTo)" inline />
            <div class="w-5 self-center">
                <icon icon="chevron-double-right" />
            </div>
            <date-time v-model="dateTo" v-bind="$props" :date-possible-callback="isDateToPossible" :range-value="dayjs(dateFrom)" inline />
        </div>

        <template #opener>
            <div class="inline-flex w-full space-x-1">
                <input type="text" class="input w-full" :class="{ error: textModelFromInvalid }" v-model="textModelFrom">
                <input type="text" class="input w-full" :class="{ error: textModelToInvalid }" v-model="textModelTo">
            </div>
        </template>
    </component>
</template>

<script>
import { placements } from '@popperjs/core/lib/enums';
import { FORMATS, dayjs } from './DateTime.vue';

export default {
    emits: ['update:from', 'update:to'],
    props: {
        from: {
            type: [String, null],
            default: null
        },
        to: {
            type: [String, null],
            default: null
        },
        past: {
            type: [String, Number, null],
            default: null,
        },
        future: {
            type: [String, Number, null],
            default: null,
        },
        type: {
            type: String,
            default: 'date',
            validator: (value) => Object.keys(FORMATS).includes(value),
        },
        inline: {
            type: Boolean,
            default: false,
        },
        displayFormat: {
            type: String,
            default: 'YYYY-MM-DD',
        },
        dayNames: {
            type: Array,
            default: () => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            validator: (value) => value.length == 7,
        },
        monthNames: {
            type: Array,
            default: () => ['January', 'February', 'March', 'April', 'May', 'June', 'Juli', 'August', 'September', 'October', 'November', 'December'],
            validator: (value) => value.length == 12,
        },
        sundayFirst: {
            type: Boolean,
            default: false,
        },
        distance: {
            type: Number,
            default: 0,
        },
        placement: {
            type: String,
            default: 'bottom',
            validator: (value) => {
                return placements.includes(value);
            }
        },
    },
    computed: {
        dateFrom: {
            get() {
                if (dayjs(this.from).isValid()) {
                    return this.from;
                }

                return null; // Now
            },
            set(value) {
                if (dayjs(value).isAfter(dayjs(this.dateTo), 'day')) {
                    // Swap from <=> to
                    this.$emit('update:from', this.to);
                    this.$emit('update:to', value);
                } else {
                    this.$emit('update:from', value);
                }
            }
        },
        dateTo: {
            get() {
                if (dayjs(this.to).isValid()) {
                    return this.to;
                }

                return null; // Now
            },
            set(value) {
                if (dayjs(value).isBefore(dayjs(this.fromDate), 'day')) {
                    // Swap from <=> to
                    this.$emit('update:to', this.from);
                    this.$emit('update:from', value);
                } else {
                    this.$emit('update:to', value);
                }
            }
        },
        textModelFrom: {
            get() {
                return dayjs(this.dateFrom).format(this.displayFormat);
            },
            set(value) {
                if (dayjs(value, this.displayFormat, true).isValid()) {
                    this.textModelFromInvalid = false;
                    this.dateFrom = value;
                } else {
                    this.textModelFromInvalid = true;
                }
            }
        },
        textModelTo: {
            get() {
                return dayjs(this.dateTo).format(this.displayFormat);
            },
            set(value) {
                if (dayjs(value, this.displayFormat, true).isValid()) {
                    this.textModelToInvalid = false;
                    this.dateTo = value;
                } else {
                    this.textModelToInvalid = true;
                }
            }
        }
    },
    data() {
        return {
            textModelFromInvalid: false,
            textModelToInvalid: false,
        };
    },
    methods: {
        isDateFromPossible(date) {
            return !date.isBetween(dayjs(this.dateTo).subtract(this.distance, 'day'), dayjs(this.dateTo), 'day', '(]');
        },
        isDateToPossible(date) {
            return !date.isBetween(dayjs(this.dateFrom), dayjs(this.dateFrom).add(this.distance, 'day'), 'day', '[)');
        },
        sanitizeDate(date) {
            if (this.type.includes('year')) {
                date = date.startOf('year');
            } else if (this.type.includes('month')) {
                date = date.startOf('month');
            } else if (!this.type.includes('time')) {
                date = date.startOf('day');
            } else if (!this.type.includes('seconds')) {
                date = date.startOf('minute');
            }

            return date.startOf('second');
        },
        dayjs,
    },
    mounted() {
        this.dateFrom = this.sanitizeDate(this.dateFrom);
        this.dateTo = this.sanitizeDate(this.dateTo);
    }
};
</script>
