<template>
    <div>
        <dropdown pos="right" width="auto" class="w-full" ref="dropdown" open-on-click>
            <div class="m-4 select-none">
                <div class="flex">
                    <div class="flex-none self-center">
                        <icon icon="chevron-left" class="cursor-pointer" @click="previousMonth" />
                    </div>
                    <div class="inline-flex flex-grow justify-center space-x-4">
                        <select v-model="current.month" class="input small">
                            <option v-for="month in 12" :key="month" :value="month - 1">
                                {{ months[month - 1] }}
                            </option>
                        </select>
                        <select v-model="current.year" class="input small">
                            <option v-for="year in displayYears" :key="year">
                                {{ year }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-none self-center">
                        <icon icon="chevron-right" class="cursor-pointer" @click="nextMonth" />
                    </div>
                </div>
                <div was="slide-y-up-transition" class="mt-2 grid grid-cols-7 gap-2" group>
                    <div v-for="weekday in weekdays" :key="weekday" class="text-center">
                        {{ weekday }}
                    </div>
                    <div
                        v-for="(day, i) in displayDays"
                        :key="i"
                        class="day"
                        v-bind:class="getDayClasses(day)"
                        @click="selectDate(day.date, day.month, day.year)"
                        @contextmenu.prevent.stop="selectDate(day.date, day.month, day.year, true)"
                    >
                        {{ day.date }}
                    </div>
                </div>
                <div v-if="selectTime">
                    <div class="w-full mt-2 inline-flex space-x-1 items-center">
                        <span class="w-12">{{ range ? 'From:' : 'Time:' }}</span>
                        <select class="input small" v-model="selected.hour">
                            <option v-for="hour in 24" :key="hour" :value="hour - 1">{{ displayHour(hour) }}</option>
                        </select>
                        <select class="input small" v-model="selected.minute">
                            <option v-for="minute in 60" :key="minute" :value="minute - 1">{{ displayTime(minute) }}</option>
                        </select>
                        <select class="input small" v-if="selectSeconds" v-model="selected.second">
                            <option v-for="second in 60" :key="second" :value="second - 1">{{ displayTime(second) }}</option>
                        </select>
                    </div>
                    <div v-if="range" class="w-full mt-2 inline-flex space-x-1 items-center">
                        <span class="w-12">To:</span>
                        <select class="input small" v-model="selectedEnd.hour">
                            <option v-for="hour in 24" :key="hour" :value="hour - 1">{{ displayHour(hour) }}</option>
                        </select>
                        <select class="input small" v-model="selectedEnd.minute">
                            <option v-for="minute in 60" :key="minute" :value="minute - 1">{{ displayTime(minute) }}</option>
                        </select>
                        <select class="input small" v-if="selectSeconds" v-model="selectedEnd.second">
                            <option v-for="second in 60" :key="second" :value="second - 1">{{ displayTime(second) }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <template v-slot:opener>
                <input
                    type="text"
                    class="input w-full"
                    v-bind:value="inputDate"
                    v-on:input="setInputDate"
                >
            </template>
        </dropdown>
    </div>
</template>

<script>
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';
dayjs.extend(customParseFormat);

import utc from 'dayjs/plugin/utc';
dayjs.extend(utc);

export default {
    props: {
        value: {
            type: [String, Array, null],
            default: null,
        },
        format: {
            type: String,
            default: 'YYYY-MM-DD',
        },
        displayFormat: {
            type: [String, null],
        },
        range: {
            type: Boolean,
            default: false,
        },
        delimiter: {
            type: String,
            default: '-',
        },
        timezoneAware: {
            type: Boolean,
            default: true,
        },
        weekdays: {
            type: Array,
            default: () => ([
                'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
            ])
        },
        months: {
            type: Array,
            default: () => ([
                'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
            ])
        },
        selectTime: {
            type: Boolean,
            default: false,
        },
        padTime: {
            type: Boolean,
            default: true,
        },
        amPm: {
            type: Boolean,
            default: false,
        },
        selectSeconds: {
            type: Boolean,
            default: false,
        }
    },
    data: function () {
        return {
            // The currently shown month/year
            current: {
                year: 0,
                month: 0,
            },
            selected: {
                year: 0,
                month: 0,
                date: 0,
                hour: 0,
                minute: 0,
                second: 0,
            },
            selectedEnd: {
                year: 0,
                month: 0,
                date: 0,
                hour: 0,
                minute: 0,
                second: 0,
            },
        };
    },
    computed: {
        displayDays: function () {
            var days = [];

            var today = new Date();
            var start = this.getObjectAsDate(this.selected);
            var end = this.getObjectAsDate(this.selectedEnd);

            var last_month = new Date(this.current.year, this.current.month, 0).getDay();
            var current_month = new Date(this.current.year, (this.current.month - 1), 0).getDate();
            var mod = (last_month + current_month) % 7;
            var next_month = 7 - mod;
            
            var days_length = last_month + current_month + next_month;

            var date = new Date(this.current.year, this.current.month, -last_month + 1);
            for (var i = 0; i < days_length; i++) {
                days.push({
                    date: date.getDate(),
                    month: date.getMonth(),
                    year: date.getFullYear(),
                    past: this.isPast(date.getFullYear(), date.getMonth()),
                    present: date.getMonth() == this.current.month,
                    future: this.isFuture(date.getFullYear(), date.getMonth()),
                    today: this.dateEquals(date, today),
                    start: this.dateEquals(date, start),
                    end: this.dateEquals(date, end),
                    range: this.range && this.dateBeween(date, start, end),
                    selected: !this.range && this.dateEquals(date, start),
                });
                date.setDate(date.getDate() + 1);
            }
            return days;
        },
        displayYears: function () {
            var current = new Date().getFullYear();
            var years = [];
            for (var year = (current - 10); year <= (current + 10); year++) {
                years.push(year);
            }

            return years;
        },
        displayEndTime: function () {
            var date = this.getObjectAsDate(this.selectedEnd, true);

            return dayjs(date).format(this.displayTimeFormat);
        },
        displayTimeFormat: function () {
            if (this.amPm) {
                if (this.selectSeconds) {
                    return 'hh:mm:ss A';
                }
                return 'hh:mm A';
            } else if (this.selectSeconds) {
                return 'HH:mm:ss';
            }

            return 'HH:mm';
        },
        inputDate: function () {
            if (this.range) {
                return dayjs(this.getObjectAsDate(this.selected, true)).format(this.displayFormat || this.format) + 
                        ' ' + this.delimiter + ' ' + 
                        dayjs(this.getObjectAsDate(this.selectedEnd, true)).format(this.displayFormat || this.format);
            }

            return dayjs(this.getObjectAsDate(this.selected, true)).format(this.displayFormat || this.format);
        },
    },
    methods: {
        selectDate: function (day, month, year, asStart = false) {
            if (this.range) {
                var selected = new Date(year, month, day).valueOf();
                var start = this.getObjectAsDate(this.selected).valueOf();
                var end = this.getObjectAsDate(this.selectedEnd);
                if (start > selected || (asStart && selected <= end)) {
                    this.selected.year = year;
                    this.selected.month = month;
                    this.selected.date = day;
                } else {
                    this.selectedEnd.year = year;
                    this.selectedEnd.month = month;
                    this.selectedEnd.date = day;
                }
                this.current.year = year;
                this.current.month = month;
            } else {
                this.selected.year = year;
                this.selected.month = month;
                this.selected.date = day;
                this.current.year = year;
                this.current.month = month;
            }
        },
        setInputDate: function (e) {
            if (this.range) {
                var parts = e.target.value.split(' '+this.delimiter+' ');
                if (parts.length == 2) {
                    if (dayjs(parts[0], this.format).isValid() && dayjs(parts[1], this.format).isValid()) {
                        // TODO: We need to validate if start <= end and if years are in bounds
                        this.selected = this.getStringAsObject(parts[0]);
                        this.selectedEnd = this.getStringAsObject(parts[1]);
                        this.current.year = this.selected.year;
                        this.current.month = this.selected.month;
                    }
                }
            } else {
                if (dayjs(e.target.value, this.format).isValid()) {
                    this.selected = this.getStringAsObject(e.target.value);
                    this.current.year = this.selected.year;
                    this.current.month = this.selected.month;
                }
            }
        },
        nextMonth: function () {
            if (this.current.month >= 11) {
                this.current.month = 0;
                this.current.year++;
            } else {
                this.current.month++;
            }
        },
        previousMonth: function () {
            if (this.current.month <= 0) {
                this.current.month = 11;
                this.current.year--;
            } else {
                this.current.month--;
            }
        },
        parseValue: function () {
            if (this.value === null) {
                this.selected = this.getStringAsObject();
                this.selectedEnd = this.getStringAsObject();
            } else if (this.isArray(this.value)) {
                if (this.value.length == 0) {
                    this.selected = this.getStringAsObject();
                    this.selectedEnd = this.getStringAsObject();
                } else if (this.value.length == 1) {
                    this.selected = this.getStringAsObject(this.value[0]);
                    this.selectedEnd = this.getStringAsObject(this.value[0]);
                } else {
                    this.selected = this.getStringAsObject(this.value[0]);
                    this.selectedEnd = this.getStringAsObject(this.value[1]);
                }
            } else {
                this.selected = this.getStringAsObject(this.value);
                this.selectedEnd = this.getStringAsObject(this.value);
            }

            this.current.year = this.selected.year;
            this.current.month = this.selected.month;
        },
        getStringAsObject: function (value = null) {
            var date = dayjs();
            if (value !== null) {
                date = dayjs(value, this.format);
            }

            return {
                year: date.year(),
                month: date.month(),
                date: date.date(),
                hour: date.hour(),
                minute: date.minute(),
                second: date.second(),
            };
        },
        getObjectAsDate: function (obj, withTime = false) {
            if (withTime) {
                return new Date(
                    obj.year,
                    obj.month,
                    obj.date,
                    obj.hour,
                    obj.minute,
                    obj.second
                );
            }

            return new Date(
                obj.year,
                obj.month,
                obj.date
            );
        },
        getDayClasses: function (day) {
            return {
                today: day.today,
                past: day.past,
                future: day.future,
                disabled: day.disabled,
                rangeBetween: day.range,
                rangeStart: day.start,
                rangeEnd: day.end,
                selected: day.selected,
            };
        },
        displayHour: function (hour) {
            hour--;
            var string = String(hour);
            if (this.amPm) {
                if (hour == 0) {
                    string = '12 AM';
                } else if (hour <= 11) {
                    string += ' AM';
                } else if (hour == 12) {
                    string = '12 PM';
                } else {
                    string = (hour - 12) + ' PM';
                }
            }

            if (this.padTime) {
                if (!this.amPm && hour <= 9) {
                    return '0' + string;
                } else if (this.amPm && ((hour >= 1 && hour <= 9) || (hour >= 13 && hour <= 21))) {
                    return '0' + string;
                }
            }

            return string;
        },
        displayTime: function (value) {
            value = String(value--);
            if (this.padTime && value.length == 1) {
                return '0' + value;
            }

            return value;
        },
        isPast: function (year, month) {
            if (month == 11 && this.current.month == 0) {
                return true;
            } else if (month == 0 && this.current.month == 11) {
                return false;
            }

            return month < this.current.month;
        },
        isFuture: function (year, month) {
            if (month == 0 && this.current.month == 11) {
                return true;
            } else if (month == 11 && this.current.month == 0) {
                return false;
            }

            return month > this.current.month;
        },
        dateEquals: function (date1, date2) {
            return  date1.getFullYear() == date2.getFullYear() &&
                    date1.getMonth() == date2.getMonth() &&
                    date1.getDate() == date2.getDate();
        },
        dateBeween: function (date, start, end) {
            return  date.valueOf() > start.valueOf() && date.valueOf() < end.valueOf();
        },
    },
    watch: {
        selected: {
            deep: true,
            handler: function (value) {
                var start = this.getObjectAsDate(this.selected, true);
                var end = this.getObjectAsDate(this.selectedEnd, true);

                if (this.range) {
                    if (this.timezoneAware) {
                        this.$emit('input', [
                            dayjs.utc(start).format(this.format),
                            dayjs.utc(end).format(this.format)
                        ]);
                    } else {
                        this.$emit('input', [
                            dayjs(start).format(this.format),
                            dayjs(end).format(this.format)
                        ]);
                    }
                } else {
                    if (this.timezoneAware) {
                        this.$emit('input', dayjs.utc(start).format(this.format));
                    } else {
                        this.$emit('input', dayjs(start).format(this.format));
                    }
                }
            }
        },
        selectedEnd: {
            deep: true,
            handler: function (value) {
                if (this.range) {
                    var start = this.getObjectAsDate(this.selected, true);
                    var end = this.getObjectAsDate(this.selectedEnd, true);

                    if (this.timezoneAware) {
                        this.$emit('input', [
                            dayjs.utc(start).format(this.format),
                            dayjs.utc(end).format(this.format)
                        ]);
                    } else {
                        this.$emit('input', [
                            dayjs(start).format(this.format),
                            dayjs(end).format(this.format)
                        ]);
                    }
                }
            }
        },
        value: function () {
            this.parseValue();
        }
    },
    mounted: function () {
        this.parseValue();
    }
}
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/bg-color";

.dark .day {
    &:hover {
        @apply bg-blue-700;
    }
    &.today {
        @apply bg-blue-600;
    }
    &.past, &.future {
        @apply text-gray-500;
    }
    &.disabled {
        @apply text-gray-400;
    }
    &.selected {
        @apply bg-blue-800;
    }
    &.rangeStart, &.rangeEnd, &.rangeBetween {
        @apply bg-blue-900;
    }
}

.day {
    @apply text-center rounded-md px-2 py-1 cursor-pointer;

    &:hover {
        @apply bg-blue-300;
    }
    &.today {
        @apply bg-blue-200;
    }
    &.past, &.future {
        @apply text-gray-400;
    }
    &.disabled {
        @apply text-gray-300 cursor-not-allowed;
    }
    &.selected {
        @apply bg-blue-500 text-white;
    }
    &.rangeStart, &.rangeEnd, &.rangeBetween {
        @apply bg-blue-300;
    }
}
</style>