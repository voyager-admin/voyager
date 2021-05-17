<template>
    <div>
        <card :title="__('voyager::settings.settings')" icon="cog">
            <alert v-if="settings.length === 0" color="red" class="mb-2">
                <template #title>{{ __('voyager::settings.no_settings_title') }}</template>
                <p>{{ __('voyager::settings.no_settings') }}</p>
            </alert>
            <template #actions>
                <div class="flex space-x-1 items-center">
                    <input type="text" class="input small" @dblclick="query = ''" @keydown.esc="query = ''" v-model="query" :placeholder="__('voyager::settings.search_settings')">
                    <button class="button accent" @click="save">
                        <icon icon="refresh" class="mr-0 md:mr-1 animate-spin-reverse" :size="4" v-if="savingSettings" />
                        <span>{{ __('voyager::generic.save') }}</span>
                    </button>
                    <dropdown placement="bottom-end">
                        <div>
                            <div class="grid grid-cols-2">
                                <a v-for="formfield in filteredFormfields"
                                    :key="'formfield-'+formfield.type"
                                    href="#"
                                    @click.prevent="addSetting(formfield)"
                                    class="link rounded">
                                    {{ formfield.name }}
                                </a>
                            </div>
                            <div class="divider"></div>
                            <a
                                :href="route('voyager.plugins.index')+'/?type=formfield'"
                                target="_blank"
                                class="w-full text-center italic link">
                                {{ __('voyager::builder.formfields_more') }}
                            </a>
                        </div>
                        <template #opener>
                            <button class="button green">
                                <icon icon="plus" :size="4" />
                                <span>{{ __('voyager::settings.add_setting') }}</span>
                            </button>
                        </template>
                    </dropdown>
                    <button class="button green" @click="addGroup">
                        <icon icon="plus" :size="4" />
                        <span>{{ __('voyager::settings.add_group') }}</span>
                    </button>
                    <locale-picker :small="false" />
                </div>
            </template>
            <div class="w-full inline-flex space-x-1 mb-4">
                <badge
                    v-for="group in groups"
                    @click="setCurrentGroup(group)"
                    :icon="currentGroup == group ? 'x' : null"
                    :color="badgeColor(group)"
                >
                    {{ titleCase(group ? group : __('voyager::settings.no_group')) }} ({{ settingsInGroup(group).length }})
                </badge>
            </div>

            <draggable as="div" v-model="settings" handle=".dd-handle">
                <card
                    v-for="setting in filteredSettings"
                    :key="setting.uuid"
                    class="dd-source"
                    :title="translate(setting.name, false) || __('voyager::settings.no_name')"
                    v-show="setting.group == currentGroup"
                >
                    <template #actions>
                        <div class="flex flex-wrap space-x-1">
                            <language-input type="text" class="input small" v-model="setting.name" :placeholder="__('voyager::settings.name')" />

                            <input type="text" class="input small" v-model="setting.key" :placeholder="__('voyager::settings.key')" />

                            <select class="input small" v-model="setting.group">
                                <option v-for="group in groups" :value="group">
                                    {{ titleCase(group ? group : __('voyager::settings.no_group')) }}
                                </option>
                            </select>

                            <button class="button small" @click="generateKey(setting)" v-tooltip="__('voyager::settings.generate_key')">
                                <icon icon="finger-print" />
                            </button>

                            <slide-in :title="__('voyager::generic.options')">
                                <template #actions>
                                    <locale-picker />
                                </template>
                                <div v-if="getFormfieldByType(setting.type).can_be_translated">
                                    <label class="label mt-4">{{ __('voyager::generic.translatable') }}</label>
                                    <input type="checkbox" class="input" v-model="setting.translatable">
                                </div>
                                <component
                                    :is="getFormfieldByType(setting.type).builder_component"
                                    v-model:options="setting.options"
                                    :column="{}"
                                    action="view-options" />
                                <breadBuilderValidation v-model="setting.validation" />
                                <template #opener>
                                    <button class="button" v-tooltip="__('voyager::generic.options')">
                                        <icon icon="cog" />
                                    </button>
                                </template>
                            </slide-in>

                            <button class="button small dd-handle cursor-move" v-tooltip="__('voyager::generic.move')">
                                <icon icon="switch-vertical" />
                            </button>

                            <button class="button small red" @click="$emit('delete', key)" v-tooltip="__('voyager::generic.delete')">
                                <icon icon="trash" />
                            </button>
                        </div>
                    </template>

                    <alert v-if="getErrors(setting).length > 0" color="red" class="my-2">
                        <ul class="list-disc">
                            <li v-for="(error, i) in getErrors(setting)" :key="'error-'+i">
                                {{ error }}
                            </li>
                        </ul>
                    </alert>

                    <component
                        :is="getFormfieldByType(setting.type).component"
                        :model-value="data(setting, null)"
                        @update:model-value="data(setting, $event)"
                        :options="setting.options"
                        :column="{}"
                        action="edit"
                    />
                </card>
            </draggable>
            <h3 class="text-center" v-if="!groupHasSettings()">{{ __('voyager::settings.no_settings_in_group') }}</h3>
        </card>
        <collapsible v-if="jsonOutput" :title="__('voyager::builder.json_output')" closed>
            <textarea class="input w-full" rows="10" v-model="jsonSettings"></textarea>
        </collapsible>
    </div>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { v4 as uuidv4 } from 'uuid';

import BreadBuilderValidation from './Builder/ValidationForm';
import Draggable from './UI/Draggable';

export default {
    components: {
        BreadBuilderValidation,
        Draggable,
    },
    props: {
        input: {
            type: Array,
            required: true,
        },
        editMode: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {
            settings: this.input,
            savingSettings: false,
            currentGroup: null,
            query: '',
            errors: [],
            tempGroups: [], // Temporarily used when adding groups
        };
    },
    methods: {
        setCurrentGroup(group) {
            if (this.currentGroup !== group) {
                this.currentGroup = group;
            } else {
                this.currentGroup = null;
            }

            if (!this.currentGroup) {
                history.replaceState(null, null, ' ');
            } else {
                window.location.hash = this.currentGroup;
            }
        },
        save() {
            this.savingSettings = true;
            this.errors = [];

            axios.post(this.route('voyager.settings.store'), { settings: this.settings })
            .then((response) => {
                new this.$notification(this.__('voyager::settings.settings_saved')).color('green').timeout().show();
            })
            .catch((response) => {
                if (response.response.status == 422) {
                    this.errors = response.response.data;
                    new this.$notification(this.__('voyager::settings.validation_errors')).color('red').timeout().show();
                }
            })
            .then(() => {
                this.savingSettings = false;
            });
        },
        addSetting(formfield) {
            this.settings.push({
                type: JSON.parse(JSON.stringify(formfield.type)),
                group: this.currentGroup,
                key: null,
                name: null,
                value: null,
                info: '',
                translatable: false,
                options: {},
                validation: [],
            });
        },
        addGroup() {
            new this
            .$notification(this.__('voyager::settings.enter_group_name'))
            .prompt()
            .timeout()
            .show()
            .then((value) => {
                if (value && value !== '') {
                    let group = this.slugify(value, { lower: true, strict:true });
                    this.tempGroups.push(group);
                    this.currentGroup = group;
                }
            });
        },
        data(setting, value = null) {
            if (setting.translatable || false && setting.value && this.isString(setting.value)) {
                setting.value = this.get_translatable_object(setting.value);
            }
            if (value !== null) {
                if (setting.translatable || false) {
                    setting.value[usePage().props.value.locale] = value;
                } else {
                    setting.value = value;
                }

                $eventbus.emit('setting-updated', setting);
            }
            if (setting.translatable || false) {
                return this.translate(this.get_translatable_object(setting.value));
            }
            return setting.value;
        },
        getErrors(setting) {
            var key = setting.key;
            if (setting.group !== null) {
                key = setting.group+'.'+setting.key;
            }

            return this.errors[key] || [];
        },
        groupHasErrors(group) {
            let errors = false;
            this.settings.forEach((setting) => {
                if (setting.group == group && this.getErrors(setting).length > 0) {
                    errors = true;
                }
            });
            return errors;
        },
        groupHasSettings() {
            return this.filteredSettings.filter((setting) => {
                return setting.group == this.currentGroup;
            }).length > 0;
        },
        settingsInGroup(group) {
            return this.filteredSettings.filter((setting) => {
                return setting.group == group;
            });
        },
        generateKey(setting) {
            setting.key = this.slugify(this.translate(setting.name, false), { lower: true, strict: true });
        },
        badgeColor(group) {
            if (this.groupHasErrors(group)) {
                return 'red';
            } else if (this.currentGroup == group) {
                return 'green';
            } else if (this.settingsInGroup(group).length == 0) {
                return 'gray';
            }

            return 'accent';
        },
    },
    computed: {
        groups() {
            return [null, ...[...this.tempGroups, ...this.settings.map((setting) => {
                return setting.group;
            })].filter((group, key, self) => {
                return self.indexOf(group) === key && group;
            }).sort()];
        },
        filteredFormfields() {
            return this.$store.formfields.filter((formfield) => {
                return formfield.in_settings;
            });
        },
        filteredSettings() {
            return this.settings.filter((s) => {
                return this.translate(s.title, false).toLowerCase().includes(this.query) || (s.key || '').toLowerCase().includes(this.query);
            });
        },
        jsonSettings: {
            get() {
                return JSON.stringify(this.settings, null, 2);
            },
            set(value) {
                let s = this.settings;
                try {
                    s = JSON.parse(value);
                    this.settings = s;
                } catch (e) {}
            }
        },
        jsonOutput() {
            return usePage().props.value.json_output;
        }
    },
    created() {
        this.$watch(() => this.settings, (settings) => {
            settings.forEach((setting) => {
                if (!setting.hasOwnProperty('uuid')) {
                    setting.uuid = uuidv4();
                }
            });
        }, { immediate: true, deep: true});
    },
    mounted() {
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 's') {
                this.save();
                e.preventDefault();
            }
        });

        if (this.groups.includes(window.location.hash.substr(1))) {
            this.currentGroup = window.location.hash.substr(1);
        }
    }
};
</script>