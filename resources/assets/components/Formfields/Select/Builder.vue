<template>
    <div>
        <div v-if="show == 'list-options'">
            <label for="multiple" class="label">Multiple</label>
            <input type="checkbox" id="multiple" class="voyager-input" v-model="options.multiple">

            <div class="w-full flex">
                <div class="w-4/6">
                    <h5>Options</h5>
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
                            <th>Key</th>
                            <th>Value</th>
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
            <label class="label mt-4">Label</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Label"
                v-bind:value="options.label"
                v-on:input="options.label = $event" />

            <label class="label mt-4">Description</label>
            <language-input
                class="voyager-input w-full"
                type="text" placeholder="Description"
                v-bind:value="options.description"
                v-on:input="options.description = $event" />

            <label for="multiple" class="label">Multiple</label>
            <input type="checkbox" id="multiple" class="voyager-input" v-model="options.multiple">

            <div class="w-full flex">
                <div class="w-4/6">
                    <h5>Options</h5>
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
                            <th>Key</th>
                            <th>Value</th>
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
            <label class="label" v-if="translate(options.label) !== ''">{{ translate(options.label) }}</label>
            <select class="voyager-input w-full" :multiple="options.multiple || false">
                <option v-for="option in options.options" :value="option.key" :key="option.key">
                    {{ translate(option.value) }}
                </option>
            </select>
            <p class="description" v-if="translate(options.description) !== ''">
                {{ translate(options.description) }}
            </p>
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