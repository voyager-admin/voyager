<template>
    <div>
        <div v-if="show == 'list-options'">
            <label for="multiple" class="label">{{ __('voyager::generic.multiple') }}</label>
            <input type="checkbox" id="multiple" class="voyager-input" v-model="options.multiple">

            <div class="w-full flex">
                <div class="w-4/6">
                    <h5>{{ __('voyager::generic.options') }}</h5>
                </div>
                <div class="w-2/6 text-right">
                    <button class="button green small icon-only" @click.stop="addOption">
                        <icon icon="plus" />
                    </button>
                </div>
            </div>
            <div class="voyager-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('voyager::generic.key') }}</th>
                            <th>{{ __('voyager::generic.value') }}</th>
                            <th>{{ __('voyager::generic.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(option, i) in options.options" :key="'option-'+i">
                            <td>
                                <input type="text" class="voyager-input w-full" v-model="option.key" placeholder="Key">
                            </td>
                            <td>
                                <language-input
                                    class="voyager-input w-full"
                                    type="text" placeholder="Value"
                                    v-bind:value="option.value"
                                    v-on:input="option.value = $event" />
                            </td>
                            <td>
                                <button class="button red small icon-only" @click.stop="removeOption(key)">
                                    <icon icon="trash" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else-if="show == 'view-options'">
            <label for="multiple" class="label">{{ __('voyager::generic.multiple') }}</label>
            <input type="checkbox" id="multiple" class="voyager-input" v-model="options.multiple">

            <div class="w-full flex">
                <div class="w-4/6">
                    <h5>{{ __('voyager::generic.options') }}</h5>
                </div>
                <div class="w-2/6 text-right">
                    <button class="button green small icon-only" @click.stop="addOption">
                        <icon icon="plus" />
                    </button>
                </div>
            </div>
            <div class="voyager-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('voyager::generic.key') }}</th>
                            <th>{{ __('voyager::generic.value') }}</th>
                            <th>{{ __('voyager::generic.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(option, i) in options.options" :key="'option-'+i">
                            <td>
                                <input type="text" class="voyager-input w-full" v-model="option.key" placeholder="Key">
                            </td>
                            <td>
                                <language-input
                                    class="voyager-input w-full"
                                    type="text" placeholder="Value"
                                    v-bind:value="option.value"
                                    v-on:input="option.value = $event" />
                            </td>
                            <td>
                                <button class="button red small icon-only" @click.stop="removeOption(key)">
                                    <icon icon="trash" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else-if="show == 'view'">
            <select class="voyager-input w-full" :multiple="options.multiple || false">
                <option v-for="option in options.options" :value="option.key" :key="option.key">
                    {{ translate(option.value) }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
    methods: {
        addOption: function () {
            if (!this.isArray(this.options.options)) {
                this.options.options = [];
            }
            this.options.options.push({
                key: '',
                value: '',
            });
        },
        removeOption: function (key) {
            this.options.options.splice(key, 1);
        }
    }
};
</script>